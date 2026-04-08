
# Habit Tracker - Laravel 12 Documentation: Artisan Command, Scheduler & Cron Reminder Flow

## 1. Tujuan Dokumen
Dokumen ini melanjutkan blueprint teknis **Habit Tracker Laravel 12** dengan fokus pada:
- artisan command untuk reminder habit
- integrasi dengan Laravel Scheduler
- konfigurasi cron job di shared hosting
- alur reminder end-to-end
- struktur command class
- strategi logging, monitoring, dan debugging
- best practice agar scheduler tetap stabil di environment shared hosting

Dokumen ini dibuat untuk stack:
- Laravel 12
- PHP 8.2
- MySQL
- Vite + Tailwind CSS
- shared hosting friendly
- notifikasi berbasis cron
- tanpa Reverb, tanpa WebSocket, tanpa queue worker

---

## 2. Gambaran Arsitektur Reminder

Reminder di aplikasi ini **tidak** dikirim secara realtime.  
Sebagai gantinya, reminder dibuat dengan pendekatan:

```text
Cron Hosting
→ php artisan schedule:run
→ Laravel Scheduler
→ Habit Reminder Command
→ HabitReminderService
→ NotificationService
→ insert ke tabel notifications
→ frontend ambil notif lewat page refresh / polling
```

Dengan model ini:
- tidak perlu process yang jalan terus
- tidak perlu queue worker permanen
- tidak perlu WebSocket server
- tetap cocok untuk shared hosting

---

## 3. Komponen yang Terlibat

Komponen final yang terlibat:

1. **Cron job hosting**
2. **Laravel Scheduler**
3. **Artisan Command**
4. **HabitReminderService**
5. **NotificationService**
6. **Tabel `notifications`**
7. **Frontend notification UI**

---

## 4. Prinsip Desain Scheduler

### 4.1 Scheduler sebagai entry point tunggal
Daripada membuat banyak cron command langsung di panel hosting, lebih baik gunakan:

```bash
php artisan schedule:run
```

Lalu biarkan Laravel yang menentukan task apa saja yang jalan.

Keuntungan:
- lebih rapi
- lebih mudah dirawat
- task mudah ditambah nanti
- semua schedule ada di satu tempat

### 4.2 Command harus idempotent
Karena cron bisa berjalan berulang, command reminder harus aman jika dipanggil berkali-kali.

Artinya:
- jangan kirim reminder dua kali untuk slot yang sama
- selalu cek kondisi sebelum insert notifikasi
- anggap command bisa dijalankan kapan saja tanpa merusak data

### 4.3 Scheduler harus toleran terhadap delay
Shared hosting kadang:
- tidak presisi
- cron bisa telat beberapa detik atau menit
- resource server tidak selalu konsisten

Karena itu:
- jangan bergantung pada detik
- pakai toleransi waktu
- desain reminder agar tidak rapuh

---

## 5. Flow Reminder End-to-End

## 5.1 Flow normal
```text
User membuat habit
→ set reminder_time, misalnya 08:00
→ habit tersimpan aktif

Pukul 08:00
→ cron hosting memanggil artisan schedule:run
→ scheduler mengeksekusi command habit:check-reminders
→ command memanggil HabitReminderService@run()
→ service mencari habit aktif yang reminder_time-nya cocok
→ service cek apakah habit sudah completed hari ini
→ service cek apakah reminder serupa sudah dibuat hari ini
→ jika belum, NotificationService membuat record notif
→ notif tampil saat user membuka halaman / polling berjalan
```

## 5.2 Flow saat habit sudah selesai
```text
Command berjalan
→ habit ditemukan
→ HabitLog status completed hari ini ditemukan
→ reminder tidak dibuat
```

## 5.3 Flow saat notif sudah pernah dibuat
```text
Command berjalan
→ habit ditemukan
→ belum completed
→ notifikasi reminder serupa hari ini sudah ada
→ reminder tidak dibuat lagi
```

---

## 6. Command Planning

## 6.1 Nama command yang direkomendasikan
Gunakan command yang jelas dan mudah dikenali.

Rekomendasi:
- `habit:check-reminders`

Opsional tambahan nanti:
- `habit:send-daily-summary`
- `habit:cleanup-notifications`
- `habit:recalculate-streaks`

Untuk fase awal, fokus pada:
- `habit:check-reminders`

---

## 6.2 Generate command
Command bisa dibuat dengan:

```bash
php artisan make:command CheckHabitReminders
```

Laravel akan membuat file seperti:
```text
app/Console/Commands/CheckHabitReminders.php
```

---

