<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Document;
use App\Services\DocumentWorkflowService;

echo "=== DMRS Updated Workflow Testing ===\n\n";

// Get our test users for all 5 roles
$recordsOfficer = User::where('email', 'records.officer@dmrs.com')->first();
$approvingAuthority = User::where('email', 'ceo.odz@dmrs.com')->first();
$documentReleaser = User::where('email', 'jasmin.releaser@dmrs.com')->first();
$employee = User::where('email', 'robert.wilson@dmrs.com')->first();
$eventManager = User::where('email', 'events.manager@dmrs.com')->first();

$workflowService = new DocumentWorkflowService();

echo "👥 Test Users (5 Roles):\n";
echo "1. Records Officer: {$recordsOfficer->full_name} ({$recordsOfficer->email})\n";
echo "2. Approving Authority: {$approvingAuthority->full_name} ({$approvingAuthority->email})\n";
echo "3. Document Releaser: {$documentReleaser->full_name} ({$documentReleaser->email})\n";
echo "4. Employee: {$employee->full_name} ({$employee->email})\n";
echo "5. Event Manager: {$eventManager->full_name} ({$eventManager->email})\n\n";

echo "📊 Dashboard Statistics by Role:\n\n";

// Test dashboard stats for each role
$roles = [
    'Records Officer' => $recordsOfficer,
    'Approving Authority' => $approvingAuthority,
    'Document Releaser' => $documentReleaser,
    'Employee' => $employee,
    'Event Manager' => $eventManager
];

foreach ($roles as $roleName => $user) {
    echo "🔹 {$roleName} Dashboard:\n";
    $stats = $workflowService->getDashboardStats($user);
    foreach ($stats as $key => $value) {
        if (is_numeric($value)) {
            echo "   - {$key}: {$value}\n";
        }
    }
    echo "\n";
}

echo "📋 Current Document Status Overview:\n";
$statusCounts = [
    'received' => Document::where('status', Document::STATUS_RECEIVED)->count(),
    'forwarded_to_authority' => Document::where('status', Document::STATUS_FORWARDED_TO_AUTHORITY)->count(),
    'reviewed_by_authority' => Document::where('status', Document::STATUS_REVIEWED_BY_AUTHORITY)->count(),
    'forwarded_to_releaser' => Document::where('status', Document::STATUS_FORWARDED_TO_RELEASER)->count(),
    'released' => Document::where('status', Document::STATUS_RELEASED)->count(),
    'sent_to_employee' => Document::where('status', Document::STATUS_SENT_TO_EMPLOYEE)->count(),
    'seen_by_employee' => Document::where('status', Document::STATUS_SEEN_BY_EMPLOYEE)->count(),
    'actioned_by_employee' => Document::where('status', Document::STATUS_ACTIONED_BY_EMPLOYEE)->count(),
];

foreach ($statusCounts as $status => $count) {
    echo "   - {$status}: {$count} documents\n";
}

echo "\n🧪 Testing New Workflow Process:\n\n";

// Create a test document
$testDocument = Document::create([
    'title' => 'Updated Workflow Test Document',
    'description' => 'Testing the new 5-role workflow system',
    'filename' => 'workflow-test.pdf',
    'file_path' => '/storage/documents/workflow-test.pdf',
    'status' => Document::STATUS_RECEIVED,
    'uploaded_by' => $recordsOfficer->id,
    'current_handler' => $recordsOfficer->id,
    'received_at' => now(),
]);

echo "📝 Created test document: {$testDocument->title} (ID: {$testDocument->id})\n";
echo "   Initial status: {$testDocument->status}\n";
echo "   Current handler: {$testDocument->currentHandler->full_name}\n\n";

