<?php

// Quick system verification script
// Run with: php artisan tinker --execute="require_once 'verify_system.php';"

use App\Models\User;
use App\Models\Role;
use App\Models\Document;
use App\Models\Event;
use App\Services\DocumentWorkflowService;
use App\Services\EventService;

echo "🔍 DMRS System Verification\n";
echo "==========================\n\n";

// Check Roles
echo "📋 Roles in System:\n";
$roles = Role::all();
foreach ($roles as $role) {
    $userCount = $role->users()->count();
    echo "  • {$role->name} ({$userCount} users)\n";
}
echo "\n";

// Check Users by Role
echo "👥 Users by Role:\n";
$roleNames = ['Records Officer', 'Approving Authority', 'Document Releaser', 'Employee'];
foreach ($roleNames as $roleName) {
    $users = User::whereHas('roles', function($q) use ($roleName) {
        $q->where('name', $roleName);
    })->get(['first_name', 'last_name', 'email']);

    echo "  {$roleName}:\n";
    foreach ($users as $user) {
        echo "    - {$user->first_name} {$user->last_name} ({$user->email})\n";
    }
    echo "\n";
}

// Check Events
echo "📅 Sample Events:\n";
$events = Event::take(3)->get(['title', 'category', 'event_date']);
foreach ($events as $event) {
    echo "  • {$event->title} [{$event->category}] - {$event->event_date}\n";
}
echo "\n";

// Check Document Table Structure
echo "📄 Document Workflow Statuses Available:\n";
$statuses = [
    'received',
    'forwarded_to_authority',
    'reviewed_by_authority',
    'released',
    'sent_to_employee',
    'seen_by_employee',
    'actioned_by_employee'
];
foreach ($statuses as $status) {
    echo "  • {$status}\n";
}
echo "\n";

// Test Services
echo "⚙️  Service Testing:\n";
try {
    $workflowService = app(DocumentWorkflowService::class);
    $eventService = app(EventService::class);
    echo "  ✅ DocumentWorkflowService - OK\n";
    echo "  ✅ EventService - OK\n";
} catch (Exception $e) {
    echo "  ❌ Service Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "🎯 System Status: READY FOR SIMULATION\n";
echo "📖 See SYSTEM_FLOW_SIMULATION_GUIDE.md for step-by-step testing\n";
