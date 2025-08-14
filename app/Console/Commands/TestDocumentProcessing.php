<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DocumentProcessingService;

class TestDocumentProcessing extends Command
{
    protected $signature = 'test:document-processing {image_path?}';
    protected $description = 'Test document processing with Google Cloud Vision and ONNX';

    public function handle()
    {
        $this->info('🔍 Testing Document Processing Service...');

        try {
            // Test service initialization
            $service = new DocumentProcessingService();
            $this->info('✅ DocumentProcessingService initialized successfully');

            // Test with a sample image path (even if file doesn't exist)
            $imagePath = $this->argument('image_path') ?? storage_path('app/public/uploads/test.jpg');

            $this->info("📁 Testing with path: " . basename($imagePath));

            // This will test the service methods
            $results = $service->processDocument($imagePath, 'Test Document', 'Test processing');

            $this->info('📋 Processing Results:');
            $this->line('   Status: ' . $results['processing_status']);

            if ($results['processing_status'] === 'success') {
                $this->info('   ✅ Document processed successfully');
                $this->line('   📄 Extracted Text Length: ' . strlen($results['extracted_text']));
                $this->line('   🎯 Detected Objects: ' . count($results['detected_objects']));
                $this->line('   📋 Document Numbers: ' . count($results['document_numbers']));

                if (!empty($results['document_numbers'])) {
                    $this->line('   📄 Found Numbers: ' . implode(', ', $results['document_numbers']));
                }
            } else {
                $this->error('   ❌ Processing failed: ' . $results['error_message']);
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('📍 File: ' . $e->getFile() . ':' . $e->getLine());
        }
    }
}
