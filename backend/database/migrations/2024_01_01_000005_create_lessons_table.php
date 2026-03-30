<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('course_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->constrained('lessons')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['text', 'video', 'pdf', 'quiz', 'assignment', 'live'])->default('text');
            $table->integer('sort_order')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('available_at')->nullable(); // Drip content
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'is_published']);
            $table->index('sort_order');
        });

        Schema::create('lesson_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lesson_id')->unique()->constrained()->cascadeOnDelete();
            $table->longText('content')->nullable(); // Rich text/HTML
            $table->string('video_url')->nullable(); // YouTube/Vimeo/S3
            $table->string('video_provider')->nullable(); // youtube, vimeo, local
            $table->string('video_id')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable(); // pdf, ppt, doc
            $table->integer('file_size')->nullable();
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('lesson_completions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('enrollment_id')->constrained()->cascadeOnDelete();
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->integer('progress_percentage')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id', 'user_id']);
            $table->index(['user_id', 'completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_completions');
        Schema::dropIfExists('lesson_contents');
        Schema::dropIfExists('lessons');
    }
};
