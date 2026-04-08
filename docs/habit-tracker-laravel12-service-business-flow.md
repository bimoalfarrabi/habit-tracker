
# Habit Tracker - Laravel 12 Documentation: Service Class Skeleton & Business Logic Flow

## 1. Tujuan Dokumen
Dokumen ini melanjutkan rangkaian blueprint teknis **Habit Tracker Laravel 12** dengan fokus pada:
- struktur service class
- pembagian business logic
- flow data antar controller, service, model, dan view
- pseudocode method inti
- dependency antar service
- aturan transaksi database
- urutan implementasi business logic

Dokumen ini diasumsikan berjalan pada stack:
- Laravel 12
- PHP 8.2
- MySQL
- Vite + Tailwind CSS
- shared hosting friendly
- notifikasi berbasis cron
- tanpa Reverb / WebSocket

---

## 2. Kenapa Perlu Service Layer

Pada titik ini, aplikasi sudah punya:
- migration & model
- request validation
- controller skeleton

Kalau semua business logic langsung dipindahkan ke controller, maka akan muncul masalah:
- controller cepat membesar
- logic sulit di-test
- logic sulit dipakai ulang
- query dan update data mudah tercecer di banyak tempat

Karena itu, service layer dipakai untuk:
- menyimpan business rule inti
- menjaga controller tetap tipis
- memisahkan validasi input dan proses domain
- memudahkan maintenance saat fitur bertambah

---

## 3. Prinsip Umum Service Design

### 3.1 Controller tetap tipis
Controller sebaiknya hanya melakukan:
- menerima request
- memanggil service
- menentukan response (view / redirect / JSON)

### 3.2 Service mengelola business rule
Service sebaiknya menangani:
- create / update yang punya aturan domain
- query lintas model
- pencegahan duplikasi
- status transition
- reminder / notification generation

### 3.3 Model tetap fokus pada data
Model sebaiknya menangani:
- relasi
- casts
- scope sederhana
- accessor ringan

### 3.4 Query kompleks yang sering dipakai dipindah ke service
Misalnya:
- statistik dashboard
- streak
- habit completion summary
- focus session summary
- reminder eligibility

---

## 4. Service List Final

Service utama yang direkomendasikan:

1. `DashboardStatsService`
2. `HabitService`
3. `HabitLogService`
4. `FocusSessionService`
5. `NotificationService`
6. `HabitReminderService`
7. `StreakService`

Untuk MVP, urutan prioritas implementasi:
- HabitService
- HabitLogService
- DashboardStatsService
- FocusSessionService
- NotificationService
- HabitReminderService
- StreakService

---

## 5. Struktur Folder yang Direkomendasikan

```text
app/
├── Services/
│   ├── DashboardStatsService.php
│   ├── HabitService.php
│   ├── HabitLogService.php
│   ├── FocusSessionService.php
│   ├── NotificationService.php
│   ├── HabitReminderService.php
│   └── StreakService.php
```

Jika nanti logic membesar, kamu bisa pecah ke:
- `Services/Habit/`
- `Services/Focus/`
- `Services/Notification/`

Tapi untuk awal, satu folder `Services/` sudah cukup rapi.

---

## 6. Global Business Flow

## 6.1 Flow Habit Create
```text
User submit form
→ StoreHabitRequest validate
→ HabitController@store
→ HabitService@createForUser
→ Habit model create
→ redirect ke habits.index
```

## 6.2 Flow Habit Check-in
```text
User klik check-in
→ StoreHabitLogRequest validate
→ HabitLogController@store
→ HabitLogService@storeForUser
→ cek habit milik user
→ cek log hari ini sudah ada atau belum
→ update/create habit log
→ optional: hitung ulang streak
→ redirect / JSON success
```

