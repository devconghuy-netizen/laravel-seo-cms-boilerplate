<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        $urlExpression = in_array($driver, ['pgsql', 'sqlite'], true)
            ? "'/storage/' || path"
            : "CONCAT('/storage/', path)";

        Schema::create('media', function (Blueprint $table) use ($driver, $urlExpression) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('size')->comment('File size in bytes');
            $table->string('disk')->default('public');
            $table->string('path');
            $driver === 'pgsql'
                ? $table->string('url')->storedAs($urlExpression)
                : $table->string('url')->virtualAs($urlExpression);
            $table->string('media_type')->comment('image, video, audio, document, archive');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->json('metadata')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('media_type');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
