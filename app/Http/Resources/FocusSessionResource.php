<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FocusSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'habit_id' => $this->habit_id,
            'session_date' => $this->session_date?->toDateString(),
            'start_time' => $this->start_time?->toISOString(),
            'end_time' => $this->end_time?->toISOString(),
            'planned_duration_minutes' => $this->planned_duration_minutes,
            'total_duration_seconds' => $this->total_duration_seconds,
            'focused_duration_seconds' => $this->focused_duration_seconds,
            'unfocused_duration_seconds' => $this->unfocused_duration_seconds,
            'interruption_count' => $this->interruption_count,
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