## 6.3 Flow Focus Timer
```text
User klik start
→ StartFocusSessionRequest validate
→ FocusSessionController@start
→ FocusSessionService@start
→ buat focus session status running
→ frontend timer jalan

User klik stop
→ StopFocusSessionRequest validate
→ FocusSessionController@stop
→ FocusSessionService@stop
→ hitung total duration
→ update focused/unfocused/interruption
→ set status completed/cancelled
→ return JSON
```

## 6.4 Flow Reminder Notification
```text
Cron jalan
→ schedule:run
→ command habit:check-reminders
→ HabitReminderService@run
→ ambil habit aktif dengan reminder_time
→ cek belum completed hari ini
→ cek notifikasi serupa belum dibuat
→ NotificationService@createForUser
→ insert notification
```

## 6.5 Flow Dashboard
```text
User buka dashboard
→ DashboardController@index
→ DashboardStatsService@getForUser
→ query habits, logs, focus sessions, notifications
→ susun statistik final
→ return view dashboard
```

---

## 7. HabitService

## 7.1 Tanggung jawab
HabitService menangani:
- create habit
- update habit
- archive habit
- toggle active status
- memastikan data habit selalu konsisten

### Business rules
- habit selalu terkait user tertentu
- `is_active` default true jika tidak dikirim
- habit yang diarsipkan tidak tampil di daftar aktif
- habit yang diarsipkan sebaiknya tidak lagi diproses untuk reminder

## 7.2 Method yang direkomendasikan
- `createForUser(User $user, array $data): Habit`
- `updateForUser(User $user, Habit $habit, array $data): Habit`
- `archiveForUser(User $user, Habit $habit): Habit`
- `toggleActiveForUser(User $user, Habit $habit): Habit`

## 7.3 Skeleton
```php
namespace App\Services;

use App\Models\Habit;
use App\Models\User;

class HabitService
{
    public function createForUser(User $user, array $data): Habit
    {
        return Habit::create([
            ...$data,
            'user_id' => $user->id,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function updateForUser(User $user, Habit $habit, array $data): Habit
    {
        if ($habit->user_id !== $user->id) {
            abort(403);
        }

        $habit->update($data);

        return $habit->refresh();
    }

    public function archiveForUser(User $user, Habit $habit): Habit
    {
        if ($habit->user_id !== $user->id) {
            abort(403);
        }

        $habit->update([
            'is_active' => false,
            'archived_at' => now(),
        ]);

        return $habit->refresh();
    }

    public function toggleActiveForUser(User $user, Habit $habit): Habit
    {
        if ($habit->user_id !== $user->id) {
            abort(403);
        }

        $habit->update([
            'is_active' => !$habit->is_active,
        ]);

        return $habit->refresh();
    }
}
```

## 7.4 Catatan implementasi
Kalau kamu ingin histori tetap aman:
- lebih baik `archive` daripada `delete`
- `delete` bisa dipakai hanya kalau habit belum punya log, atau hanya untuk admin/internal dev

---

## 8. HabitLogService

## 8.1 Tanggung jawab
HabitLogService menangani:
- create atau update log habit harian
- mencegah duplikasi log
- memastikan log hanya untuk habit milik user
- memberi fondasi untuk streak calculation

### Business rules
- satu habit maksimal satu log utama per tanggal
- log hanya boleh untuk habit milik user
- jika log hari itu sudah ada, update data yang ada
- status default biasanya `completed`
- log skipped / missed harus tetap bisa dihitung untuk histori

## 8.2 Method yang direkomendasikan
- `storeForUser(User $user, array $data): HabitLog`
- `updateForUser(User $user, HabitLog $habitLog, array $data): HabitLog`
- `deleteForUser(User $user, HabitLog $habitLog): void`
- `getDailySummary(User $user, Carbon|string $date): array`

