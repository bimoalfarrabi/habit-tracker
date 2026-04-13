<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class EmailVerificationCooldownService
{
    public function getRemainingSeconds(User $user): int
    {
        if (! $user->exists) {
            return 0;
        }

        $nextAllowedAt = (int) Cache::get($this->cacheKey($user->id), 0);
        $remaining = $nextAllowedAt - now()->timestamp;

        return max(0, $remaining);
    }

    public function markSent(User $user): int
    {
        $cooldownSeconds = $this->getCooldownSeconds();

        if ($cooldownSeconds < 1 || ! $user->exists) {
            return 0;
        }

        Cache::put(
            $this->cacheKey($user->id),
            now()->addSeconds($cooldownSeconds)->timestamp,
            now()->addSeconds($cooldownSeconds),
        );

        return $cooldownSeconds;
    }

    public function getCooldownSeconds(): int
    {
        return max(0, (int) config('auth.verification.resend_cooldown', 60));
    }

    private function cacheKey(int $userId): string
    {
        return "auth:verification-resend-cooldown:{$userId}";
    }
}