## 6.3 Tanggung jawab command
Command sebaiknya:
- menjadi jembatan antara scheduler dan service
- tidak memuat query besar langsung di dalam command
- tidak memuat business rule detail
- cukup memanggil service dan menampilkan output console

### Yang dilakukan command:
- log start process
- panggil `HabitReminderService`
- tangani error utama
- tampilkan summary singkat ke console

### Yang tidak dilakukan command:
- query reminder kompleks langsung di file command
- insert notifikasi langsung tanpa service
- logika anti-duplicate yang bercampur dengan output console

---

## 6.4 Skeleton command
```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HabitReminderService;
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
            $this->line('Processed habits: ' . ($result['processed'] ?? 0));
            $this->line('Created notifications: ' . ($result['created'] ?? 0));
            $this->line('Skipped habits: ' . ($result['skipped'] ?? 0));

            return self::SUCCESS;
        } catch (Throwable $e) {
            report($e);
            $this->error('Habit reminder check failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
```

---

## 7. HabitReminderService Revisi untuk Scheduler

Agar command lebih informatif, `HabitReminderService::run()` sebaiknya mengembalikan summary array.

### Output yang direkomendasikan
```php
[
    'processed' => 120,
    'created' => 18,
    'skipped' => 102,
]
```

### Alasan
Dengan summary ini:
- console output lebih berguna
- debugging lebih mudah
- nanti bisa dipakai untuk logging

### Skeleton versi revisi
```php
public function run(): array
{
    $processed = 0;
    $created = 0;
    $skipped = 0;

    Habit::query()
        ->active()
        ->whereNotNull('reminder_time')
        ->with('user')
        ->chunkById(100, function ($habits) use (&$processed, &$created, &$skipped) {
            foreach ($habits as $habit) {
                $processed++;

                $createdForHabit = $this->processHabit($habit);

                if ($createdForHabit) {
                    $created++;
                } else {
                    $skipped++;
                }
            }
        });

    return [
        'processed' => $processed,
        'created' => $created,
        'skipped' => $skipped,
    ];
}
```

### Signature `processHabit()`
```php
public function processHabit(Habit $habit): bool
```

Return:
- `true` jika notifikasi berhasil dibuat
- `false` jika dilewati

---

## 8. Scheduler Planning

## 8.1 Lokasi scheduler
Scheduler didefinisikan di:
```text
app/Console/Kernel.php
```

## 8.2 Konfigurasi dasar
Contoh:
```php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('habit:check-reminders')->everyMinute();
    }
}
```

### Kenapa `everyMinute()`?
Karena:
- reminder berbasis jam:menit
- shared hosting umumnya mendukung cron per menit
- paling aman untuk kebutuhan reminder harian sederhana

---

## 8.3 Opsi frekuensi
Pilihan frekuensi:
- `everyMinute()` → terbaik untuk reminder presisi menit
- `everyFiveMinutes()` → lebih ringan, tapi kurang presisi
- `hourly()` → terlalu kasar untuk habit reminder individual

Rekomendasi final:
- gunakan `everyMinute()` bila hosting memungkinkan
- fallback ke `everyFiveMinutes()` bila resource terbatas

---

## 8.4 Menjalankan tanpa overlap
Untuk shared hosting sederhana, overlap kecil biasanya jarang, tapi kalau command nanti jadi lebih berat, kamu bisa menambahkan:

```php
$schedule->command('habit:check-reminders')
    ->everyMinute()
    ->withoutOverlapping();
```

### Catatan
`withoutOverlapping()` membantu mencegah dua instance task berjalan bersamaan jika task sebelumnya belum selesai.

---

## 8.5 Environment restriction
Kalau suatu saat kamu ingin membedakan schedule dev dan production, bisa gunakan:

```php
$schedule->command('habit:check-reminders')
    ->everyMinute()
    ->environments(['production']);
```

Untuk fase awal, ini opsional.

---

## 9. Cron Job di Shared Hosting

## 9.1 Konsep
Cron job di panel hosting akan memanggil scheduler Laravel secara periodik.

Command umumnya:
```bash
php /path-to-project/artisan schedule:run
```

### Contoh format umum
```bash
* * * * * php /home/username/app/artisan schedule:run >> /dev/null 2>&1
```

---

## 9.2 Penyesuaian path
Path harus disesuaikan dengan struktur server hosting kamu.

Contoh variasi:
```bash
* * * * * /usr/local/bin/php /home/username/public_html/project/artisan schedule:run
```

atau

```bash
* * * * * php82 /home/username/laravel-app/artisan schedule:run
```

### Yang perlu dipastikan
- binary PHP benar
- path `artisan` benar
- project root benar
- versi PHP sesuai 8.2

