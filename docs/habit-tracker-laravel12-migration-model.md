
# Habit Tracker - Laravel 12 Documentation: Migration & Model

## 1. Tujuan Dokumen
Dokumen ini berisi perencanaan teknis untuk:
- struktur migration database
- relasi antar tabel
- rancangan model Eloquent
- casting, fillable, dan helper method dasar
- urutan implementasi migration dan model

Dokumen ini dibuat untuk melanjutkan blueprint aplikasi **Habit Tracker Laravel 12** yang:
- menggunakan PHP 8.2
- memakai Vite + Tailwind
- cocok untuk shared hosting
- menggunakan notifikasi berbasis cron job
- tidak bergantung pada Reverb atau websocket

---

## 2. Prinsip Desain Database

### 2.1 Prinsip umum
- gunakan struktur yang cukup normal untuk MVP, jangan over-engineer
- utamakan tabel yang mudah dipahami dan mudah di-query
- pisahkan data master, data log, dan data notifikasi
- simpan data statistik penting yang memang layak dihitung belakangan, bukan semuanya diprecompute

### 2.2 Prinsip relasi
- satu user bisa memiliki banyak habit
- satu habit bisa memiliki banyak log harian
- satu user bisa memiliki banyak sesi fokus
- satu user bisa memiliki banyak notifikasi

### 2.3 Prinsip implementasi
- pakai foreign key
- pakai soft delete hanya jika benar-benar diperlukan
- gunakan enum terbatas melalui string + validation, bukan enum DB bila ingin fleksibel
- gunakan index untuk kolom yang sering dipakai filter atau agregasi

---

## 3. Daftar Tabel Final (MVP + Ready to Grow)

Tabel inti:
1. `users`
2. `habits`
3. `habit_logs`
4. `focus_sessions`
5. `notifications`

Opsional fase lanjut:
6. `habit_categories`
7. `daily_reviews`
8. `achievement_badges`
9. `user_notification_preferences`

Untuk fase awal, fokus implementasi utama cukup:
- users
- habits
- habit_logs
- focus_sessions
- notifications

---

## 4. ERD Sederhana

```text
users
 ├── hasMany habits
 ├── hasMany habit_logs
 ├── hasMany focus_sessions
 └── hasMany notifications

habits
 ├── belongsTo users
 └── hasMany habit_logs

habit_logs
 ├── belongsTo users
 └── belongsTo habits

focus_sessions
 └── belongsTo users

notifications
 └── belongsTo users
```

---

## 5. Migration Planning

## 5.1 Users
Laravel sudah menyediakan migration bawaan untuk `users`, `password_reset_tokens`, dan `sessions` jika diperlukan.

### Struktur minimum `users`
- id
- name
- email
- email_verified_at
- password
- remember_token
- created_at
- updated_at

### Catatan
Tidak perlu banyak custom field di awal. Jika nanti butuh profile tambahan, buat tabel terpisah atau tambahkan kolom seperlunya.

---

## 5.2 Habits

### Tujuan tabel
Menyimpan habit milik user.

### Kolom
- id
- user_id
- title
- description (nullable)
- frequency (`daily` / `weekly`)
- target_count (default 1)
- reminder_time (nullable)
- color (nullable)
- icon (nullable)
- is_active (default true)
- archived_at (nullable)
- created_at
- updated_at

### Penjelasan
- `frequency`: untuk membedakan habit harian atau mingguan
- `target_count`: misalnya minum air 8 kali, baca 1 kali, olahraga 3 kali seminggu
- `reminder_time`: dipakai cron sebagai acuan notifikasi
- `is_active`: memudahkan filter habit aktif tanpa harus menghapus data
- `archived_at`: lebih aman daripada hard delete jika habit ingin disembunyikan dari daftar aktif

### Index yang disarankan
- index `user_id`
- index gabungan `user_id, is_active`
- index `reminder_time`

### Draft migration
```php
Schema::create('habits', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('frequency', 20)->default('daily');
    $table->unsignedInteger('target_count')->default(1);
    $table->time('reminder_time')->nullable();
    $table->string('color', 30)->nullable();
    $table->string('icon', 50)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamp('archived_at')->nullable();
    $table->timestamps();

    $table->index('user_id');
    $table->index(['user_id', 'is_active']);
    $table->index('reminder_time');
});
```

---

## 5.3 Habit Logs

### Tujuan tabel
Mencatat progres habit per tanggal.

