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
        Schema::create('seo_metas', function (Blueprint $table) {
            $table->id();
            $table->string('seoable_type');
            $table->unsignedBigInteger('seoable_id');
            $table->string('locale', 5)->default('en');
            $table->string('title')->nullable();
            $table->string('description', 160)->nullable();
            $table->string('keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type')->default('website');
            $table->string('twitter_card')->nullable();
            $table->string('twitter_handle')->nullable();
            $table->json('structured_data')->nullable();
            $table->boolean('index')->default(true);
            $table->boolean('follow')->default(true);
            $table->timestamps();
            
            $table->unique(['seoable_type', 'seoable_id', 'locale']);
            $table->index(['seoable_type', 'seoable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_metas');
    }
};
