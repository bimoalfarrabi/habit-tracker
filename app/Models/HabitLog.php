<?php

namespace App\Models;

use Database\Factories\HabitLogFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitLog extends Model
{
    /** @use HasFactory<HabitLogFactory> */
    use HasFactory;

    protected $fillable = [
        'habit_id',
        'user_id',
        'log_date',
        'status',
        'qty',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
        ];
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('log_date', $date);
    }
}
