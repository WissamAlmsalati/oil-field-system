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
            $table->string('file_name');
            $table->string('file_path');
            $table->bigInteger('file_size'); // in bytes
            $table->string('file_type'); // pdf, doc, xlsx, etc.
            $table->string('mime_type');
            $table->enum('category', [
                'Contract',
                'Invoice',
                'Report',
                'Certificate',
                'License',
                'Manual',
                'Procedure',
                'Policy',
                'Form',
                'Other'
            ])->default('Other');
            $table->json('tags')->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_public')->default(false);
            $table->integer('download_count')->default(0);
            $table->date('expiry_date')->nullable();
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['category', 'client_id']);
            $table->index(['file_type', 'uploaded_by']);
            $table->index(['is_public', 'created_at']);
            $table->index('expiry_date');
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
