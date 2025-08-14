<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DocumentProcessingService;
use Illuminate\Support\Facades\Log;

class ValidateSetup extends Command
{
    protected $signature = 'validate:setup';
    protected $description = 'Validate complete DMRS setup';

    public function handle()
    {
        $this->info('🔍 DMRS Complete Setup Validation');
        $this->line('');

        // 1. Test DocumentProcessingService
        $this->info('1. Testing DocumentProcessingService...');
        $service = new DocumentProcessingService();

        // Check if Google Cloud Vision is properly initialized
        $reflection = new \ReflectionClass($service);
        $visionProperty = $reflection->getProperty('visionClient');
        $visionProperty->setAccessible(true);
        $visionClient = $visionProperty->getValue($service);

        if ($visionClient) {
            $this->line('   ✅ Google Cloud Vision client initialized');
        } else {
            $this->line('   ❌ Google Cloud Vision not available');
        }

        // 2. Test with real image processing
        if (file_exists(storage_path('app/public/uploads/test.jpg'))) {
            $this->info('2. Testing with real image...');
            $results = $service->processDocument(storage_path('app/public/uploads/test.jpg'), 'test.jpg', 'jpg');

            $this->line('   📄 Processing Status: ' . $results['processing_status']);
            $this->line('   📄 Text Length: ' . strlen($results['extracted_text'] ?? ''));
            $this->line('   🎯 Objects Detected: ' . count($results['detected_objects'] ?? []));
            $this->line('   📋 Document Numbers: ' . count($results['document_numbers'] ?? []));

            // Check if we got real OCR vs simulation
            $textLength = strlen($results['extracted_text'] ?? '');
            if ($textLength > 0 && $textLength != 231) { // 231 is the simulation length
                $this->line('   ✅ Real Google Cloud Vision OCR detected');
            } else {
                $this->line('   ⚠️ Using fallback/simulation mode');
            }
        } else {
            $this->line('2. No test image found, skipping image processing test');
        }

        // 3. Check environment configuration
        $this->info('3. Environment Configuration:');
        $this->line('   GOOGLE_CLOUD_KEY_FILE: ' . (env('GOOGLE_CLOUD_KEY_FILE') ? '✅ Set' : '❌ Not set'));
        $this->line('   Key file exists: ' . (file_exists(env('GOOGLE_CLOUD_KEY_FILE')) ? '✅ Yes' : '❌ No'));
        $this->line('   Python executable: ' . (env('PYTHON_EXECUTABLE') ?: 'python'));
        $this->line('   ONNX model path: ' . env('ONNX_MODEL_PATH', 'models/document_detector.onnx'));

        // 4. Check routes and controllers
        $this->info('4. Application Structure:');
        $this->line('   DocumentHandlingController: ' . (file_exists(app_path('Http/Controllers/AdminControllers/DocumentHandlingController.php')) ? '✅ Present' : '❌ Missing'));
        $this->line('   DocumentProcessingService: ' . (file_exists(app_path('Services/DocumentProcessingService.php')) ? '✅ Present' : '❌ Missing'));
        $this->line('   ONNX Python script: ' . (file_exists(base_path('scripts/onnx_inference.py')) ? '✅ Present' : '❌ Missing'));
        $this->line('   Dashboard view: ' . (file_exists(resource_path('views/dashboard.blade.php')) ? '✅ Present' : '❌ Missing'));

        // 5. Check storage directories
        $this->info('5. Storage Setup:');
        $this->line('   Upload directory: ' . (is_dir(storage_path('app/public/uploads')) ? '✅ Ready' : '❌ Missing'));
        $this->line('   Credentials directory: ' . (is_dir(storage_path('credentials')) ? '✅ Ready' : '❌ Missing'));

        $this->line('');
        $this->info('🎉 Setup validation complete!');
        $this->line('');
        $this->line('Next steps:');
        $this->line('1. Login as admin (admin@dmrs.com) to test the dashboard');
        $this->line('2. Upload a document image to test ML processing');
        $this->line('3. Replace the ONNX model with your actual document detection model');
        $this->line('4. The system will automatically use real Google Cloud Vision OCR + your ONNX model');
    }
}
