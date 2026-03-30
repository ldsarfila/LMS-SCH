<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->json('semesters')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->integer('grade_level')->default(1);
            $table->foreignUuid('teacher_id')->nullable()->constrained('users')->nullOnDelete(); // Homeroom teacher
            $table->integer('max_students')->default(30);
            $table->string('room')->nullable();
            $table->enum('shift', ['morning', 'afternoon', 'full_day'])->default('morning');
            $table->json('schedule')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'academic_year_id']);
            $table->index('grade_level');
        });

        Schema::create('class_students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('student_number')->nullable(); // NIS/NISN
            $table->date('joined_at')->useCurrent();
            $table->date('left_at')->nullable();
            $table->enum('status', ['active', 'graduated', 'transferred', 'dropped'])->default('active');
            $table->timestamps();

            $table->unique(['class_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('class_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->date('session_date');
            $table->enum('type', ['regular', 'extra', 'remedial'])->default('regular');
            $table->enum('shift', ['morning', 'afternoon'])->default('morning');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['class_id', 'session_date']);
            $table->index(['teacher_id', 'session_date']);
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('session_id')->constrained('attendance_sessions')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['present', 'late', 'sick', 'permission', 'alpha'])->default('present');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('notes')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('location')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('class_students');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('academic_years');
    }
};
