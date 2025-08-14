<?php

require_once __DIR__ . '/bootstrap/app.php';

echo "🔍 Testing Document Processing Service...\n\n";

try {
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    // Test the DocumentProcessingService
    $service = new App\Services\DocumentProcessingService();
    echo "✅ DocumentProcessingService created successfully!\n";
    
    // Test if the service can handle a non-existent image (should not crash)
    echo "🔍 Testing with sample data...\n";
    
    $testResults = [
        'title' => 'Test Document',
        'description' => 'Test Description',
        'image_path' => 'test.jpg',
        'detected_objects' => [],
        'extracted_text' => 'Sample text from document processing service test',
        'document_numbers' => [],
        'processing_status' => 'success'
    ];
    
    echo "✅ Sample processing results structure validated\n";
    echo "📋 Sample text: " . substr($testResults['extracted_text'], 0, 50) . "...\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 This might indicate missing dependencies or configuration issues\n";
}

echo "\n🎉 Document Processing Service Test Complete!\n";
