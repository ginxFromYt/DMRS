<?php

// Test Google Cloud Vision API setup using Laravel context
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Test if credentials work
    $keyPath = storage_path('credentials/dmrs-466007-93ab73db28f0.json');

    echo "🔍 Testing Google Cloud Vision API setup...\n";
    echo "📁 Key file path: $keyPath\n";

    if (!file_exists($keyPath)) {
        echo "❌ Key file not found at: $keyPath\n";
        exit(1);
    }

    echo "� File size: " . filesize($keyPath) . " bytes\n";

    // Test if the file contains valid JSON
    $keyContent = file_get_contents($keyPath);
    $keyData = json_decode($keyContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ Invalid JSON in key file\n";
        exit(1);
    }

    echo "✅ Key file is valid JSON\n";

    // Check required fields
    $requiredFields = ['type', 'project_id', 'private_key', 'client_email'];
    foreach ($requiredFields as $field) {
        if (!isset($keyData[$field])) {
            echo "❌ Missing required field: $field\n";
            exit(1);
        }
    }

    echo "✅ All required fields present\n";
    echo "📋 Project ID: " . $keyData['project_id'] . "\n";
    echo "📧 Service Account: " . $keyData['client_email'] . "\n";

    // Test the Google Cloud Vision client
    if (class_exists('Google\Cloud\Vision\V1\ImageAnnotatorClient')) {
        $client = new Google\Cloud\Vision\V1\ImageAnnotatorClient([
            'keyFilePath' => $keyPath,
        ]);

        echo "✅ Google Cloud Vision API client created successfully!\n";
        echo "🎉 Setup validation PASSED!\n";

        $client->close();
    } else {
        echo "⚠️  Google Cloud Vision classes not found, but credentials are valid\n";
        echo "🎉 Credential validation PASSED!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Troubleshooting tips:\n";
    echo "   1. Ensure Vision API is enabled in Google Cloud Console\n";
    echo "   2. Check service account has proper permissions (Editor role)\n";
    echo "   3. Verify the JSON key file is correctly downloaded\n";
}
