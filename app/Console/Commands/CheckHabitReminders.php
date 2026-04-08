<?php

namespace App\Console\Commands;

use App\Services\HabitReminderService;
use Illuminate\Console\Command;
use Throwable;

class CheckHabitReminders extends Command
{
    protected $signature = 'habit:check-reminders';

    protected $description = 'Check habit reminders and create notifications for eligible users';

    public function __construct(
        protected HabitReminderService $habitReminderService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Habit reminder check started.');

        try {
            $result = $this->habitReminderService->run();

            $this->info('Habit reminder check finished.');
            $this->line('Processed habits: '.($result['processed'] ?? 0));
            $this->line('Created notifications: '.($result['created'] ?? 0));
            $this->line('Skipped habits: '.($result['skipped'] ?? 0));

            \Log::info('Habit reminder check finished', $result);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            \Log::error('Habit reminder check failed', [
                'message' => $exception->getMessage(),
            ]);
            $this->error('Habit reminder check failed: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
