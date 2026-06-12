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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5);
            $table->string('translatable_type');
            $table->unsignedBigInteger('translatable_id');
            $table->string('key');
            $table->longText('value');
            $table->timestamps();
            
            // Unique constraint: one translation per locale/model/key combination
            $table->unique(['locale', 'translatable_type', 'translatable_id', 'key']);
            
            // Indexes for common queries
            $table->index(['translatable_type', 'translatable_id']);
            $table->index(['locale', 'translatable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
