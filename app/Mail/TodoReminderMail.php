<?php

namespace App\Mail;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TodoReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Todo $todo,
        public string $scheduledAtLabel
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ritme Reminder: Todo '.$this->todo->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reminders.todo',
            with: [
                'userName' => $this->user->name ?: 'Teman Ritme',
                'todoTitle' => $this->todo->title,
                'todoPriority' => $this->todo->priority,
                'todoDueDate' => $this->todo->due_date?->format('d M Y'),
                'scheduledAtLabel' => $this->scheduledAtLabel,
            ],
        );
    }
}
