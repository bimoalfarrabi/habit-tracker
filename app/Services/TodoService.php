<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class TodoService
{
    public function createForUser(User $user, array $data): Todo
    {
        $payload = $this->normalizePayload($data);

        return Todo::query()->create([
            ...$payload,
            'user_id' => $user->id,
            'priority' => $payload['priority'] ?? 'medium',
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    public function updateForUser(User $user, Todo $todo, array $data): Todo
    {
        $this->ensureOwned($user, $todo);

        $todo->update($this->normalizePayload($data));

        return $todo->refresh();
    }

    public function setCompletionForUser(User $user, Todo $todo, bool $isCompleted): Todo
    {
        $this->ensureOwned($user, $todo);

        $todo->update([
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? now() : null,
        ]);

        return $todo->refresh();
    }

    public function deleteForUser(User $user, Todo $todo): void
    {
        $this->ensureOwned($user, $todo);

        $todo->delete();
    }

    private function normalizePayload(array $data): array
    {
        if (! isset($data['due_date']) || $data['due_date'] === null || $data['due_date'] === '') {
            $data['due_date'] = null;
            $data['reminder_time'] = null;
        }

        return $data;
    }

    private function ensureOwned(User $user, Todo $todo): void
    {
        if ($todo->user_id !== $user->id) {
            throw new AuthorizationException('You are not allowed to access this resource.');
        }
    }
}
