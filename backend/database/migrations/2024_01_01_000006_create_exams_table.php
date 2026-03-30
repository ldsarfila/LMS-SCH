<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['daily_quiz', 'mid_term', 'final_term', 'practice', 'certification'])->default('daily_quiz');
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'archived'])->default('draft');
            $table->integer('duration_minutes')->default(60);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->integer('passing_score')->default(70);
            $table->integer('total_points')->default(100);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            $table->boolean('show_correct_answer')->default(false);
            $table->boolean('show_result_immediately')->default(false);
            $table->integer('max_attempts')->default(1);
            $table->integer('time_between_attempts_minutes')->default(0);
            $table->boolean('require_lockdown_browser')->default(false);
            $table->boolean('prevent_tab_switch')->default(true);
            $table->integer('allowed_tab_switches')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index(['teacher_id', 'status']);
            $table->index('starts_at');
            $table->index('ends_at');
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('exam_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['multiple_choice', 'multiple_option', 'true_false', 'matching', 'short_answer', 'essay'])->default('multiple_choice');
            $table->longText('question_text');
            $table->longText('explanation')->nullable();
            $table->integer('points')->default(1);
            $table->integer('sort_order')->default(0);
            $table->json('options')->nullable(); // [{id, text, is_correct}]
            $table->json('correct_answers')->nullable(); // For multiple option/matching
            $table->string('expected_answer')->nullable(); // For short answer
            $table->json('rubric')->nullable(); // For essay grading
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['exam_id', 'type']);
            $table->index('sort_order');
        });

        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['in_progress', 'submitted', 'auto_submitted', 'graded'])->default('in_progress');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->decimal('score', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('passed')->default(false);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('tab_switches')->default(0);
            $table->json('browser_info')->nullable();
            $table->json('ip_address_history')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'user_id', 'attempt_number']);
            $table->index(['user_id', 'status']);
            $table->index(['exam_id', 'status']);
        });

        Schema::create('exam_attempt_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignUuid('question_id')->constrained('exam_questions')->cascadeOnDelete();
            $table->json('answer')->nullable(); // User's answer
            $table->boolean('is_correct')->default(false);
            $table->decimal('points_earned', 5, 2)->default(0);
            $table->text('feedback')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempt_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
    }
};
