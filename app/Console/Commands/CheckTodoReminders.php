<?php

namespace App\Console\Commands;

use App\Services\TodoReminderService;
use Illuminate\Console\Command;
use Throwable;

class CheckTodoReminders extends Command
{
    protected $signature = 'todo:check-reminders';

    protected $description = 'Check todo reminders and create notifications for eligible users';

    public function __construct(
        protected TodoReminderService $todoReminderService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Todo reminder check started.');

        try {
            $result = $this->todoReminderService->run();

            $this->info('Todo reminder check finished.');
            $this->line('Processed todos: '.($result['processed'] ?? 0));
            $this->line('Created notifications: '.($result['created'] ?? 0));
            $this->line('Skipped todos: '.($result['skipped'] ?? 0));

            \Log::info('Todo reminder check finished', $result);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            \Log::error('Todo reminder check failed', [
                'message' => $exception->getMessage(),
            ]);
            $this->error('Todo reminder check failed: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
