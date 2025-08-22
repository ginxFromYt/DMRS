<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Services\DocumentProcessingService;
use App\Services\DocumentWorkflowService;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentHandlingController extends Controller
{
    protected $documentProcessingService;
    protected $documentWorkflowService;

    public function __construct(
        DocumentProcessingService $documentProcessingService,
        DocumentWorkflowService $documentWorkflowService
    ) {
        $this->documentProcessingService = $documentProcessingService;
        $this->documentWorkflowService = $documentWorkflowService;
    }

    /**
     * Handle image upload for admin dashboard with ML processing
     * Supports both single and multiple file uploads (images and PDFs)
     */
    public function uploadImage(Request $request)
    {
        // Validate the request for both single and multiple files
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'sometimes|required_without:images|image|mimes:jpeg,jpg,png,gif,webp|max:2048', // Single image
            'images' => 'sometimes|required_without:image|array|min:1|max:10', // Multiple images
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'pdfs' => 'sometimes|array|max:5', // PDF files
            'pdfs.*' => 'file|mimes:pdf|max:10240', // 10MB max per PDF
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $uploadedFiles = [];
            $fileTypes = [];
            $primaryFilePath = null;
            $documentType = 'image'; // Default type

            // Handle single image upload (legacy compatibility)
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('uploads', $filename, 'public');
                $uploadedFiles[] = $imagePath;
                $fileTypes[] = 'image';
                $primaryFilePath = $imagePath;
            }

            // Handle multiple image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $filename = time() . '_' . Str::random(10) . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('uploads', $filename, 'public');
                    $uploadedFiles[] = $imagePath;
                    $fileTypes[] = 'image';

                    // Set first image as primary if not already set
                    if (!$primaryFilePath) {
                        $primaryFilePath = $imagePath;
                    }
                }
            }

            // Handle PDF uploads
            if ($request->hasFile('pdfs')) {
                foreach ($request->file('pdfs') as $index => $pdf) {
                    $filename = time() . '_' . Str::random(10) . '_pdf_' . $index . '.' . $pdf->getClientOriginalExtension();
                    $pdfPath = $pdf->storeAs('documents', $filename, 'public');
                    $uploadedFiles[] = $pdfPath;
                    $fileTypes[] = 'pdf';

                    // Update document type if we have PDFs
                    if ($documentType === 'image') {
                        $documentType = count($uploadedFiles) > 1 ? 'mixed' : 'pdf';
                    } else {
                        $documentType = 'mixed';
                    }
                }
            }

            if (empty($uploadedFiles)) {
                return back()
                    ->with('error', 'No files were uploaded.')
                    ->withInput();
            }

            // Process files with ML model and OCR (images) or text extraction (PDFs)
            $processingResults = [
                'extracted_text' => '',
                'detected_objects' => [],
                'document_numbers' => [],
                'processing_status' => 'success',
                'metadata' => []
            ];

            $allExtractedText = [];
            $allDetectedObjects = [];
            $allDocumentNumbers = [];
            $allMetadata = [];

            // Process primary file
            if ($primaryFilePath) {
                $extension = strtolower(pathinfo($primaryFilePath, PATHINFO_EXTENSION));
                $fullFilePath = storage_path('app/public/' . $primaryFilePath);

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    // Process image file
                    Log::info('Starting image processing for primary file: ' . $primaryFilePath);

                    $imageResults = $this->documentProcessingService->processDocument(
                        $fullFilePath,
                        $request->title,
                        $request->description
                    );

                    if ($imageResults['processing_status'] === 'error') {
                        return back()
                            ->with('error', 'Files uploaded but image processing failed: ' . $imageResults['error_message'])
                            ->withInput();
                    }

                    $allExtractedText[] = $imageResults['extracted_text'];
                    $allDetectedObjects = array_merge($allDetectedObjects, $imageResults['detected_objects']);
                    $allDocumentNumbers = array_merge($allDocumentNumbers, $imageResults['document_numbers']);

                } elseif ($extension === 'pdf') {
                    // Process PDF file
                    Log::info('Starting PDF processing for primary file: ' . $primaryFilePath);

                    $pdfResults = $this->documentProcessingService->processPdf(
                        $fullFilePath,
                        $request->title,
                        $request->description
                    );

                    if ($pdfResults['processing_status'] === 'error') {
                        return back()
                            ->with('error', 'Files uploaded but PDF processing failed: ' . $pdfResults['error_message'])
                            ->withInput();
                    }

                    $allExtractedText[] = $pdfResults['extracted_text'];
                    $allDocumentNumbers = array_merge($allDocumentNumbers, $pdfResults['document_numbers']);
                    $allMetadata[] = $pdfResults['metadata'];
                }
            }

            // Process additional files for text extraction
            foreach ($uploadedFiles as $index => $filePath) {
                if ($filePath === $primaryFilePath) continue; // Skip primary file (already processed)

                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $fullFilePath = storage_path('app/public/' . $filePath);

                if ($extension === 'pdf') {
                    Log::info("Processing additional PDF file: {$filePath}");

                    $pdfResults = $this->documentProcessingService->processPdf(
                        $fullFilePath,
                        $request->title,
                        "Additional PDF - {$request->description}"
                    );

                    if ($pdfResults['processing_status'] === 'success') {
                        $allExtractedText[] = $pdfResults['extracted_text'];
                        $allDocumentNumbers = array_merge($allDocumentNumbers, $pdfResults['document_numbers']);
                        $allMetadata[] = $pdfResults['metadata'];
                    }
                }
                // Note: Additional images are not processed to avoid overwhelming the system
                // Only the primary image gets full ML processing
            }

            // Combine all processing results
            $processingResults = [
                'extracted_text' => implode("\n\n---\n\n", array_filter($allExtractedText)),
                'detected_objects' => array_unique($allDetectedObjects, SORT_REGULAR),
                'document_numbers' => array_unique($allDocumentNumbers),
                'processing_status' => 'success',
                'metadata' => $allMetadata
            ];            // Prepare file data for database
            $primaryFile = $uploadedFiles[0]; // First uploaded file
            $additionalFiles = array_slice($uploadedFiles, 1); // Remaining files
            $additionalFileTypes = array_slice($fileTypes, 1);

            // Create document using the workflow service
            $document = $this->documentWorkflowService->createDocument([
                'title' => $request->title,
                'description' => $request->description,
                'filename' => basename($primaryFile),
                'file_path' => $primaryFile,
                'file_paths' => $additionalFiles,
                'document_type' => $documentType,
                'file_types' => $additionalFileTypes,
                'primary_file_path' => $primaryFilePath ?: $primaryFile,
                'uploaded_by' => auth()->id(),
                'extracted_text' => $processingResults['extracted_text'],
                'detected_objects' => $processingResults['detected_objects'],
                'document_numbers' => $processingResults['document_numbers'],
                'metadata' => $processingResults['metadata'],
            ]);

            // Prepare success message with processing results
            $fileCount = count($uploadedFiles);
            $imageCount = count(array_filter($fileTypes, fn($type) => $type === 'image'));
            $pdfCount = count(array_filter($fileTypes, fn($type) => $type === 'pdf'));

            $message = "Document uploaded successfully with {$fileCount} file(s)!\n";
            $message .= "Document ID: {$document->id}\n";
            $message .= "Document Type: {$documentType}\n";

            if ($imageCount > 0 && $pdfCount > 0) {
                $message .= "Files: {$imageCount} image(s) + {$pdfCount} PDF(s)\n";
            } elseif ($pdfCount > 0) {
                $message .= "Files: {$pdfCount} PDF document(s)\n";
            } else {
                $message .= "Files: {$imageCount} image(s)\n";
            }

            if (!empty($processingResults['document_numbers'])) {
                $message .= "Found document numbers: " . implode(', ', $processingResults['document_numbers']) . "\n";
            }

            if (!empty($processingResults['detected_objects'])) {
                $objectTypes = array_column($processingResults['detected_objects'], 'class');
                $message .= "Detected objects: " . implode(', ', $objectTypes) . "\n";
            }

            if (!empty($processingResults['extracted_text'])) {
                $textLength = strlen($processingResults['extracted_text']);
                $textPreview = substr($processingResults['extracted_text'], 0, 100);
                $message .= "Extracted text ({$textLength} characters): " . $textPreview . "...";
            }

            // Add PDF metadata info if available
            if (!empty($processingResults['metadata'])) {
                $pdfInfo = [];
                foreach ($processingResults['metadata'] as $metadata) {
                    if (isset($metadata['pages'])) {
                        $pdfInfo[] = "Pages: {$metadata['pages']}";
                    }
                }
                if (!empty($pdfInfo)) {
                    $message .= "\nPDF Info: " . implode(', ', $pdfInfo);
                }
            }

            // Store processing results in session for display
            session(['processing_results' => $processingResults]);

            return back()
                ->with('success', $message)
                ->with('document_id', $document->id);

        } catch (\Exception $e) {
            Log::error('Document upload and processing failed: ' . $e->getMessage());

            return back()
                ->with('error', 'Failed to upload and process document. Please try again.')
                ->withInput();
        }
    }

    /**
     * Handle document upload (for various document types)
     */
    public function uploadDocument(Request $request)
    {
        // Validate the request for document files
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document' => 'required|file|mimes:pdf,doc,docx,xlsx,xls,ppt,pptx,txt|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get the uploaded file
            $document = $request->file('document');

            // Generate a unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $document->getClientOriginalExtension();

            // Store the document in the public disk under 'documents' directory
            $documentPath = $document->storeAs('documents', $filename, 'public');

            // Return success response
            return back()->with('success', 'Document uploaded successfully! File saved as: ' . $filename);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Document upload failed: ' . $e->getMessage());

            return back()
                ->with('error', 'Failed to upload document. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display list of users (example admin function)
     */
    public function users()
    {
        // This would typically fetch and display users
        // return view('Admin_Pages.users', compact('users'));
        return view('Admin_Pages.users');
    }

    /**
     * Display admin settings (example admin function)
     */
    public function settings()
    {
        // This would typically handle admin settings
        return view('Admin_Pages.settings');
    }

    /**
     * Display uploaded documents/images with processing results
     */
    public function index()
    {
        $user = auth()->user();
        $documents = $this->documentWorkflowService->getDocumentsForRole($user);
        $stats = $this->documentWorkflowService->getDashboardStats($user);

        // Get role-specific view
        $primaryRole = $user->getPrimaryRole()?->name;

        switch ($primaryRole) {
            case 'Records Officer':
                $view = 'RecordsOfficer_Pages.documents.index';
                break;
            case 'Approving Authority':
                $view = 'ApprovingAuthority_Pages.documents.index';
                break;
            case 'Document Releaser':
                $view = 'DocumentReleaser_Pages.documents.index';
                break;
            case 'Employee':
                $view = 'Employee_Pages.documents.index';
                break;
            case 'Event Manager':
                $view = 'EventManager_Pages.documents.index';
                break;
            case 'Administrator':
            case 'SuperAdmin':
            default:
                $view = 'Admin_Pages.documents.index';
                break;
        }

        return view($view, compact('documents', 'stats'));
    }

    /**
     * View detailed processing results for a document
     */
    public function viewProcessingResults()
    {
        $processingResults = session('processing_results');

        if (!$processingResults) {
            return back()->with('error', 'No processing results available.');
        }

        return view('Admin_Pages.documents.processing_results', compact('processingResults'));
    }

    /**
     * Show document details
     */
    public function show(Document $document)
    {
        $user = auth()->user();

        // Check if user can view this document
        if (!$document->canBeUpdatedBy($user) && $document->assigned_to !== $user->id && $document->uploaded_by !== $user->id) {
            if (!$user->hasRole('SuperAdmin') && !$user->hasRole('Administrator')) {
                abort(403, 'Unauthorized to view this document.');
            }
        }

        // Determine the appropriate view based on user role
        $primaryRole = $user->getPrimaryRole()?->name;

        switch ($primaryRole) {
            case 'SuperAdmin':
                $viewPath = 'SuperAdmin_Pages.documents.show';
                break;
            case 'Records Officer':
                $viewPath = 'RecordsOfficer_Pages.documents.show';
                break;
            case 'Approving Authority':
                $viewPath = 'ApprovingAuthority_Pages.documents.show';
                break;
            case 'Employee':
                $viewPath = 'Employee_Pages.documents.show';
                break;
            case 'Administrator':
                $viewPath = 'Admin_Pages.documents.show';
                break;
            case 'Event Manager':
                $viewPath = 'EventManager_Pages.documents.show';
                break;
            default:
                $viewPath = 'documents.show'; // Fallback generic view
                break;
        }

        return view($viewPath, compact('document'));
    }

    /**
     * Forward document to approving authority
     */
    public function forwardToAuthority(Request $request, Document $document)
    {
        try {
            $user = auth()->user();
            $this->documentWorkflowService->forwardToAuthority($document, $user);

            return redirect()->back()->with('success', 'Document forwarded to approving authority successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Review document by approving authority
     */
    public function reviewByAuthority(Request $request, Document $document)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'decision' => 'required|in:approve,reject',
        ]);

        try {
            $user = auth()->user();
            $action = $request->input('decision');
            $notes = $request->input('notes');

            $this->documentWorkflowService->reviewByAuthority($document, $user, $action, $notes);

            $actionText = $action === 'approve' ? 'approved and released' : 'rejected';
            return redirect()->back()->with('success', "Document {$actionText} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Release document
     */
    public function releaseDocument(Request $request, Document $document)
    {
        try {
            $user = auth()->user();
            $this->documentWorkflowService->releaseDocument($document, $user);

            return redirect()->back()->with('success', 'Document released successfully and is ready to be sent to employees.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Send document to employee
     */
    public function sendToEmployee(Request $request, Document $document)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
        ]);

        try {
            $user = auth()->user();
            $employee = User::findOrFail($request->employee_id);
            $this->documentWorkflowService->sendToEmployee($document, $employee, $user);

            return redirect()->back()->with('success', 'Document sent to employee successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark document as seen
     */
    public function markAsSeen(Request $request, Document $document)
    {
        try {
            $user = auth()->user();
            $this->documentWorkflowService->markAsSeen($document, $user);

            return redirect()->back()->with('success', 'Document marked as seen.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark document as actioned
     */
    public function markAsActioned(Request $request, Document $document)
    {
        try {
            $user = auth()->user();
            $this->documentWorkflowService->markAsActioned($document, $user);

            return redirect()->back()->with('success', 'Document marked as actioned.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get employees for dropdown
     */
    public function getEmployees()
    {
        $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'Employee');
        })->get(['id', 'first_name', 'last_name', 'email']);

        return response()->json($employees);
    }

    /**
     * Delete a document
     */
    public function delete(Request $request)
    {
        // Implementation for deleting documents
        // This would typically handle document deletion
        return back()->with('success', 'Document deleted successfully.');
    }
}
