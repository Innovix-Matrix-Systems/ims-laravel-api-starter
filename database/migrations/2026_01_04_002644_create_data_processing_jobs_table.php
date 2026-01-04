<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Run the migrations. */
    public function up(): void
    {
        Schema::create('data_processing_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->unique();
            $table->string('type');
            $table->string('status');
            $table->string('entity_type')->nullable();
            $table->json('filters')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_file_name')->nullable();
            $table->integer('total_rows')->nullable();
            $table->integer('processed_rows')->nullable();
            $table->integer('success_count')->nullable();
            $table->integer('error_count')->nullable();
            $table->json('errors')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('user_id');
            $table->index('job_id');
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::dropIfExists('data_processing_jobs');
    }
};
