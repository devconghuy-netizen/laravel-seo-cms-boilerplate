<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_link_id')->constrained('affiliate_links')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->dateTime('converted_at');
            $table->timestamps();

            $table->index(['affiliate_link_id', 'converted_at']);
            $table->index('converted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_conversions');
    }
};
