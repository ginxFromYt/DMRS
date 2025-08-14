<?php

echo "🔍 Validating Google Cloud Vision API Setup...\n\n";

// Test 1: Check if key file exists
$keyPath = __DIR__ . '/storage/credentials/dmrs-466007-93ab73db28f0.json';
echo "📁 Checking key file: " . basename($keyPath) . "\n";

if (!file_exists($keyPath)) {
    echo "❌ Key file not found!\n";
    exit(1);
}

echo "✅ Key file exists\n";
echo "📊 File size: " . filesize($keyPath) . " bytes\n\n";

// Test 2: Validate JSON structure
echo "🔍 Validating JSON structure...\n";
$keyContent = file_get_contents($keyPath);
$keyData = json_decode($keyContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "❌ Invalid JSON in key file: " . json_last_error_msg() . "\n";
    exit(1);
}

echo "✅ Valid JSON format\n";

// Test 3: Check required fields
echo "🔍 Checking required fields...\n";
$requiredFields = ['type', 'project_id', 'private_key', 'client_email', 'private_key_id'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (!isset($keyData[$field]) || empty($keyData[$field])) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    echo "❌ Missing required fields: " . implode(', ', $missingFields) . "\n";
    exit(1);
}

echo "✅ All required fields present\n";

// Test 4: Display key information
echo "\n📋 Key Information:\n";
echo "   • Type: " . $keyData['type'] . "\n";
echo "   • Project ID: " . $keyData['project_id'] . "\n";
echo "   • Service Account: " . $keyData['client_email'] . "\n";
echo "   • Key ID: " . substr($keyData['private_key_id'], 0, 8) . "...\n";

// Test 5: Check .env configuration
echo "\n🔍 Checking .env configuration...\n";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);

    if (strpos($envContent, 'GOOGLE_CLOUD_PROJECT_ID=dmrs-466007') !== false) {
        echo "✅ Project ID configured in .env\n";
    } else {
        echo "⚠️  Project ID not found in .env\n";
    }

    if (strpos($envContent, 'dmrs-466007-93ab73db28f0.json') !== false) {
        echo "✅ Key file path configured in .env\n";
    } else {
        echo "⚠️  Key file path not found in .env\n";
    }
} else {
    echo "⚠️  .env file not found\n";
}

// Test 6: Check if Google Cloud Vision package is available
echo "\n🔍 Checking Google Cloud Vision package...\n";
require_once __DIR__ . '/vendor/autoload.php';

if (class_exists('Google\Cloud\Vision\V1\ImageAnnotatorClient')) {
    echo "✅ Google Cloud Vision package is loaded\n";

    try {
        // Try to create client (this validates credentials)
        $client = new Google\Cloud\Vision\V1\ImageAnnotatorClient([
            'keyFilePath' => $keyPath,
        ]);
        echo "✅ Vision API client created successfully!\n";
        $client->close();
    } catch (Exception $e) {
        echo "❌ Error creating Vision API client: " . $e->getMessage() . "\n";
        echo "💡 This might indicate permissions or API enablement issues\n";
    }
} else {
    echo "❌ Google Cloud Vision package not found\n";
}

echo "\n🎉 Validation Complete!\n";
echo "💡 If all tests passed, your Google Cloud Vision API is ready to use.\n";
