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
        Schema::table('call_out_jobs', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])
                  ->default('scheduled')
                  ->after('end_date');
            $table->text('description')->nullable()->after('work_order_number');
            $table->enum('priority', ['low', 'medium', 'high'])
                  ->default('medium')
                  ->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_out_jobs', function (Blueprint $table) {
            $table->dropColumn(['status', 'description', 'priority']);
        });
    }
};
