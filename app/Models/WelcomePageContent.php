<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelcomePageContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'hero_badge',
        'hero_title',
        'hero_highlight',
        'hero_description',
        'hero_primary_cta_text',
        'hero_secondary_cta_text',
        'preview_title',
        'preview_description',
        'stories_title',
        'stories_description',
        'features_title',
        'how_it_works_title',
        'final_cta_title',
        'final_cta_description',
        'footer_note',
    ];

    public static function singleton(): self
    {
        return static::query()->firstOrCreate(
            ['key' => 'home'],
            static::defaultContent()
        );
    }

    public static function defaultContent(): array
    {
        return [
            'hero_badge' => 'Habit Tracker + Todo + Multi-channel Reminder',
            'hero_title' => 'Bangun ritme harian yang realistis,',
            'hero_highlight' => 'bukan memaksa.',
            'hero_description' => 'Ritme membantu kamu menjaga habit kecil, merapikan todo harian, melacak sesi fokus, dan mengatur reminder email/Telegram dari satu dashboard.',
            'hero_primary_cta_text' => 'Coba Ritme Sekarang',
            'hero_secondary_cta_text' => 'Saya sudah punya akun',
            'preview_title' => 'Lihat rasa aplikasi Ritme',
            'preview_description' => 'Lebih dari checklist biasa. Ritme menggabungkan habit, todo, focus, dan settings reminder dalam alur yang ringkas.',
            'stories_title' => 'Dipakai di ritme kerja yang berbeda-beda',
            'stories_description' => 'Dari belajar mandiri sampai tim kecil, Ritme membantu menjaga konsistensi tanpa sistem yang ribet.',
            'features_title' => 'Fitur Inti Ritme',
            'how_it_works_title' => 'Mulai dalam 3 langkah',
            'final_cta_title' => 'Ready to keep your rhythm?',
            'final_cta_description' => 'Ritme dirancang untuk konsistensi jangka panjang. Mulai dari satu kebiasaan kecil hari ini.',
            'footer_note' => 'Laravel 12 · Blade UI',
        ];
    }
}
