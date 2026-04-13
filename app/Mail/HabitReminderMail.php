<?php

namespace App\Mail;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HabitReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Habit $habit,
        public string $scheduledAtLabel
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ritme Reminder: '.$this->habit->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reminders.habit',
            with: [
                'userName' => $this->user->name ?: 'Teman Ritme',
                'habitTitle' => $this->habit->title,
                'scheduledAtLabel' => $this->scheduledAtLabel,
            ],
        );
    }
}