## 8.3 Skeleton
```php
namespace App\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HabitLogService
{
    public function storeForUser(User $user, array $data): HabitLog
    {
        return DB::transaction(function () use ($user, $data) {
            $habit = Habit::query()
                ->where('id', $data['habit_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            $habitLog = HabitLog::updateOrCreate(
                [
                    'habit_id' => $habit->id,
                    'log_date' => $data['log_date'],
                ],
                [
                    'user_id' => $user->id,
                    'status' => $data['status'] ?? 'completed',
                    'qty' => $data['qty'] ?? 1,
                    'note' => $data['note'] ?? null,
                ]
            );

            return $habitLog->refresh();
        });
    }

    public function updateForUser(User $user, HabitLog $habitLog, array $data): HabitLog
    {
        if ($habitLog->user_id !== $user->id) {
            abort(403);
        }

        $habitLog->update($data);

        return $habitLog->refresh();
    }

    public function deleteForUser(User $user, HabitLog $habitLog): void
    {
        if ($habitLog->user_id !== $user->id) {
            abort(403);
        }

        $habitLog->delete();
    }
}
```

## 8.4 Kenapa pakai transaction
Walaupun `updateOrCreate()` sendiri sudah cukup aman untuk kasus sederhana, transaction berguna untuk:
- hook tambahan di masa depan
- hitung streak langsung setelah save
- insert audit log
- update summary harian kalau nanti ada

---

## 9. StreakService

## 9.1 Tanggung jawab
StreakService menangani:
- current streak
- longest streak
- evaluasi apakah streak putus
- statistik habit completion berurutan

### Business rules
Untuk MVP, definisi streak yang paling sederhana:
- streak dihitung dari status `completed`
- streak berjalan jika ada log `completed` pada hari-hari berurutan
- `skipped` dan `missed` tidak menambah streak
- untuk habit weekly, streak sebaiknya dihitung terpisah atau ditunda dulu ke fase lanjut

## 9.2 Method yang direkomendasikan
- `getCurrentStreakForHabit(Habit $habit): int`
- `getLongestStreakForHabit(Habit $habit): int`
- `getCurrentStreakForUser(User $user): int`
- `getHabitCompletionRate(Habit $habit, int $days = 7): float`

## 9.3 Skeleton sederhana
```php
namespace App\Services;

use App\Models\Habit;
use App\Models\User;

class StreakService
{
    public function getCurrentStreakForHabit(Habit $habit): int
    {
        $logs = $habit->logs()
            ->where('status', 'completed')
            ->orderByDesc('log_date')
            ->pluck('log_date')
            ->map(fn ($date) => \Illuminate\Support\Carbon::parse($date)->toDateString())
            ->toArray();

        if (empty($logs)) {
            return 0;
        }

        $streak = 0;
        $cursor = now()->toDateString();

        foreach ($logs as $logDate) {
            if ($logDate === $cursor) {
                $streak++;
                $cursor = now()->parse($cursor)->subDay()->toDateString();
                continue;
            }

            if ($logDate === now()->parse($cursor)->subDay()->toDateString() && $streak === 0) {
                $streak++;
                $cursor = now()->parse($logDate)->subDay()->toDateString();
                continue;
            }

            break;
        }

        return $streak;
    }
}
```

## 9.4 Catatan penting
Logic streak sering berubah setelah aplikasi mulai dipakai. Karena itu:
- mulai dari definisi sederhana
- jangan over-engineer
- dokumentasikan definisi streak di UI jika perlu

---

## 10. FocusSessionService

## 10.1 Tanggung jawab
FocusSessionService menangani:
- start focus session
- stop focus session
- memastikan hanya satu sesi running
- menghitung total durasi
- menyimpan focused/unfocused duration

### Business rules
- satu user hanya boleh punya satu sesi status `running`
- sesi hanya bisa dihentikan jika status masih `running`
- `total_duration_seconds` = `focused_duration_seconds + unfocused_duration_seconds`
- jika stop dipanggil, `end_time` wajib diisi
- `session_date` mengikuti tanggal mulai sesi

## 10.2 Method yang direkomendasikan
- `start(User $user, array $data): FocusSession`
- `stop(User $user, FocusSession $session, array $data): FocusSession`
- `cancel(User $user, FocusSession $session): FocusSession`
- `getTodaySummary(User $user): array`

