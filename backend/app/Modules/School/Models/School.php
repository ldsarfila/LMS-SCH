<?php

namespace App\Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'timezone',
        'logo_path',
        'banner_path',
        'settings',
        'status',
        'activated_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'activated_at' => 'datetime',
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

    public function users(): HasMany
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(\App\Modules\Course\Models\Course::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(\App\Modules\Attendance\Models\ClassRoom::class, 'school_id');
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(\App\Modules\Attendance\Models\AcademicYear::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(\App\Modules\Cbt\Models\Exam::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(\App\Modules\Assignment\Models\Assignment::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(\App\Modules\Notification\Models\Announcement::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner_path ? asset('storage/' . $this->banner_path) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