### Kolom
- id
- habit_id
- user_id
- log_date
- status (`completed`, `skipped`, `missed`)
- qty (default 1)
- note (nullable)
- created_at
- updated_at

### Penjelasan
- `log_date`: tanggal log habit, bukan waktu input
- `status`:
  - `completed` = habit dikerjakan
  - `skipped` = sengaja dilewati
  - `missed` = tidak dilakukan
- `qty`: berguna untuk habit kuantitatif, misalnya 3 gelas air, 20 menit baca
- `note`: catatan pendek user

### Constraint penting
Satu habit idealnya hanya punya satu log utama per tanggal per user.
Gunakan unique constraint untuk mencegah duplikasi.

### Index yang disarankan
- unique `habit_id, log_date`
- index `user_id, log_date`
- index `status`

### Draft migration
```php
Schema::create('habit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('habit_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->date('log_date');
    $table->string('status', 20)->default('completed');
    $table->unsignedInteger('qty')->default(1);
    $table->text('note')->nullable();
    $table->timestamps();

    $table->unique(['habit_id', 'log_date']);
    $table->index(['user_id', 'log_date']);
    $table->index('status');
});
```

### Catatan desain
`user_id` sebenarnya bisa diturunkan dari `habit_id`, tapi tetap disimpan untuk:
- query dashboard lebih cepat
- agregasi per user lebih mudah
- mengurangi join berulang

---

## 5.4 Focus Sessions

### Tujuan tabel
Mencatat sesi fokus/timer user.

### Kolom
- id
- user_id
- habit_id (nullable)
- session_date
- start_time
- end_time (nullable)
- planned_duration_minutes (nullable)
- total_duration_seconds (default 0)
- focused_duration_seconds (default 0)
- unfocused_duration_seconds (default 0)
- interruption_count (default 0)
- status (`running`, `completed`, `cancelled`)
- note (nullable)
- created_at
- updated_at

### Penjelasan
- `habit_id` nullable karena sesi fokus bisa terkait habit tertentu atau sesi umum
- `session_date` memudahkan grouping per hari
- `focused_duration_seconds` dan `unfocused_duration_seconds` dipisah agar insight lebih jelas
- `interruption_count` bertambah saat halaman hidden lalu visible kembali
- `status` penting untuk membedakan sesi aktif, selesai, atau batal

### Index yang disarankan
- index `user_id, session_date`
- index `habit_id`
- index `status`

### Draft migration
```php
Schema::create('focus_sessions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('habit_id')->nullable()->constrained()->nullOnDelete();
    $table->date('session_date');
    $table->timestamp('start_time');
    $table->timestamp('end_time')->nullable();
    $table->unsignedInteger('planned_duration_minutes')->nullable();
    $table->unsignedInteger('total_duration_seconds')->default(0);
    $table->unsignedInteger('focused_duration_seconds')->default(0);
    $table->unsignedInteger('unfocused_duration_seconds')->default(0);
    $table->unsignedInteger('interruption_count')->default(0);
    $table->string('status', 20)->default('running');
    $table->text('note')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'session_date']);
    $table->index('habit_id');
    $table->index('status');
});
```

---

## 5.5 Notifications

### Tujuan tabel
Menyimpan notifikasi reminder dan informasi sistem yang dibuat oleh cron job atau sistem aplikasi.

### Kolom
- id
- user_id
- type
- title
- message
- data (json, nullable)
- is_read (default false)
- read_at (nullable)
- scheduled_for (nullable)
- created_at
- updated_at

### Penjelasan
- `type`: misalnya `habit_reminder`, `streak_warning`, `daily_summary`
- `title`: ringkasan notifikasi
- `message`: isi notifikasi
- `data`: metadata tambahan, misalnya `habit_id`, `target_date`, atau payload lain
- `scheduled_for`: membantu membedakan waktu rencana reminder dengan waktu insert nyata

### Index yang disarankan
- index `user_id, is_read`
- index `type`
- index `scheduled_for`
- index `created_at`

### Draft migration
```php
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('type', 50);
    $table->string('title');
    $table->text('message');
    $table->json('data')->nullable();
    $table->boolean('is_read')->default(false);
    $table->timestamp('read_at')->nullable();
    $table->timestamp('scheduled_for')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'is_read']);
    $table->index('type');
    $table->index('scheduled_for');
    $table->index('created_at');
});
```

