<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Document;

echo "=== Testing Document Show Views ===\n\n";

// Get test users
$recordsOfficer = User::where('email', 'records.officer@dmrs.com')->first();
$ceoAuthority = User::where('email', 'ceo.odz@dmrs.com')->first();
$employee = User::where('email', 'robert.wilson@dmrs.com')->first();

// Get test documents
$documents = Document::all();

echo "Available Documents:\n";
foreach ($documents as $doc) {
    echo "- ID: {$doc->id}, Title: {$doc->title}, Status: {$doc->status}\n";
}

echo "\n=== Testing Route Generation ===\n";

// Test route generation for document show
foreach ($documents as $doc) {
    echo "Document {$doc->id} ({$doc->title}):\n";
    echo "  Show URL: /documents/{$doc->id}\n";
    echo "  Status: {$doc->status}\n";
    echo "  Current Handler: " . ($doc->currentHandler ? $doc->currentHandler->name : 'None') . "\n";
    echo "  Assigned To: " . ($doc->assignedUser ? $doc->assignedUser->name : 'None') . "\n";
    echo "\n";
}

echo "=== Test Complete ===\n";
echo "\nTo test the document show views:\n";
echo "1. Visit: http://127.0.0.1:8000\n";
echo "2. Login with:\n";
echo "   - CEO: ceo.odz@dmrs.com / password123\n";
echo "   - Records Officer: records.officer@dmrs.com / password123\n";
echo "   - Employee: robert.wilson@dmrs.com / password123\n";
echo "3. Click 'View Full Document' on any document\n";