## 10.3 Skeleton
```php
namespace App\Services;

use App\Models\FocusSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FocusSessionService
{
    public function start(User $user, array $data): FocusSession
    {
        $existingRunning = FocusSession::query()
            ->where('user_id', $user->id)
            ->where('status', 'running')
            ->first();

        if ($existingRunning) {
            return $existingRunning;
        }

        return FocusSession::create([
            'user_id' => $user->id,
            'habit_id' => $data['habit_id'] ?? null,
            'session_date' => now()->toDateString(),
            'start_time' => now(),
            'planned_duration_minutes' => $data['planned_duration_minutes'] ?? null,
            'status' => 'running',
            'note' => $data['note'] ?? null,
        ]);
    }

    public function stop(User $user, FocusSession $session, array $data): FocusSession
    {
        if ($session->user_id !== $user->id) {
            abort(403);
        }

        if ($session->status !== 'running') {
            abort(422, 'Sesi fokus sudah tidak aktif.');
        }

        return DB::transaction(function () use ($session, $data) {
            $focused = (int) ($data['focused_duration_seconds'] ?? 0);
            $unfocused = (int) ($data['unfocused_duration_seconds'] ?? 0);

            $session->update([
                'end_time' => now(),
                'focused_duration_seconds' => $focused,
                'unfocused_duration_seconds' => $unfocused,
                'total_duration_seconds' => $focused + $unfocused,
                'interruption_count' => (int) ($data['interruption_count'] ?? 0),
                'status' => $data['status'],
            ]);

            return $session->refresh();
        });
    }
}
```

## 10.4 Catatan UX penting
Untuk kasus user multi-monitor:
- timer **tidak harus auto-pause**
- lebih baik simpan `focused_duration` dan `unfocused_duration`
- dengan begitu sistem tetap jujur, tapi tidak terlalu kaku

---

## 11. NotificationService

## 11.1 Tanggung jawab
NotificationService menangani:
- create notification
- latest notifications
- unread count
- mark as read
- mark all as read

### Business rules
- notifikasi hanya terkait satu user
- notifikasi reminder tidak boleh spam berulang untuk slot yang sama
- read state harus konsisten antara `is_read` dan `read_at`

## 11.2 Method yang direkomendasikan
- `createForUser(User $user, array $payload): UserNotification`
- `markAsRead(UserNotification $notification): UserNotification`
- `markAllAsRead(User $user): int`
- `getLatestForUser(User $user, int $limit = 10): Collection`
- `getUnreadCount(User $user): int`

## 11.3 Skeleton
```php
namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    public function createForUser(User $user, array $payload): UserNotification
    {
        return UserNotification::create([
            'user_id' => $user->id,
            'type' => $payload['type'],
            'title' => $payload['title'],
            'message' => $payload['message'],
            'data' => $payload['data'] ?? null,
            'scheduled_for' => $payload['scheduled_for'] ?? null,
            'is_read' => false,
        ]);
    }

    public function markAsRead(UserNotification $notification): UserNotification
    {
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $notification->refresh();
    }

    public function markAllAsRead(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function getLatestForUser(User $user, int $limit = 10): Collection
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getUnreadCount(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }
}
```

---

## 12. HabitReminderService

## 12.1 Tanggung jawab
HabitReminderService menangani:
- proses reminder berbasis cron
- pencarian habit yang eligible
- pencegahan duplikasi reminder
- delegasi pembuatan notif ke NotificationService

### Business rules
Reminder dikirim jika:
- habit aktif
- habit punya `reminder_time`
- waktu saat ini sudah cocok / masuk toleransi
- habit belum completed hari ini
- reminder serupa belum dibuat hari ini

## 12.2 Method yang direkomendasikan
- `run(): void`
- `processUser(User $user): void`
- `processHabit(Habit $habit): void`
- `shouldSendReminder(Habit $habit, Carbon $now): bool`

