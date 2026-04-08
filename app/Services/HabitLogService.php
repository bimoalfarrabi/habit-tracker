<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class HabitLogService
{
    public function storeForUser(User $user, array $data, bool $forceToday = false): HabitLog
    {
        return DB::transaction(function () use ($user, $data, $forceToday) {
            $habit = Habit::query()
                ->where('id', $data['habit_id'])
                ->where('user_id', $user->id)
                ->first();

            if (! $habit) {
                throw new AuthorizationException('You are not allowed to access this resource.');
            }

            $logDate = $forceToday
                ? now()->toDateString()
                : ($data['log_date'] ?? now()->toDateString());

            $habitLog = HabitLog::query()
                ->where('habit_id', $habit->id)
                ->whereDate('log_date', $logDate)
                ->first();

            if ($habitLog) {
                $habitLog->update([
                    'user_id' => $user->id,
                    'status' => $data['status'] ?? 'completed',
                    'qty' => $data['qty'] ?? 1,
                    'note' => $data['note'] ?? null,
                ]);

                $habitLog = $habitLog->refresh();
                $habitLog->setAttribute('action', 'updated');

                return $habitLog;
            }

            $habitLog = HabitLog::query()->create([
                'habit_id' => $habit->id,
                'log_date' => $logDate,
                'user_id' => $user->id,
                'status' => $data['status'] ?? 'completed',
                'qty' => $data['qty'] ?? 1,
                'note' => $data['note'] ?? null,
            ]);

            $habitLog = $habitLog->refresh();
            $habitLog->setAttribute('action', 'created');

            return $habitLog;
        });
    }

    public function updateForUser(User $user, HabitLog $habitLog, array $data): HabitLog
    {
        $this->ensureOwned($user, $habitLog);

        $habitLog->update([
            'status' => $data['status'] ?? $habitLog->status,
            'qty' => $data['qty'] ?? $habitLog->qty,
            'note' => $data['note'] ?? $habitLog->note,
        ]);

        return $habitLog->refresh();
    }

    public function deleteForUser(User $user, HabitLog $habitLog): void
    {
        $this->ensureOwned($user, $habitLog);

        $habitLog->delete();
    }

    private function ensureOwned(User $user, HabitLog $habitLog): void
    {
        if ($habitLog->user_id !== $user->id) {
            throw new AuthorizationException('You are not allowed to access this resource.');
        }
    }
}
