<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->enum('type', ['individual', 'group'])->default('individual');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->integer('max_points')->default(100);
            $table->boolean('allow_late_submission')->default(true);
            $table->integer('late_penalty_percentage')->default(10);
            $table->integer('max_files')->default(5);
            $table->integer('max_file_size_mb')->default(10);
            $table->json('allowed_file_types')->nullable(); // ['pdf', 'doc', 'docx']
            $table->json('rubric')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index('due_at');
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'submitted', 'graded', 'late'])->default('pending');
            $table->text('comments')->nullable();
            $table->json('files')->nullable(); // [{name, path, size, type}]
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->json('graded_rubric')->nullable();
            $table->foreignUuid('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->integer('late_days')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['assignment_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('graded_by')->constrained('users')->cascadeOnDelete();
            $table->enum('grade_type', ['exam', 'assignment', 'quiz', 'participation', 'project', 'final'])->default('exam');
            $table->string('gradeable_type');
            $table->uuid('gradeable_id');
            $table->decimal('score', 8, 2);
            $table->decimal('max_score', 8, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('letter_grade')->nullable();
            $table->decimal('weight', 5, 2)->default(1);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'course_id']);
            $table->index(['user_id', 'grade_type']);
            $table->index(['gradeable_type', 'gradeable_id']);
        });

        Schema::create('grade_scales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('scale'); // [{min, max, letter, gpa}]
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