---

## 9.3 Cara uji manual
Sebelum bergantung pada cron hosting, uji command manual:

```bash
php artisan habit:check-reminders
```

Lalu uji scheduler manual:
```bash
php artisan schedule:run
```

Kalau dua ini berhasil, baru pasang cron job di hosting panel.

---

## 10. Toleransi Waktu Reminder

## 10.1 Masalah umum
Kalau reminder dicocokkan persis:
```php
$now->format('H:i') === $habit->reminder_time
```

maka bisa bermasalah jika:
- cron telat 1 menit
- scheduler tidak jalan tepat waktu
- server sibuk

## 10.2 Solusi yang direkomendasikan
Gunakan toleransi waktu kecil, misalnya 1–2 menit.

### Contoh pendekatan
- reminder dianggap valid jika `reminder_time` berada dalam rentang:
  - sekarang
  - atau 1 menit sebelumnya

### Contoh helper
```php
public function shouldSendReminder(Habit $habit, Carbon $now): bool
{
    if (!$habit->reminder_time) {
        return false;
    }

    $target = Carbon::createFromFormat('H:i:s', $habit->reminder_time);
    $current = Carbon::createFromFormat('H:i', $now->format('H:i'));

    return abs($target->diffInMinutes($current, false)) <= 1;
}
```

### Catatan
Format `time` dari database kadang terbaca sebagai `08:00:00`, jadi pastikan parsing konsisten.

---

## 11. Anti-Duplicate Reminder Strategy

## 11.1 Kenapa penting
Karena scheduler berjalan berulang, tanpa anti-duplicate user bisa menerima spam reminder.

## 11.2 Aturan anti-duplicate minimum
Sebelum membuat notif, cek:
- `user_id` sama
- `type = habit_reminder`
- `habit_id` sama di payload/data
- tanggal yang sama

## 11.3 Query ide
```php
$alreadyNotified = UserNotification::query()
    ->where('user_id', $habit->user_id)
    ->where('type', 'habit_reminder')
    ->whereDate('created_at', now()->toDateString())
    ->where('data->habit_id', $habit->id)
    ->exists();
```

## 11.4 Anti-duplicate yang lebih kuat
Kalau nanti dibutuhkan, kamu bisa menambah kolom:
- `dedupe_key`

Contoh value:
```text
habit_reminder:user_id:habit_id:date
```

Tapi untuk MVP, validasi query biasa sudah cukup.

---

## 12. Logging & Monitoring

## 12.1 Kenapa perlu log
Cron job sering jalan tanpa UI. Kalau ada masalah, satu-satunya petunjuk sering datang dari log.

## 12.2 Yang sebaiknya dilog
- command mulai
- jumlah habit diproses
- jumlah notif dibuat
- jumlah skip
- error yang muncul

## 12.3 Logging sederhana
Di command:
```php
\Log::info('Habit reminder check started');
\Log::info('Habit reminder check finished', $result);
```

Jika error:
```php
\Log::error('Habit reminder check failed', [
    'message' => $e->getMessage(),
]);
```

## 12.4 Lokasi log Laravel
Biasanya di:
```text
storage/logs/laravel.log
```

Di shared hosting, pastikan folder `storage/` writable.

---

## 13. Debugging Checklist

Kalau reminder tidak muncul, cek berurutan:

### 13.1 Scheduler
- apakah cron hosting benar-benar aktif
- apakah `schedule:run` bisa dieksekusi
- apakah path `artisan` benar
- apakah binary PHP benar

### 13.2 Command
- apakah `php artisan habit:check-reminders` berhasil
- apakah command muncul di `php artisan list`
- apakah ada exception di log

### 13.3 Data habit
- habit aktif
- `reminder_time` terisi
- user valid
- habit belum diarsipkan

### 13.4 Habit log
- mungkin user sudah completed hari ini
- jika iya, reminder memang tidak akan dibuat

### 13.5 Notification
- mungkin notif sudah pernah dibuat hari ini
- cek tabel `notifications`

### 13.6 Frontend
- notif sebenarnya sudah masuk DB, tapi UI belum fetch
- cek endpoint polling / halaman notif

---

## 14. Command Testing Strategy

## 14.1 Test manual basic
1. buat habit aktif
2. set `reminder_time` ke 1–2 menit dari sekarang
3. pastikan belum ada `habit_logs` completed hari ini
4. jalankan:
```bash
php artisan habit:check-reminders
```
5. cek tabel `notifications`

## 14.2 Test duplicate prevention
1. jalankan command sekali
2. jalankan lagi di menit yang sama
3. pastikan notifikasi tidak bertambah ganda

