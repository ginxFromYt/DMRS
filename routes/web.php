<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminControllers\DocumentHandlingController;
use App\Http\Controllers\EventController;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if (!$user) {
        return view('dashboard');
    }

    $documentWorkflowService = app(\App\Services\DocumentWorkflowService::class);
    $documents = $documentWorkflowService->getDocumentsForRole($user);
    $stats = $documentWorkflowService->getDashboardStats($user);

    $primaryRole = $user->getPrimaryRole()?->name;

    switch ($primaryRole) {
        case 'SuperAdmin':
            return view('SuperAdmin_Pages.dashboard', compact('documents', 'stats'));
        case 'Records Officer':
            return view('RecordsOfficer_Pages.dashboard', compact('documents', 'stats'));
        case 'Approving Authority':
            return view('ApprovingAuthority_Pages.dashboard', compact('documents', 'stats'));
        case 'Document Releaser':
            return view('DocumentReleaser_Pages.dashboard', compact('documents', 'stats'));
        case 'Employee':
            // For employees, we need different data sets
            $newDocuments = $documents->where('status', 'sent_to_employee');
            $seenDocuments = $documents->where('status', 'seen_by_employee');
            $actionedDocuments = $documents->where('status', 'actioned_by_employee');
            return view('Employee_Pages.dashboard', compact('documents', 'stats', 'newDocuments', 'seenDocuments', 'actionedDocuments'));
        case 'Administrator':
            return view('Admin_Pages.dashboard', compact('documents', 'stats'));
        case 'Event Manager':
            // For event managers, we need event data instead
            $eventService = app(\App\Services\EventService::class);
            $universityEvents = collect(); // Will be implemented
            $campusEvents = collect();
            $externalEvents = collect();
            $recentEvents = collect();
            return view('EventManager_Pages.dashboard', compact('documents', 'stats', 'universityEvents', 'campusEvents', 'externalEvents', 'recentEvents'));
        default:
            return view('dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Document Workflow Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Document routes accessible to multiple roles
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentHandlingController::class, 'index'])->name('index');
        Route::get('/{document}', [DocumentHandlingController::class, 'show'])->name('show');
        Route::get('/employees/list', [DocumentHandlingController::class, 'getEmployees'])->name('employees');

        // Document upload (Records Officer and admins)
        Route::post('/upload-image', [DocumentHandlingController::class, 'uploadImage'])
            ->middleware('role:Records Officer,Administrator,SuperAdmin')
            ->name('upload.image');
        Route::post('/upload-document', [DocumentHandlingController::class, 'uploadDocument'])
            ->middleware('role:Records Officer,Administrator,SuperAdmin')
            ->name('upload.document');

        // Workflow actions
        Route::post('/{document}/forward-authority', [DocumentHandlingController::class, 'forwardToAuthority'])
            ->middleware('role:Records Officer')
            ->name('forward.authority');
        Route::post('/{document}/review', [DocumentHandlingController::class, 'reviewByAuthority'])
            ->middleware('role:Approving Authority')
            ->name('review');
        Route::post('/{document}/release', [DocumentHandlingController::class, 'releaseDocument'])
            ->middleware('role:Document Releaser')
            ->name('release');
        Route::post('/{document}/send-employee', [DocumentHandlingController::class, 'sendToEmployee'])
            ->middleware('role:Document Releaser')
            ->name('send.employee');
        Route::post('/{document}/mark-seen', [DocumentHandlingController::class, 'markAsSeen'])
            ->middleware('role:Employee')
            ->name('mark.seen');
        Route::post('/{document}/mark-actioned', [DocumentHandlingController::class, 'markAsActioned'])
            ->middleware('role:Employee')
            ->name('mark.actioned');
    });

    // Event Management Routes
    Route::resource('events', EventController::class);
    Route::get('/events/category/{category}', [EventController::class, 'byCategory'])->name('events.category');
    Route::get('/deadlines/upcoming', [EventController::class, 'upcomingDeadlines'])->name('events.deadlines');
    Route::get('/api/events/homepage', [EventController::class, 'getHomepageEvents'])->name('api.events.homepage');
    Route::get('/api/events/search', [EventController::class, 'search'])->name('api.events.search');
});

// Legacy Admin Routes - Updated for new role system
Route::middleware(['auth', 'verified'])->group(function () {

    // Admin-only routes with role checking
    Route::prefix('admin')->name('admin.')->group(function () {

        // Processing results route
        Route::get('/processing-results', [DocumentHandlingController::class, 'viewProcessingResults'])
            ->middleware('role:Records Officer,Administrator,SuperAdmin')
            ->name('processing.results');

        // Additional admin routes
        Route::get('/users', [DocumentHandlingController::class, 'users'])
            ->middleware('role:Administrator,SuperAdmin')
            ->name('users');

        Route::get('/settings', [DocumentHandlingController::class, 'settings'])
            ->middleware('role:Administrator,SuperAdmin')
            ->name('settings');

        // Document deletion (admin only)
        Route::delete('/documents/delete', [DocumentHandlingController::class, 'delete'])
            ->middleware('role:Administrator,SuperAdmin')
            ->name('documents.delete');
    });
});

require __DIR__.'/auth.php';