### Pencegahan duplikasi notifikasi
Di level aplikasi, sebelum insert reminder:
- cek apakah user sudah punya notifikasi tipe yang sama
- untuk habit yang sama
- di tanggal yang sama
- pada slot waktu reminder yang sama

Tidak harus unique di DB dulu, karena payload notifikasi bisa lebih fleksibel. Validasi di service layer lebih aman untuk fase awal.

---

## 6. Urutan Migration yang Disarankan

Urutan aman:
1. users (bawaan Laravel)
2. habits
3. habit_logs
4. focus_sessions
5. notifications

Urutan command:
```bash
php artisan make:model Habit -m
php artisan make:model HabitLog -m
php artisan make:model FocusSession -m
php artisan make:model Notification -m
```

Jika ingin pisah:
```bash
php artisan make:migration create_habits_table
php artisan make:migration create_habit_logs_table
php artisan make:migration create_focus_sessions_table
php artisan make:migration create_notifications_table
```

Lalu jalankan:
```bash
php artisan migrate
```

---

## 7. Model Planning

## 7.1 User Model

### Relasi
- hasMany Habit
- hasMany HabitLog
- hasMany FocusSession
- hasMany Notification

### Draft tambahan di `User.php`
```php
public function habits()
{
    return $this->hasMany(Habit::class);
}

public function habitLogs()
{
    return $this->hasMany(HabitLog::class);
}

public function focusSessions()
{
    return $this->hasMany(FocusSession::class);
}

public function notifications()
{
    return $this->hasMany(Notification::class);
}
```

---

## 7.2 Habit Model

### Fillable
- user_id
- title
- description
- frequency
- target_count
- reminder_time
- color
- icon
- is_active
- archived_at

### Casts
- is_active => boolean
- archived_at => datetime
- reminder_time => string atau datetime custom bila perlu

### Relasi
- belongsTo User
- hasMany HabitLog

### Contoh model
```php
class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'frequency',
        'target_count',
        'reminder_time',
        'color',
        'icon',
        'is_active',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'archived_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(HabitLog::class);
    }
}
```

### Scope yang disarankan
```php
public function scopeActive($query)
{
    return $query->where('is_active', true)->whereNull('archived_at');
}
```

### Helper method opsional
```php
public function isCompletedToday(): bool
{
    return $this->logs()
        ->whereDate('log_date', now()->toDateString())
        ->where('status', 'completed')
        ->exists();
}
```

---

## 7.3 HabitLog Model

### Fillable
- habit_id
- user_id
- log_date
- status
- qty
- note

### Casts
- log_date => date

### Relasi
- belongsTo Habit
- belongsTo User

### Contoh model
```php
class HabitLog extends Model
{
    protected $fillable = [
        'habit_id',
        'user_id',
        'log_date',
        'status',
        'qty',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
        ];
    }

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Scope yang disarankan
```php
public function scopeCompleted($query)
{
    return $query->where('status', 'completed');
}

public function scopeForDate($query, $date)
{
    return $query->whereDate('log_date', $date);
}
```

---

## 7.4 FocusSession Model

### Fillable
- user_id
- habit_id
- session_date
- start_time
- end_time
- planned_duration_minutes
- total_duration_seconds
- focused_duration_seconds
- unfocused_duration_seconds
- interruption_count
- status
- note

### Casts
- session_date => date
- start_time => datetime
- end_time => datetime

### Relasi
- belongsTo User
- belongsTo Habit (nullable)

### Contoh model
```php
class FocusSession extends Model
{
    protected $fillable = [
        'user_id',
        'habit_id',
        'session_date',
        'start_time',
        'end_time',
        'planned_duration_minutes',
        'total_duration_seconds',
        'focused_duration_seconds',
        'unfocused_duration_seconds',
        'interruption_count',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }
}
```

### Accessor/helper opsional
```php
public function getFocusedMinutesAttribute(): int
{
    return (int) floor($this->focused_duration_seconds / 60);
}
```

---

## 7.5 Notification Model

### Catatan penamaan
Laravel punya class notifikasi bawaan. Untuk menghindari konflik, lebih aman gunakan nama model:

- `App\Models\UserNotification`
atau
- `HabitNotification`

Dokumen ini merekomendasikan **UserNotification**.

### Fillable
- user_id
- type
- title
- message
- data
- is_read
- read_at
- scheduled_for

### Casts
- data => array
- is_read => boolean
- read_at => datetime
- scheduled_for => datetime

### Relasi
- belongsTo User

### Contoh model
```php
class UserNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'scheduled_for',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'scheduled_for' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Scope yang disarankan
```php
public function scopeUnread($query)
{
    return $query->where('is_read', false);
}
```

