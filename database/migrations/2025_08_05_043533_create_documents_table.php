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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('filename');
            $table->string('file_path');
            $table->enum('status', [
                'received',
                'forwarded_to_authority',
                'reviewed_by_authority',
                'forwarded_to_releaser',
                'released',
                'sent_to_employee',
                'seen_by_employee',
                'actioned_by_employee'
            ])->default('received');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('current_handler')->nullable()->constrained('users')->onDelete('set null');
            $table->text('authority_notes')->nullable();
            $table->enum('review_decision', ['approved', 'rejected'])->nullable();
            $table->text('extracted_text')->nullable();
            $table->json('detected_objects')->nullable();
            $table->json('document_numbers')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('forwarded_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('forwarded_to_releaser_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
