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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('module')->default('general')->comment('Permission module/group');
            $table->string('resource')->nullable()->comment('Resource type (posts, categories, etc)');
            $table->string('action')->nullable()->comment('Action type (create, read, update, delete)');
            $table->boolean('is_system')->default(false)->comment('System permissions cannot be deleted');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['module', 'resource', 'action']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