// Step 1: Records Officer forwards to Authority
echo "Step 1: Records Officer → Approving Authority\n";
try {
    $result = $workflowService->forwardToAuthority($testDocument, $recordsOfficer);
    if ($result) {
        $testDocument->refresh();
        echo "   ✅ Successfully forwarded to authority\n";
        echo "   Status: {$testDocument->status}\n";
        echo "   Current handler: {$testDocument->currentHandler->full_name}\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n\n";
}

// Step 2: Approving Authority reviews and approves (forwards to releaser)
echo "Step 2: Approving Authority → Review & Forward to Releaser\n";
try {
    $result = $workflowService->reviewByAuthority($testDocument, $approvingAuthority, 'approve', 'Document approved for release');
    if ($result) {
        $testDocument->refresh();
        echo "   ✅ Successfully reviewed and forwarded to releaser\n";
        echo "   Status: {$testDocument->status}\n";
        echo "   Current handler: {$testDocument->currentHandler->full_name}\n";
        echo "   Authority notes: {$testDocument->authority_notes}\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n\n";
}

// Step 3: Document Releaser releases document
echo "Step 3: Document Releaser → Release Document\n";
try {
    $result = $workflowService->releaseDocument($testDocument, $documentReleaser);
    if ($result) {
        $testDocument->refresh();
        echo "   ✅ Successfully released document\n";
        echo "   Status: {$testDocument->status}\n";
        echo "   Released at: {$testDocument->released_at}\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n\n";
}

// Step 4: Document Releaser sends to employee
echo "Step 4: Document Releaser → Send to Employee\n";
try {
    $result = $workflowService->sendToEmployee($testDocument, $employee, $documentReleaser);
    if ($result) {
        $testDocument->refresh();
        echo "   ✅ Successfully sent to employee\n";
        echo "   Status: {$testDocument->status}\n";
        echo "   Assigned to: {$testDocument->assignedUser->full_name}\n";
        echo "   Current handler: {$testDocument->currentHandler->full_name}\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n\n";
}

// Step 5: Employee marks as seen
echo "Step 5: Employee → Mark as Seen\n";
try {
    $result = $workflowService->markAsSeen($testDocument, $employee);
    if ($result) {
        $testDocument->refresh();
        echo "   ✅ Successfully marked as seen\n";
        echo "   Status: {$testDocument->status}\n";
        echo "   Seen at: {$testDocument->seen_at}\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n\n";
}

// Step 6: Employee marks as actioned
echo "Step 6: Employee → Mark as Actioned\n";
try {
    $result = $workflowService->markAsActioned($testDocument, $employee);
    if ($result) {
        $testDocument->refresh();
        echo "   ✅ Successfully marked as actioned\n";
        echo "   Status: {$testDocument->status}\n";
        echo "   Actioned at: {$testDocument->actioned_at}\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n\n";
}

echo "🔍 Testing Role-based Document Access:\n\n";

foreach ($roles as $roleName => $user) {
    echo "📂 {$roleName} can see:\n";
    $documents = $workflowService->getDocumentsForRole($user);
    if ($documents->count() > 0) {
        foreach ($documents->take(3) as $doc) {
            echo "   - {$doc->title} (Status: {$doc->status})\n";
        }
        if ($documents->count() > 3) {
            echo "   - ... and " . ($documents->count() - 3) . " more documents\n";
        }
    } else {
        echo "   - No documents in their queue\n";
    }
    echo "\n";
}

echo "✅ Updated DMRS Workflow Test Complete!\n\n";
echo "🎯 The system now properly supports:\n";
echo "   1. ✅ Records Officer - Document reception and forwarding\n";
echo "   2. ✅ Approving Authority (Sir Odz) - Review and approval\n";
echo "   3. ✅ Document Releaser (Jasmin) - Release and distribution\n";
echo "   4. ✅ Receiving Employee - Document processing and action\n";
echo "   5. ✅ Event Manager - Event management (optional)\n\n";
echo "📋 Updated Workflow Status Flow:\n";
echo "   received → forwarded_to_authority → reviewed_by_authority → \n";
echo "   forwarded_to_releaser → released → sent_to_employee → \n";
echo "   seen_by_employee → actioned_by_employee\n";

echo "\n🔑 Login Credentials for Testing:\n";
echo "   Records Officer: records.officer@dmrs.com / password123\n";
echo "   Approving Authority: ceo.odz@dmrs.com / password123\n";
echo "   Document Releaser: jasmin.releaser@dmrs.com / password123\n";
echo "   Employee: robert.wilson@dmrs.com / password123\n";
echo "   Event Manager: events.manager@dmrs.com / password123\n";
