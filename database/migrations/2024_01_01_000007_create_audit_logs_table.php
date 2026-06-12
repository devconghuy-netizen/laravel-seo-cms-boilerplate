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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullableOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('action')->comment('create, update, delete, restore');
            $table->json('old_values')->nullable()->comment('Previous values before change');
            $table->json('new_values')->nullable()->comment('New values after change');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->uuid('correlation_id')->nullable()->comment('For request tracing');
            $table->string('description')->nullable();
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['model_type', 'model_id']);
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
            $table->index('correlation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
