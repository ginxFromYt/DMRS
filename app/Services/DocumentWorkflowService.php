<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocumentWorkflowService
{
    /**
     * Create a new document in the system
     */
    public function createDocument(array $data): Document
    {
        return DB::transaction(function () use ($data) {
            $document = Document::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'filename' => $data['filename'],
                'file_path' => $data['file_path'],
                'status' => Document::STATUS_RECEIVED,
                'uploaded_by' => $data['uploaded_by'],
                'extracted_text' => $data['extracted_text'] ?? null,
                'detected_objects' => $data['detected_objects'] ?? null,
                'document_numbers' => $data['document_numbers'] ?? null,
                'received_at' => now(),
            ]);

            // Set current handler to Records Officer
            $recordsOfficer = User::whereHas('roles', function ($query) {
                $query->where('name', 'Records Officer');
            })->first();

            if ($recordsOfficer) {
                $document->current_handler = $recordsOfficer->id;
                $document->save();

                // Send notification to Records Officer
                $this->createNotification(
                    $recordsOfficer->id,
                    $document->id,
                    Notification::TYPE_DOCUMENT_RECEIVED,
                    'New Document Received',
                    "A new document '{$document->title}' has been received and requires processing."
                );
            }

            Log::info("Document created: {$document->title} (ID: {$document->id})");

            return $document;
        });
    }

    /**
     * Forward document to approving authority
     */
    public function forwardToAuthority(Document $document, User $user): bool
    {
        if (!$user->hasRole('Records Officer')) {
            throw new \Exception('Only Records Officers can forward documents to authority.');
        }

        if ($document->status !== Document::STATUS_RECEIVED) {
            throw new \Exception('Document can only be forwarded from received status.');
        }

        return DB::transaction(function () use ($document) {
            $document->updateStatus(Document::STATUS_FORWARDED_TO_AUTHORITY);

            // Set current handler to Approving Authority
            $approvingAuthority = User::whereHas('roles', function ($query) {
                $query->where('name', 'Approving Authority');
            })->first();

            if ($approvingAuthority) {
                $document->current_handler = $approvingAuthority->id;
                $document->save();

                // Send notification to Approving Authority
                $this->createNotification(
                    $approvingAuthority->id,
                    $document->id,
                    Notification::TYPE_DOCUMENT_FORWARDED,
                    'Document Forwarded for Review',
                    "Document '{$document->title}' has been forwarded to you for review and approval."
                );
            }

            Log::info("Document forwarded to authority: {$document->title} (ID: {$document->id})");

            return true;
        });
    }

    /**
     * Review document by approving authority (Sir Odz)
     * Sir Odz reviews and approves, then forwards to Document Releaser
     */
    public function reviewByAuthority(Document $document, User $user, string $action = 'approve', ?string $notes = null): bool
    {
        if (!$user->hasRole('Approving Authority')) {
            throw new \Exception('Only Approving Authority can review documents.');
        }

        if ($document->status !== Document::STATUS_FORWARDED_TO_AUTHORITY) {
            throw new \Exception('Document must be forwarded to authority before review.');
        }

        return DB::transaction(function () use ($document, $action, $notes) {
            if ($action === 'approve') {
                // Sir Odz approves and forwards to Document Releaser
                $document->updateStatus(Document::STATUS_REVIEWED_BY_AUTHORITY, $notes);
                $document->review_decision = 'approved';
                $document->save();

                // Forward to Document Releaser (Jasmin)
                $forwardResult = $this->forwardToReleaser($document);

                Log::info("Document approved by authority and forwarded to releaser: {$document->title} (ID: {$document->id})");

                return $forwardResult;
            } else {
                // Sir Odz rejects the document - send back to Records Officer
                $document->updateStatus(Document::STATUS_RECEIVED, $notes);
                $document->review_decision = 'rejected';

                // Set current handler back to Records Officer
                $recordsOfficer = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Records Officer');
                })->first();

                if ($recordsOfficer) {
                    $document->current_handler = $recordsOfficer->id;
                    $document->save();

                    // Send notification to Records Officer
                    $this->createNotification(
                        $recordsOfficer->id,
                        $document->id,
                        Notification::TYPE_DOCUMENT_REJECTED,
                        'Document Rejected',
                        "Document '{$document->title}' has been rejected and requires revision."
                    );
                }

                Log::info("Document rejected by authority: {$document->title} (ID: {$document->id})");
            }

            return true;
        });
    }

    /**
     * Forward document to Document Releaser (Jasmin)
     */
    public function forwardToReleaser(Document $document): bool
    {
        if ($document->status !== Document::STATUS_REVIEWED_BY_AUTHORITY) {
            throw new \Exception('Document must be reviewed and approved before forwarding to releaser.');
        }

        return DB::transaction(function () use ($document) {
            $document->updateStatus(Document::STATUS_FORWARDED_TO_RELEASER);

            // Set current handler to Document Releaser
            $documentReleaser = User::whereHas('roles', function ($query) {
                $query->where('name', 'Document Releaser');
            })->first();

            if ($documentReleaser) {
                $document->current_handler = $documentReleaser->id;
                $document->save();

                // Send notification to Document Releaser
                $this->createNotification(
                    $documentReleaser->id,
                    $document->id,
                    Notification::TYPE_DOCUMENT_FORWARDED,
                    'Document Ready for Release',
                    "Document '{$document->title}' has been approved and is ready for release to employee."
                );
            }

            Log::info("Document forwarded to releaser: {$document->title} (ID: {$document->id})");

            return true;
        });
    }

    /**
     * Release document (handled by Document Releaser - Jasmin)
     */
    public function releaseDocument(Document $document, User $user): bool
    {
        if (!$user->hasRole('Document Releaser')) {
            throw new \Exception('Only Document Releaser can release documents.');
        }

        if ($document->status !== Document::STATUS_FORWARDED_TO_RELEASER) {
            throw new \Exception('Document must be forwarded to releaser before release.');
        }

        return DB::transaction(function () use ($document) {
            $document->updateStatus(Document::STATUS_RELEASED);

            Log::info("Document released: {$document->title} (ID: {$document->id})");

            return true;
        });
    }

    /**
     * Send document to employee (now handled by Document Releaser - Jasmin)
     */
    public function sendToEmployee(Document $document, User $employee, User $user): bool
    {
        if (!$user->hasRole('Document Releaser')) {
            throw new \Exception('Only Document Releaser can send documents to employees.');
        }

        if ($document->status !== Document::STATUS_RELEASED) {
            throw new \Exception('Document must be released before sending to employee.');
        }

        return DB::transaction(function () use ($document, $employee) {
            $document->updateStatus(Document::STATUS_SENT_TO_EMPLOYEE);
            $document->assigned_to = $employee->id;
            $document->current_handler = $employee->id;
            $document->save();

            // Send notification to employee
            $this->createNotification(
                $employee->id,
                $document->id,
                Notification::TYPE_DOCUMENT_ASSIGNED,
                'Document Assigned',
                "Document '{$document->title}' has been assigned to you."
            );

            Log::info("Document sent to employee: {$document->title} (ID: {$document->id}) -> {$employee->email}");

            return true;
        });
    }

    /**
     * Mark document as seen by employee
     */
    public function markAsSeen(Document $document, User $user): bool
    {
        if ($document->assigned_to !== $user->id) {
            throw new \Exception('You can only mark documents assigned to you as seen.');
        }

        if ($document->status !== Document::STATUS_SENT_TO_EMPLOYEE) {
            throw new \Exception('Document must be sent to employee before marking as seen.');
        }

        return DB::transaction(function () use ($document) {
            $document->updateStatus(Document::STATUS_SEEN_BY_EMPLOYEE);

            Log::info("Document marked as seen: {$document->title} (ID: {$document->id})");

            return true;
        });
    }

    /**
     * Mark document as actioned by employee
     */
    public function markAsActioned(Document $document, User $user): bool
    {
        if ($document->assigned_to !== $user->id) {
            throw new \Exception('You can only mark documents assigned to you as actioned.');
        }

        if (!in_array($document->status, [Document::STATUS_SENT_TO_EMPLOYEE, Document::STATUS_SEEN_BY_EMPLOYEE])) {
            throw new \Exception('Document must be sent to employee before marking as actioned.');
        }

        return DB::transaction(function () use ($document) {
            $document->updateStatus(Document::STATUS_ACTIONED_BY_EMPLOYEE);

            Log::info("Document marked as actioned: {$document->title} (ID: {$document->id})");

            return true;
        });
    }

    /**
     * Create a notification
     */
    private function createNotification(int $userId, int $documentId, string $type, string $title, string $message): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'document_id' => $documentId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);
    }

    /**
     * Get documents for a specific role
     */
    public function getDocumentsForRole(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $userRole = $user->getPrimaryRole()?->name;

        switch ($userRole) {
            case 'Records Officer':
                // Records Officer sees documents that are received (need to be forwarded)
                return Document::with(['user', 'uploader', 'assignedUser', 'currentHandler'])
                    ->where('status', Document::STATUS_RECEIVED)
                    ->orderBy('created_at', 'desc')
                    ->get();

            case 'Approving Authority':
                // Sir Odz sees documents forwarded to him for review
                return Document::with(['user', 'uploader', 'assignedUser', 'currentHandler'])
                    ->where('status', Document::STATUS_FORWARDED_TO_AUTHORITY)
                    ->orderBy('forwarded_at', 'desc')
                    ->get();

            case 'Document Releaser':
                // Jasmin sees approved documents ready for release and released documents for sending
                return Document::with(['user', 'uploader', 'assignedUser', 'currentHandler'])
                    ->whereIn('status', [
                        Document::STATUS_FORWARDED_TO_RELEASER,
                        Document::STATUS_RELEASED
                    ])
                    ->orderBy('updated_at', 'desc')
                    ->get();

            case 'Employee':
                // Employees see documents assigned to them
                return Document::with(['user', 'uploader', 'assignedUser', 'currentHandler'])
                    ->where('assigned_to', $user->id)
                    ->orderBy('sent_at', 'desc')
                    ->get();

            case 'Event Manager':
                // Event Manager might see all documents for reporting or just their own events
                return Document::with(['user', 'uploader', 'assignedUser', 'currentHandler'])
                    ->orderBy('created_at', 'desc')
                    ->take(20)
                    ->get();

            default:
                return Document::with(['user', 'uploader', 'assignedUser', 'currentHandler'])
                    ->orderBy('created_at', 'desc')
                    ->get();
        }
    }

    /**
     * Get dashboard statistics for a user
     */
    public function getDashboardStats(User $user): array
    {
        $userRole = $user->getPrimaryRole()?->name;

        switch ($userRole) {
            case 'Records Officer':
                return [
                    'pending_documents' => Document::where('status', Document::STATUS_RECEIVED)->count(),
                    'total_received' => Document::count(),
                    'forwarded_today' => Document::where('status', Document::STATUS_FORWARDED_TO_AUTHORITY)
                        ->whereDate('forwarded_at', now())->count(),
                    'recent_uploads' => Document::orderBy('created_at', 'desc')->take(5)->get()
                ];

            case 'Approving Authority':
                return [
                    'pending_review' => Document::where('status', Document::STATUS_FORWARDED_TO_AUTHORITY)->count(),
                    'approved_documents' => Document::where('status', Document::STATUS_REVIEWED_BY_AUTHORITY)
                        ->where('review_decision', 'approved')->count(),
                    'rejected_documents' => Document::where('review_decision', 'rejected')->count(),
                    'total_reviews' => Document::whereNotNull('reviewed_at')->count(),
                    'recent_reviews' => Document::whereNotNull('reviewed_at')
                        ->orderBy('reviewed_at', 'desc')->take(5)->get()
                ];

            case 'Document Releaser':
                return [
                    'pending_release' => Document::where('status', Document::STATUS_FORWARDED_TO_RELEASER)->count(),
                    'released_documents' => Document::where('status', Document::STATUS_RELEASED)->count(),
                    'sent_today' => Document::where('status', Document::STATUS_SENT_TO_EMPLOYEE)
                        ->whereDate('sent_at', now())->count(),
                    'total_processed' => Document::whereIn('status', [
                        Document::STATUS_RELEASED,
                        Document::STATUS_SENT_TO_EMPLOYEE,
                        Document::STATUS_SEEN_BY_EMPLOYEE,
                        Document::STATUS_ACTIONED_BY_EMPLOYEE
                    ])->count(),
                    'recent_releases' => Document::whereNotNull('released_at')
                        ->orderBy('released_at', 'desc')->take(5)->get()
                ];

            case 'Employee':
                return [
                    'assigned_documents' => Document::where('assigned_to', $user->id)
                        ->where('status', Document::STATUS_SENT_TO_EMPLOYEE)->count(),
                    'completed_tasks' => Document::where('assigned_to', $user->id)
                        ->where('status', Document::STATUS_ACTIONED_BY_EMPLOYEE)->count(),
                    'seen_documents' => Document::where('assigned_to', $user->id)
                        ->where('status', Document::STATUS_SEEN_BY_EMPLOYEE)->count(),
                    'pending_action' => Document::where('assigned_to', $user->id)
                        ->whereIn('status', [Document::STATUS_SENT_TO_EMPLOYEE, Document::STATUS_SEEN_BY_EMPLOYEE])
                        ->count(),
                    'recent_assignments' => Document::where('assigned_to', $user->id)
                        ->orderBy('sent_at', 'desc')->take(5)->get()
                ];

            case 'Event Manager':
                return [
                    'total_events' => \App\Models\Event::count(),
                    'upcoming_events' => \App\Models\Event::where('event_date', '>', now())->count(),
                    'today_events' => \App\Models\Event::whereDate('event_date', now())->count(),
                    'total_documents' => Document::count(),
                    'recent_activity' => Document::orderBy('updated_at', 'desc')->take(5)->get()
                ];

            default:
                return [
                    'total_documents' => Document::count(),
                    'active_workflows' => Document::whereNotIn('status', [
                        Document::STATUS_COMPLETED,
                        Document::STATUS_REJECTED
                    ])->count(),
                    'completed_today' => Document::where('status', Document::STATUS_ACTIONED_BY_EMPLOYEE)
                        ->whereDate('updated_at', now())->count(),
                    'recent_activity' => Document::orderBy('updated_at', 'desc')->take(5)->get()
                ];
        }
    }
}
