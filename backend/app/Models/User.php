<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'school_id',
        'name',
        'email',
        'password',
        'phone',
        'avatar_path',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'city',
        'province',
        'postal_code',
        'identity_number',
        'status',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Str::uuid();
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\School\Models\School::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(\App\Modules\Course\Models\Course::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(\App\Modules\Course\Models\Enrollment::class);
    }

    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Modules\Course\Models\Course::class,
            'enrollments',
            'user_id',
            'course_id'
        )->withPivot('status', 'progress_percentage', 'enrolled_at')->withTimestamps();
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(\App\Modules\Cbt\Models\ExamAttempt::class);
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(\App\Modules\Assignment\Models\AssignmentSubmission::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(\App\Modules\Grade\Models\Grade::class);
    }

    public function classStudents(): HasMany
    {
        return $this->hasMany(\App\Modules\Attendance\Models\ClassStudent::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(\App\Modules\Attendance\Models\ClassRoom::class, 'teacher_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(\App\Modules\Attendance\Models\AttendanceRecord::class);
    }

    public function notifications(): HasMany
    {
        return $this->morphMany(\App\Modules\Notification\Models\Notification::class, 'notifiable');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(\App\Modules\Notification\Models\Announcement::class, 'author_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isParent(): bool
    {
        return $this->hasRole('parent');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? asset('storage/' . $this->avatar_path) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeStudents($query)
    {
        return $query->role('student');
    }

    public function scopeTeachers($query)
    {
        return $query->role('teacher');
    }
}
