<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DocumentProcessingService;

class DebugVision extends Command
{
    protected $signature = 'debug:vision';
    protected $description = 'Debug Google Cloud Vision configuration';

    public function handle()
    {
        $this->info('🔍 Debugging Google Cloud Vision Configuration');
        $this->line('');

        // Check environment variables
        $this->info('Environment Variables:');
        $this->line('GOOGLE_CLOUD_KEY_FILE: ' . env('GOOGLE_CLOUD_KEY_FILE'));
        $this->line('File exists: ' . (file_exists(env('GOOGLE_CLOUD_KEY_FILE')) ? 'Yes' : 'No'));
        $this->line('');

        // Test direct instantiation
        $this->info('Testing Direct Instantiation:');
        try {
            $service = new DocumentProcessingService();
            $this->line('✅ Service created successfully');

            // Use reflection to check the vision client
            $reflection = new \ReflectionClass($service);
            $property = $reflection->getProperty('visionClient');
            $property->setAccessible(true);
            $visionClient = $property->getValue($service);

            if ($visionClient) {
                $this->line('✅ Vision client initialized');
            } else {
                $this->line('❌ Vision client is null');
            }
        } catch (\Exception $e) {
            $this->line('❌ Error: ' . $e->getMessage());
        }

        $this->line('');

        // Test Laravel service container instantiation
        $this->info('Testing Laravel Service Container:');
        try {
            $service = app(DocumentProcessingService::class);
            $this->line('✅ Service resolved from container');

            // Use reflection to check the vision client
            $reflection = new \ReflectionClass($service);
            $property = $reflection->getProperty('visionClient');
            $property->setAccessible(true);
            $visionClient = $property->getValue($service);

            if ($visionClient) {
                $this->line('✅ Vision client initialized via container');
            } else {
                $this->line('❌ Vision client is null via container');
            }
        } catch (\Exception $e) {
            $this->line('❌ Error via container: ' . $e->getMessage());
        }
    }
}
