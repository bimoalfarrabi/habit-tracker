<?php

namespace App\Models;

use Database\Factories\HabitFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    /** @use HasFactory<HabitFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'frequency',
        'target_count',
        'reminder_time',
        'color',
        'icon',
        'is_active',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'archived_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->whereNull('archived_at');
    }
}
