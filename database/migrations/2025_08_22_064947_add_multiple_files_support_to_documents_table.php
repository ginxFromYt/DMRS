<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Add support for multiple files and file types
            $table->json('file_paths')->nullable()->after('file_path'); // Store multiple file paths
            $table->string('document_type')->default('image')->after('filename'); // image, pdf, mixed
            $table->json('file_types')->nullable()->after('document_type'); // Store file type for each file
            $table->string('primary_file_path')->nullable()->after('file_types'); // Main file for display
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['file_paths', 'document_type', 'file_types', 'primary_file_path']);
        });
    }
};
