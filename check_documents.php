<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\Document;

echo "=== Current Documents Status ===\n\n";

$documents = Document::with(['uploader:id,name', 'currentHandler:id,name'])->get();

if ($documents->count() > 0) {
    foreach ($documents as $document) {
        echo "ID: {$document->id}\n";
        echo "Title: {$document->title}\n";
        echo "Status: {$document->status}\n";
        echo "Uploaded by: " . ($document->uploader->name ?? 'N/A') . "\n";
        echo "Current handler: " . ($document->currentHandler->name ?? 'N/A') . "\n";
        echo "Review decision: " . ($document->review_decision ?? 'N/A') . "\n";
        echo "Created: {$document->created_at}\n";
        echo "---\n";
    }
} else {
    echo "No documents found in the system.\n";
}

echo "\nTotal documents: " . $documents->count() . "\n";
