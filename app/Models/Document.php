<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'filename',
        'file_path',
        'file_paths',
        'document_type',
        'file_types',
        'primary_file_path',
        'status',
        'uploaded_by',
        'assigned_to',
        'current_handler',
        'authority_notes',
        'review_decision',
        'extracted_text',
        'detected_objects',
        'document_numbers',
        'metadata',
        'received_at',
        'forwarded_at',
        'reviewed_at',
        'forwarded_to_releaser_at',
        'released_at',
        'sent_at',
        'seen_at',
        'actioned_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'detected_objects' => 'array',
        'document_numbers' => 'array',
        'metadata' => 'array',
        'file_paths' => 'array',
        'file_types' => 'array',
        'received_at' => 'datetime',
        'forwarded_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'forwarded_to_releaser_at' => 'datetime',
        'released_at' => 'datetime',
        'sent_at' => 'datetime',
        'seen_at' => 'datetime',
        'actioned_at' => 'datetime',
    ];

    /**
     * Get the user who uploaded the document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Alias for uploader relationship for backward compatibility
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the user assigned to the document.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the current handler of the document.
     */
    public function currentHandler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_handler');
    }

    /**
     * Get the notifications for the document.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Alias for file_path to maintain compatibility
     */
    public function getImagePathAttribute()
    {
        // Return file_path if it's an image, null otherwise
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));

        return in_array($extension, $imageExtensions) ? $this->file_path : null;
    }

    /**
     * Get all image files from the document
     */
    public function getImageFilesAttribute()
    {
        $images = [];
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

        // Add primary file if it's an image
        if ($this->file_path) {
            $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
            if (in_array($extension, $imageExtensions)) {
                $images[] = $this->file_path;
            }
        }

        // Add additional image files
        if ($this->file_paths) {
            foreach ($this->file_paths as $index => $filePath) {
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if (in_array($extension, $imageExtensions)) {
                    $images[] = $filePath;
                }
            }
        }

        return array_unique($images);
    }

    /**
     * Get all PDF files from the document
     */
    public function getPdfFilesAttribute()
    {
        $pdfs = [];

        // Check primary file
        if ($this->file_path) {
            $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
            if ($extension === 'pdf') {
                $pdfs[] = $this->file_path;
            }
        }

        // Check additional files
        if ($this->file_paths) {
            foreach ($this->file_paths as $filePath) {
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if ($extension === 'pdf') {
                    $pdfs[] = $filePath;
                }
            }
        }

        return array_unique($pdfs);
    }

    /**
     * Get all files from the document
     */
    public function getAllFilesAttribute()
    {
        $files = [];

        // Add primary file
        if ($this->file_path) {
            $files[] = $this->file_path;
        }

        // Add additional files
        if ($this->file_paths) {
            $files = array_merge($files, $this->file_paths);
        }

        return array_unique($files);
    }

    /**
     * Check if document has multiple files
     */
    public function hasMultipleFiles()
    {
        return count($this->getAllFilesAttribute()) > 1;
    }

    /**
     * Check if document has PDF files
     */
    public function hasPdfFiles()
    {
        return count($this->getPdfFilesAttribute()) > 0;
    }

    /**
     * Get the primary display file (image or first file)
     */
    public function getPrimaryDisplayFileAttribute()
    {
        // Use primary_file_path if set
        if ($this->primary_file_path) {
            return $this->primary_file_path;
        }

        // Otherwise use the first image file
        $images = $this->getImageFilesAttribute();
        if (!empty($images)) {
            return $images[0];
        }

        // Fallback to the first available file
        $allFiles = $this->getAllFilesAttribute();
        return !empty($allFiles) ? $allFiles[0] : $this->file_path;
    }

    /**
     * Status constants
     */
    const STATUS_RECEIVED = 'received';
    const STATUS_FORWARDED_TO_AUTHORITY = 'forwarded_to_authority';
    const STATUS_REVIEWED_BY_AUTHORITY = 'reviewed_by_authority';
    const STATUS_FORWARDED_TO_RELEASER = 'forwarded_to_releaser';
    const STATUS_RELEASED = 'released';
    const STATUS_SENT_TO_EMPLOYEE = 'sent_to_employee';
    const STATUS_SEEN_BY_EMPLOYEE = 'seen_by_employee';
    const STATUS_ACTIONED_BY_EMPLOYEE = 'actioned_by_employee';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';

    /**
     * Update document status and set appropriate timestamp
     */
    public function updateStatus(string $status, ?string $notes = null): bool
    {
        $this->status = $status;

        switch ($status) {
            case self::STATUS_RECEIVED:
                $this->received_at = now();
                break;
            case self::STATUS_FORWARDED_TO_AUTHORITY:
                $this->forwarded_at = now();
                break;
            case self::STATUS_REVIEWED_BY_AUTHORITY:
                $this->reviewed_at = now();
                if ($notes) {
                    $this->authority_notes = $notes;
                }
                break;
            case self::STATUS_FORWARDED_TO_RELEASER:
                $this->forwarded_to_releaser_at = now();
                break;
            case self::STATUS_RELEASED:
                $this->released_at = now();
                break;
            case self::STATUS_SENT_TO_EMPLOYEE:
                $this->sent_at = now();
                break;
            case self::STATUS_SEEN_BY_EMPLOYEE:
                $this->seen_at = now();
                break;
            case self::STATUS_ACTIONED_BY_EMPLOYEE:
                $this->actioned_at = now();
                break;
        }

        return $this->save();
    }

    /**
     * Check if document can be updated by the current user
     */
    public function canBeUpdatedBy(User $user): bool
    {
        $userRole = $user->getPrimaryRole()?->name;

        switch ($this->status) {
            case self::STATUS_RECEIVED:
                return $user->hasRole('Records Officer');
            case self::STATUS_FORWARDED_TO_AUTHORITY:
                return $user->hasRole('Approving Authority');
            case self::STATUS_REVIEWED_BY_AUTHORITY:
                return $user->hasRole('Approving Authority');
            case self::STATUS_FORWARDED_TO_RELEASER:
                return $user->hasRole('Document Releaser');
            case self::STATUS_RELEASED:
                return $user->hasRole('Document Releaser');
            case self::STATUS_SENT_TO_EMPLOYEE:
                return $this->assigned_to === $user->id;
            default:
                return false;
        }
    }
}
