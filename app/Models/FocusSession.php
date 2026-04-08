<?php

namespace App\Models;

use Database\Factories\FocusSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FocusSession extends Model
{
    /** @use HasFactory<FocusSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'habit_id',
        'session_date',
        'start_time',
        'end_time',
        'planned_duration_minutes',
        'total_duration_seconds',
        'focused_duration_seconds',
        'unfocused_duration_seconds',
        'interruption_count',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
