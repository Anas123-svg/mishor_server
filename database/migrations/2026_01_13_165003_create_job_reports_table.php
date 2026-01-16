<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_name')->nullable();
            $table->foreignId('job_id')->nullable()->constrained('app_jobs')->onDelete('cascade');
            $table->json('form_data')->nullable();
            $table->string('layout')->nullable();
            $table->timestamps();
        });
    }

    /**  id INTEGER PRIMARY KEY AUTOINCREMENT,

     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_reports');
    }
};