## 12.3 Skeleton
```php
namespace App\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Carbon;

class HabitReminderService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function run(): void
    {
        Habit::query()
            ->active()
            ->whereNotNull('reminder_time')
            ->with('user')
            ->chunkById(100, function ($habits) {
                foreach ($habits as $habit) {
                    $this->processHabit($habit);
                }
            });
    }

    public function processHabit(Habit $habit): void
    {
        $now = now();

        if (!$this->shouldSendReminder($habit, $now)) {
            return;
        }

        $alreadyCompleted = HabitLog::query()
            ->where('habit_id', $habit->id)
            ->whereDate('log_date', $now->toDateString())
            ->where('status', 'completed')
            ->exists();

        if ($alreadyCompleted) {
            return;
        }

        $alreadyNotified = UserNotification::query()
            ->where('user_id', $habit->user_id)
            ->where('type', 'habit_reminder')
            ->whereDate('created_at', $now->toDateString())
            ->where('data->habit_id', $habit->id)
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        $this->notificationService->createForUser($habit->user, [
            'type' => 'habit_reminder',
            'title' => 'Reminder Habit',
            'message' => "Jangan lupa: {$habit->title}",
            'data' => [
                'habit_id' => $habit->id,
                'habit_title' => $habit->title,
            ],
            'scheduled_for' => $now,
        ]);
    }

    public function shouldSendReminder(Habit $habit, Carbon $now): bool
    {
        if (!$habit->reminder_time) {
            return false;
        }

        return $now->format('H:i') === $habit->reminder_time;
    }
}
```

## 12.4 Catatan praktis shared hosting
Karena cron di shared hosting kadang tidak presisi sampai detik:
- cocokkan `H:i`
- jangan terlalu bergantung pada akurasi detik
- kalau perlu, buat toleransi 1–2 menit

---

## 13. DashboardStatsService

## 13.1 Tanggung jawab
DashboardStatsService menyatukan statistik dari:
- habits
- habit_logs
- focus_sessions
- notifications
- streak

### Business rules
Statistik dashboard harus:
- cepat di-load
- mudah dipahami user
- cukup akurat untuk MVP
- tidak perlu terlalu banyak query berulang

## 13.2 Method yang direkomendasikan
- `getForUser(User $user): array`

## 13.3 Skeleton
```php
namespace App\Services;

use App\Models\FocusSession;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;

class DashboardStatsService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected StreakService $streakService
    ) {}

    public function getForUser(User $user): array
    {
        $totalActiveHabits = Habit::query()
            ->where('user_id', $user->id)
            ->active()
            ->count();

        $completedToday = HabitLog::query()
            ->where('user_id', $user->id)
            ->whereDate('log_date', today())
            ->where('status', 'completed')
            ->count();

        $focusSecondsToday = FocusSession::query()
            ->where('user_id', $user->id)
            ->whereDate('session_date', today())
            ->sum('focused_duration_seconds');

        return [
            'total_active_habits' => $totalActiveHabits,
            'completed_today' => $completedToday,
            'current_streak' => $this->streakService->getCurrentStreakForUser($user),
            'focus_minutes_today' => (int) floor($focusSecondsToday / 60),
            'unread_notifications' => $this->notificationService->getUnreadCount($user),
        ];
    }
}
```

## 13.4 Catatan performa
Kalau nanti dashboard mulai berat:
- tambahkan cache pendek
- hitung summary harian
- buat table agregat
- tapi untuk MVP, query langsung masih aman

---

## 14. Dependency Map Antar Service

```text
HabitService
  └── tidak wajib bergantung ke service lain

HabitLogService
  └── opsional memanggil StreakService

StreakService
  └── berdiri sendiri

FocusSessionService
  └── berdiri sendiri

NotificationService
  └── berdiri sendiri

HabitReminderService
  └── bergantung ke NotificationService

DashboardStatsService
  ├── bergantung ke NotificationService
  └── bergantung ke StreakService
```