## 14.3 Test completed habit
1. buat `habit_log` status `completed` untuk hari ini
2. jalankan command
3. pastikan notifikasi tidak dibuat

## 14.4 Test archived habit
1. arsipkan habit
2. jalankan command
3. pastikan habit tidak diproses

---

## 15. Future Command Expansion

Setelah reminder dasar stabil, kamu bisa menambah command lain:

### 15.1 Daily summary
Contoh:
- ringkasan habit hari ini
- total fokus hari ini
- habit yang terlewat

Command:
```text
habit:send-daily-summary
```

### 15.2 Streak warning
Contoh:
- “Kalau hari ini tidak check-in, streak kamu putus”

Command:
```text
habit:check-streak-warnings
```

### 15.3 Cleanup old notifications
Contoh:
- hapus notif sangat lama
- arsipkan notif yang tidak relevan lagi

Command:
```text
habit:cleanup-notifications
```

Untuk MVP, jangan implement semua sekaligus. Fokus dulu ke:
- `habit:check-reminders`

---

## 16. Controller & UI Integration

Walaupun reminder dibuat lewat command, user tetap melihat hasilnya lewat UI.

### 16.1 Notification dropdown / page
Frontend bisa mengambil:
- 10 notif terbaru
- unread count

Dari:
- `NotificationController@list`
- `NotificationController@index`

### 16.2 Polling sederhana
Kalau ingin dropdown notif terasa hidup:
- polling tiap 15–30 detik
- ambil unread count + latest notifications

Tapi walaupun tanpa polling, reminder tetap berguna karena:
- notif tetap tersimpan di database
- user akan melihatnya saat reload halaman

---

## 17. Security & Stability Notes

### 17.1 Jangan expose cron endpoint publik
Jangan buat reminder dipicu lewat URL publik seperti:
```text
https://domain.com/run-reminder
```

Lebih aman gunakan:
- cron hosting
- artisan command
- scheduler Laravel

### 17.2 Jangan letakkan logic besar di Kernel
`Kernel` cukup untuk deklarasi jadwal.
Semua logic tetap di:
- command
- service

### 17.3 Pastikan command ringan
Karena dijalankan sering:
- query harus efisien
- gunakan index
- pakai `chunkById()` untuk dataset besar

---

## 18. Task Breakdown Implementasi

## Fase 1 - Command Foundation
- buat `CheckHabitReminders` command
- definisikan signature dan description
- inject `HabitReminderService`
- tampilkan output summary

## Fase 2 - Service Integration
- revisi `HabitReminderService::run()` agar return summary
- revisi `processHabit()` agar return boolean
- tambahkan toleransi waktu reminder

## Fase 3 - Scheduler
- daftarkan command di `Kernel`
- set `everyMinute()`
- tambahkan `withoutOverlapping()` bila perlu

## Fase 4 - Cron Hosting
- cek path project
- cek binary PHP 8.2
- pasang cron job di panel hosting
- uji `schedule:run`

## Fase 5 - Debugging & Logging
- tambahkan log info/error
- uji duplicate prevention
- uji completed habit
- uji archived habit

## Fase 6 - UI Confirmation
- cek notifikasi masuk ke DB
- cek endpoint notif
- cek unread count tampil benar

---

## 19. Checklist Verifikasi

### Command
- command muncul di `php artisan list`
- command bisa dijalankan manual
- output summary tampil

### Scheduler
- `schedule:run` menjalankan command
- task berjalan sesuai interval

### Cron
- cron di hosting aktif
- scheduler benar-benar terpanggil
- log menunjukkan task berjalan

### Reminder logic
- notif dibuat hanya untuk habit eligible
- habit completed tidak dikirimi reminder
- habit archived tidak diproses
- notif tidak dobel di hari yang sama

### UI
- notif baru muncul di halaman notif
- unread count sesuai DB
- mark as read tetap bekerja

---

## 20. Summary
Dokumen ini menetapkan fondasi lengkap untuk:
- command reminder berbasis artisan
- scheduler Laravel
- cron job shared hosting
- flow reminder end-to-end
- anti-duplicate strategy
- toleransi waktu
- logging, debugging, dan testing

Dengan dokumen ini, sistem notifikasi kamu sekarang sudah punya fondasi teknis yang realistis untuk shared hosting.

Langkah berikut yang paling logis adalah:
1. membuat **Blade layout + page flow documentation**
2. atau membuat **UI module documentation (dashboard, habits, notifications, focus timer)**
3. atau langsung membuat **starter code artisan command + scheduler + service revisi**
