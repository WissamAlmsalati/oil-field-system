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
        Schema::create('service_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_agreement_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('call_out_job_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->enum('status', ['In Field to Sign', 'Issue', 'Delivered', 'Invoiced']);
            $table->decimal('amount', 12, 2);
            $table->json('related_log_ids')->nullable();
            $table->json('documents')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_tickets');
    }
};