---

## 8. Service / Query Planning yang Terkait Model

## 8.1 Dashboard query
Contoh kebutuhan:
- total habit aktif
- habit selesai hari ini
- completion rate 7 hari
- total focus minutes hari ini
- unread notifications

### Contoh query ide
```php
$totalActiveHabits = Habit::where('user_id', $userId)->active()->count();

$completedToday = HabitLog::where('user_id', $userId)
    ->whereDate('log_date', today())
    ->where('status', 'completed')
    ->count();

$totalFocusToday = FocusSession::where('user_id', $userId)
    ->whereDate('session_date', today())
    ->sum('focused_duration_seconds');
```

---

## 8.2 Reminder query
Cron job akan mencari:
- habit aktif
- punya `reminder_time`
- belum punya log completed hari ini
- belum punya notifikasi reminder serupa

### Contoh alur query
```php
$habits = Habit::query()
    ->active()
    ->whereNotNull('reminder_time')
    ->get();
```

Lalu untuk tiap habit:
- cek `HabitLog`
- cek `UserNotification`
- insert jika memenuhi syarat

---

## 9. Validasi Data yang Direkomendasikan

## Habit request
- title: required|string|max:255
- description: nullable|string
- frequency: required|in:daily,weekly
- target_count: required|integer|min:1
- reminder_time: nullable|date_format:H:i
- color: nullable|string|max:30
- icon: nullable|string|max:50
- is_active: boolean

## Habit log request
- habit_id: required|exists:habits,id
- log_date: required|date
- status: required|in:completed,skipped,missed
- qty: nullable|integer|min:1
- note: nullable|string

## Focus session request
- habit_id: nullable|exists:habits,id
- planned_duration_minutes: nullable|integer|min:1
- note: nullable|string

---

## 10. Naming Convention yang Disarankan

### Model
- User
- Habit
- HabitLog
- FocusSession
- UserNotification

### Controller
- DashboardController
- HabitController
- HabitLogController
- FocusSessionController
- NotificationController

### Request class
- StoreHabitRequest
- UpdateHabitRequest
- StoreHabitLogRequest
- StartFocusSessionRequest
- StopFocusSessionRequest

### Service class opsional
- HabitReminderService
- StreakService
- DashboardStatsService
- FocusSessionService

---

## 11. Urutan Implementasi Nyata

### Tahap 1
- siapkan migration habits
- siapkan migration habit_logs
- siapkan migration focus_sessions
- siapkan migration notifications
- migrate database

### Tahap 2
- buat model
- definisikan fillable
- definisikan casts
- definisikan relasi

### Tahap 3
- test relasi di tinker
- test create data dummy
- test query dashboard

### Tahap 4
- buat request validation
- buat controller CRUD

### Tahap 5
- buat cron command untuk reminder
- buat endpoint list notifikasi
- buat mark-as-read

---

## 12. Checklist Verifikasi

### Migration
- semua tabel berhasil dibuat
- foreign key valid
- index valid
- unique constraint habit log aktif

### Model
- relasi berjalan
- casting tanggal berjalan
- create/update mass assignment aman

### Data flow
- user bisa buat habit
- user bisa log habit
- focus session tersimpan
- notifikasi bisa diinsert dan dibaca

---

## 13. Rekomendasi Final
Untuk versi awal, struktur ini sudah cukup kuat:
- sederhana
- tidak terlalu banyak tabel
- tetap scalable
- cocok untuk shared hosting
- mudah dikembangkan ke statistik, badge, dan summary

Kalau ingin menambah fitur nanti, tabel yang paling aman ditambahkan belakangan adalah:
- `daily_reviews`
- `habit_categories`
- `achievements`

---

## 14. Summary
Dokumen ini menetapkan fondasi teknis untuk:
- migration database Laravel 12
- relasi model Eloquent
- query dashboard
- flow focus session
- flow notifikasi cron-based

Dengan fondasi ini, langkah berikut yang paling logis adalah:
1. membuat file migration asli Laravel
2. membuat model Eloquent final
3. membuat request validation
4. mulai implementasi controller dan blade views