### Prinsip penting
Jangan membuat service saling memanggil terlalu banyak, karena:
- rawan circular dependency
- sulit di-debug
- service jadi terlalu saling menempel

---

## 15. Transaksi Database yang Disarankan

Gunakan `DB::transaction()` saat:
- create/update habit log yang bisa memicu perubahan data lain
- stop focus session jika nanti ada side-effect tambahan
- reminder creation jika nanti ada audit log atau summary insert

Tidak wajib transaction untuk:
- query dashboard read-only
- mark one notification as read
- toggle sederhana yang hanya update satu record

---

## 16. Error Handling Pattern

### Untuk web flow
Gunakan:
- `abort(403)` untuk akses tidak sah
- redirect dengan flash message untuk operasi berhasil
- biarkan validation ditangani Form Request

### Untuk AJAX flow
Gunakan:
- `abort(403)` untuk unauthorized
- `abort(422)` untuk domain state tidak valid
- JSON message yang konsisten

Contoh:
```php
return response()->json([
    'message' => 'Sesi fokus dihentikan.',
    'data' => $session,
]);
```

---

## 17. Urutan Implementasi yang Disarankan

### Fase A - Habit Foundation
1. buat `HabitService`
2. pakai di `HabitController`
3. test create/update/archive

### Fase B - Habit Logging
1. buat `HabitLogService`
2. hubungkan dengan `HabitLogController`
3. test `updateOrCreate` log harian
4. verifikasi unique constraint berjalan

### Fase C - Dashboard
1. buat `StreakService`
2. buat `DashboardStatsService`
3. tampilkan stats dasar di dashboard

### Fase D - Focus Timer
1. buat `FocusSessionService`
2. test start session
3. test stop session via AJAX
4. verifikasi hanya satu running session

### Fase E - Notification
1. buat `NotificationService`
2. hubungkan ke `NotificationController`
3. buat list notif + unread count

### Fase F - Cron Reminder
1. buat `HabitReminderService`
2. buat artisan command
3. hubungkan ke scheduler
4. test cron job secara manual

---

## 18. Checklist Verifikasi

### HabitService
- habit tersimpan dengan `user_id` benar
- archive mengubah `is_active` dan `archived_at`
- user tidak bisa update habit orang lain

### HabitLogService
- satu habit satu log per tanggal
- log milik user lain tidak bisa diubah
- status dan qty tersimpan benar

### StreakService
- current streak terhitung benar untuk kasus sederhana
- streak nol jika tidak ada log completed

### FocusSessionService
- tidak bisa punya dua sesi running
- stop hanya untuk sesi running
- total duration = focused + unfocused

### NotificationService
- unread count benar
- mark as read konsisten
- mark all as read bekerja

### HabitReminderService
- reminder tidak dikirim kalau habit sudah completed
- reminder tidak dikirim dua kali pada hari sama
- reminder hanya untuk habit aktif

### DashboardStatsService
- data dashboard muncul lengkap
- angka sesuai database
- fokus hari ini terhitung benar

---

## 19. Rekomendasi Praktis untuk Coding Nyata

Kalau kamu ingin progres cepat tanpa chaos:
- implement service satu per satu
- setiap service langsung dipakai di controller terkait
- test pakai Tinker / manual browser sebelum lanjut
- jangan menunggu semua service selesai baru dites

Urutan terbaik:
1. HabitService
2. HabitLogService
3. DashboardStatsService
4. FocusSessionService
5. NotificationService
6. HabitReminderService
7. StreakService refinement

---

## 20. Summary
Dokumen ini menetapkan:
- service class mana saja yang dibutuhkan
- batas tanggung jawab tiap service
- flow business logic utama
- dependency antar service
- aturan transaksi dan error handling
- urutan implementasi aman untuk MVP

Setelah dokumen ini, langkah paling logis berikutnya adalah:
1. membuat **artisan command + scheduler documentation**
2. atau membuat **Blade layout + page flow documentation**
3. atau langsung membuat **starter code service class** siap tempel
