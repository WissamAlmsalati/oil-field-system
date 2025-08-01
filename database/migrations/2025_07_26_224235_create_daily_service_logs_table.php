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
        Schema::create('daily_service_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_number')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('field');
            $table->string('well');
            $table->string('contract');
            $table->string('job_no');
            $table->date('date');
            $table->string('linked_job_id')->nullable();
            $table->json('personnel')->nullable();
            $table->json('equipment_used')->nullable();
            $table->json('almansoori_rep')->nullable();
            $table->json('mog_approval_1')->nullable();
            $table->json('mog_approval_2')->nullable();
            $table->string('excel_file_path')->nullable();
            $table->string('excel_file_name')->nullable();
            $table->string('pdf_file_path')->nullable();
            $table->string('pdf_file_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_service_logs');
    }
};
