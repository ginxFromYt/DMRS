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
     */
    public function uploadImage(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Get the uploaded file
            $image = $request->file('image');

            // Generate a unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

            // Store the image in the public disk under 'uploads' directory
            $imagePath = $image->storeAs('uploads', $filename, 'public');
            $fullImagePath = storage_path('app/public/' . $imagePath);

            // Process the document with ML model and OCR
            Log::info('Starting document processing for: ' . $filename);
            $processingResults = $this->documentProcessingService->processDocument(
                $fullImagePath,
                $request->title,
                $request->description
            );

            if ($processingResults['processing_status'] === 'error') {
                return back()
                    ->with('error', 'Document uploaded but processing failed: ' . $processingResults['error_message'])
                    ->withInput();
            }

            // Here you could save the processing results to database
            // Create document using the workflow service
            $document = $this->documentWorkflowService->createDocument([
                'title' => $request->title,
                'description' => $request->description,
                'filename' => $filename,
                'file_path' => $imagePath,
                'uploaded_by' => auth()->id(),
                'extracted_text' => $processingResults['extracted_text'],
                'detected_objects' => $processingResults['detected_objects'],
                'document_numbers' => $processingResults['document_numbers'],
            ]);

            // Prepare success message with processing results
            $message = "Document uploaded and processed successfully!\n";
            $message .= "File: {$filename}\n";
            $message .= "Document ID: {$document->id}\n";

            if (!empty($processingResults['document_numbers'])) {
                $message .= "Found document numbers: " . implode(', ', $processingResults['document_numbers']) . "\n";
            }

            if (!empty($processingResults['detected_objects'])) {
                $objectTypes = array_column($processingResults['detected_objects'], 'class');
                $message .= "Detected objects: " . implode(', ', $objectTypes) . "\n";
            }

            if (!empty($processingResults['extracted_text'])) {
                $textPreview = substr($processingResults['extracted_text'], 0, 100);
                $message .= "Text preview: " . $textPreview . "...";
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
