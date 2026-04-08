
# Habit Tracker - Query & Metrics Specification

## 1. Tujuan
Dokumen ini mendefinisikan spesifikasi query dan metrik untuk aplikasi **Habit Tracker Laravel 12** agar:
- perhitungan dashboard konsisten
- Codex memahami definisi angka yang harus ditampilkan
- query backend tidak ambigu
- derived fields, summary, dan aggregation memiliki aturan yang jelas
- implementasi service dan UI memakai definisi metrik yang sama

Dokumen ini mengikuti fondasi arsitektur Laravel 12, workflow frontend modern berbasis `resources/`, Vite, dan asset build terstruktur. fileciteturn0file0

Dokumen ini juga konsisten dengan use case, service flow, API contract, dan UI documentation yang sudah dibuat sebelumnya.

---

## 2. Scope
Dokumen ini mencakup:
1. definisi metrik dashboard
2. definisi derived field per resource
3. query summary harian
4. query summary mingguan
5. query fokus
6. query notifikasi
7. streak logic level query/service
8. completion rate
9. today habits aggregation
10. performance consideration
11. acceptance criteria per metrik

Dokumen ini **tidak** membahas detail implementasi chart library.  
Fokus utamanya adalah **apa yang dihitung**, **dari mana sumber datanya**, dan **aturan business logic-nya**.

---

## 3. Prinsip Umum Perhitungan

## 3.1 User scope wajib
Semua query summary harus selalu dibatasi oleh:
- `user_id = authenticated user`

Tidak boleh ada metrik lintas user pada aplikasi user-facing ini.

---

## 3.2 Source of truth
Sumber data utama:
- `habits` → master habit
- `habit_logs` → sumber utama progres
- `focus_sessions` → sumber utama fokus/timer
- `notifications` → sumber utama reminder dan info sistem

Derived fields tidak disimpan permanen di DB pada MVP, kecuali nanti benar-benar dibutuhkan untuk optimasi.

---

## 3.3 Date basis
Semua summary harian dihitung berdasarkan:
- `log_date` untuk `habit_logs`
- `session_date` untuk `focus_sessions`
- `created_at` atau `scheduled_for` untuk `notifications`, tergantung konteks

Default timezone mengikuti konfigurasi aplikasi/server.

---

## 3.4 Active habit basis
Metrik yang terkait habit aktif hanya menghitung habit yang:
- `is_active = true`
- `archived_at IS NULL`

Habit archived:
- tetap bisa muncul di histori jika dibutuhkan
- tidak ikut dihitung pada dashboard operasional utama

---

## 4. Definisi Metrik Dashboard Utama

## 4.1 total_active_habits

### Definisi
Jumlah habit aktif milik user saat ini.

### Sumber data
Tabel: `habits`

### Rule
Hitung semua habit dengan:
- `user_id = current user`
- `is_active = true`
- `archived_at IS NULL`

### Query intent
Angka ini menunjukkan beban habit aktif user, bukan jumlah seluruh habit yang pernah dibuat.

### Contoh query
```php
$totalActiveHabits = Habit::query()
    ->where('user_id', $user->id)
    ->active()
    ->count();
```

### Acceptance criteria
- habit archived tidak dihitung
- habit inactive tidak dihitung
- habit tanpa log tetap dihitung jika aktif

---

## 4.2 completed_today

### Definisi
Jumlah log habit dengan status `completed` untuk hari ini.

### Sumber data
Tabel: `habit_logs`

### Rule
Hitung semua `habit_logs` dengan:
- `user_id = current user`
- `log_date = today`
- `status = completed`

### Catatan penting
Ini **bukan** jumlah habit aktif yang sudah selesai secara unik bila data log tidak konsisten.  
Namun karena ada unique constraint `habit_id + log_date`, secara praktik hasilnya sama dengan jumlah habit yang diselesaikan hari ini.

### Query intent
Menunjukkan seberapa banyak kebiasaan yang berhasil dicentang hari ini.

### Contoh query
```php
$completedToday = HabitLog::query()
    ->where('user_id', $user->id)
    ->whereDate('log_date', today())
    ->where('status', 'completed')
    ->count();
```

### Acceptance criteria
- status `skipped` dan `missed` tidak dihitung
- log untuk hari lain tidak dihitung
- hanya data user sendiri yang dihitung

---

## 4.3 current_streak

### Definisi
Jumlah hari berurutan hingga hari ini atau kemarin, di mana user memiliki progres habit `completed` sesuai definisi streak aplikasi.

### Scope MVP
Untuk MVP, `current_streak` di level user didefinisikan sederhana sebagai:

> jumlah hari berurutan ke belakang di mana user memiliki **minimal satu log habit berstatus completed**.

### Kenapa definisi ini dipilih
- lebih sederhana
- cocok untuk dashboard global user
- tidak memaksa semua habit harus selesai
- lebih realistis untuk fase awal

### Rule
- cek hari ini: jika ada minimal satu `completed`, streak lanjut
- jika hari ini tidak ada, boleh cek kemarin sebagai awal streak bila hari ini belum selesai tapi user baru membuka app
- streak putus jika pada satu hari tidak ditemukan log `completed` sesuai aturan yang dipilih

### Catatan
Ini berbeda dari **streak per habit**.  
Streak per habit dihitung terpisah.

### Acceptance criteria
- jika user tidak punya log completed sama sekali → `0`
- jika hanya kemarin ada completed dan hari ini belum ada, product decision perlu konsisten:
  - rekomendasi MVP: current streak tetap boleh menampilkan streak aktif sampai hari kemarin bila hari ini belum berakhir
- definisi ini harus dijaga konsisten di seluruh UI

### Rekomendasi implementasi
Karena definisi streak sering berubah, implementasi tetap lebih aman di `StreakService`, bukan query tunggal mentah.

---

## 4.4 focus_minutes_today

### Definisi
Jumlah total menit fokus hari ini berdasarkan `focused_duration_seconds` dari semua sesi fokus hari ini.

### Sumber data
Tabel: `focus_sessions`

### Rule
- `user_id = current user`
- `session_date = today`
- jumlahkan `focused_duration_seconds`
- konversi ke menit dengan `floor(seconds / 60)` atau tampilkan rounded sesuai UI rule

### Query intent
Menunjukkan durasi fokus efektif, bukan total timer menyala.

### Contoh query
```php
$focusSecondsToday = FocusSession::query()
    ->where('user_id', $user->id)
    ->whereDate('session_date', today())
    ->sum('focused_duration_seconds');

$focusMinutesToday = (int) floor($focusSecondsToday / 60);
```

### Acceptance criteria
- `unfocused_duration_seconds` tidak ikut dihitung
- sesi cancelled tetap boleh dihitung jika datanya valid, tergantung product decision
- rekomendasi MVP: hitung semua session yang sudah stop, baik completed maupun cancelled, selama punya duration valid

---

## 4.5 unread_notifications

### Definisi
Jumlah notifikasi unread milik user.

### Sumber data
Tabel: `notifications`

### Rule
- `user_id = current user`
- `is_read = false`

### Contoh query
```php
$unreadNotifications = UserNotification::query()
    ->where('user_id', $user->id)
    ->where('is_read', false)
    ->count();
```

### Acceptance criteria
- notif read tidak dihitung
- notif user lain tidak dihitung

---

## 5. Derived Field Specification

## 5.1 is_completed_today pada Habit

### Definisi
Menunjukkan apakah habit tertentu sudah memiliki log `completed` untuk hari ini.

### Sumber data
- `habits`
- `habit_logs`

### Rule
Untuk tiap habit aktif:
- cari log dengan `habit_id = current habit`
- `log_date = today`
- `status = completed`

### Bentuk output
Boolean:
- `true`
- `false`

### Contoh implementasi intent
```php
$habit->logs()
    ->whereDate('log_date', today())
    ->where('status', 'completed')
    ->exists();
```

### UI use case
- checkbox / badge “Done today”
- warna status di habit card
- filter habit pending vs completed hari ini

---

## 5.2 today_log pada Habit

### Definisi
Log habit milik hari ini jika ada.

### Rule
Ambil satu log:
- `habit_id = current habit`
- `log_date = today`

### Bentuk output
Object atau `null`.

### Contoh shape
```json
{
  "id": 100,
  "status": "completed",
  "qty": 8,
  "note": "Selesai pagi"
}
```

### UI use case
- tampilkan qty hari ini
- edit log cepat
- badge completed / skipped / missed

---

## 5.3 has_running_focus_session

### Definisi
Menunjukkan apakah user sedang punya sesi fokus `running`.

### Sumber data
Tabel: `focus_sessions`

### Rule
Cari record:
- `user_id = current user`
- `status = running`

### Bentuk output
Boolean

### UI use case
- tombol timer berubah menjadi resume/stop
- dashboard menampilkan current running timer
- cegah start sesi baru

---

## 5.4 latest_notification_preview

### Definisi
Daftar singkat notifikasi terbaru untuk dropdown/navbar preview.

### Rule
- `user_id = current user`
- urut `latest()`
- `take(5)` atau `take(10)`

### UI use case
- preview notifikasi di navbar
- polling notif ringan

---

## 6. Today Habits Query Specification

## 6.1 Tujuan
Query ini dipakai untuk dashboard section **Today Habits**.

### Output yang diharapkan
Daftar habit aktif lengkap dengan derived fields:
- habit info dasar
- `is_completed_today`
- `today_log`
- optional `current_streak_for_habit`

---

## 6.2 Rule data inclusion
Masukkan habit yang:
- `user_id = current user`
- `is_active = true`
- `archived_at IS NULL`

---

## 6.3 Sorting recommendation
Urutan yang direkomendasikan:
1. habit yang belum completed hari ini
2. habit yang sudah completed hari ini
3. optional: by `reminder_time ASC`
4. fallback: `created_at DESC`

### Kenapa
Dashboard harus memprioritaskan habit yang masih perlu perhatian.

---

## 6.4 Query strategy recommendation
Gunakan eager loading untuk menghindari N+1:
- load habit aktif
- eager load logs untuk hari ini
- hitung derived field di service / transformer

### Contoh intent
```php
$habits = Habit::query()
    ->where('user_id', $user->id)
    ->active()
    ->with(['logs' => function ($query) {
        $query->whereDate('log_date', today());
    }])
    ->get();
```

### Output target
```json
{
  "id": 12,
  "title": "Minum air putih",
  "frequency": "daily",
  "target_count": 8,
  "reminder_time": "08:00:00",
  "is_completed_today": true,
  "today_log": {
    "id": 100,
    "status": "completed",
    "qty": 8
  }
}
```

---

## 7. Habit Completion Rate Specification

## 7.1 Definisi
Persentase keberhasilan habit selama periode tertentu.

## 7.2 Scope
Bisa dihitung:
- per habit
- per user global
- per range 7 hari / 30 hari

Untuk MVP, yang paling berguna:
- completion rate 7 hari level user
- completion rate 7 hari level habit

---

## 7.3 Completion rate per habit
### Definisi
```text
jumlah hari status completed dalam range / jumlah target hari dalam range × 100
```

### Contoh
Dalam 7 hari:
- habit completed 5 hari
- completion rate = `5 / 7 * 100 = 71.43%`

### Catatan
Untuk `weekly` habit, definisi ini bisa menjadi kompleks.  
Rekomendasi MVP:
- fokus dulu pada habit `daily`
- weekly habit boleh punya completion rate sederhana atau ditunda refinement-nya

### Query intent
```php
$completedDays = HabitLog::query()
    ->where('habit_id', $habit->id)
    ->whereBetween('log_date', [$startDate, $endDate])
    ->where('status', 'completed')
    ->count();

$completionRate = $daysInRange > 0
    ? round(($completedDays / $daysInRange) * 100, 2)
    : 0;
```

---

## 7.4 Completion rate level user
### Definisi
```text
jumlah seluruh completed log dalam range / jumlah seluruh active habit × jumlah hari dalam range × 100
```

### Catatan
Ini cocok bila semua habit harian dianggap target harian.

### Contoh
- active habits = 4
- range = 7 hari
- target total = 28 slot
- completed logs = 20
- rate = `20 / 28 * 100 = 71.43%`

### Acceptance criteria
- hanya habit aktif yang dihitung
- archived habit idealnya tidak ikut target periodik setelah diarsipkan
- jika active habits = 0 → rate = 0, bukan division error

---

## 8. Streak Specification Detail

## 8.1 current_streak_for_habit
### Definisi
Jumlah hari berurutan ke belakang di mana sebuah habit memiliki log `completed`.

### Rule
- cari semua `completed log` untuk habit
- urut descending berdasarkan `log_date`
- hitung berurutan mulai hari ini atau kemarin, sesuai definisi product

### Acceptance criteria
- jika tidak ada log → `0`
- jika hanya ada log acak tidak berurutan → streak berhenti di gap pertama

---

## 8.2 longest_streak_for_habit
### Definisi
Rangkaian completed terpanjang yang pernah dimiliki habit.

### Rule
Hitung longest consecutive sequence dari `completed log_date`.

### Implementasi
Lebih aman dihitung di service, bukan SQL mentah.

### UI use case
- tampil di habit detail
- badge motivasional

---

## 8.3 current_streak_for_user
### Definisi MVP
Jumlah hari berurutan di mana user memiliki setidaknya satu habit completed.

### Catatan
Ini metrik motivasional global, bukan performa detail.

---

## 9. Focus Metrics Specification

## 9.1 total_focus_seconds_today
### Definisi
Sum semua `focused_duration_seconds` hari ini.

### Sumber
`focus_sessions`

---

## 9.2 total_background_seconds_today
### Definisi
Sum semua `unfocused_duration_seconds` hari ini.

### Tujuan
Menunjukkan durasi timer saat app tidak aktif/di background.

---

## 9.3 total_focus_sessions_today
### Definisi
Jumlah sesi fokus hari ini.

### Rule
- `user_id = current user`
- `session_date = today`
- count semua session yang tersimpan

### Catatan
Kalau mau lebih ketat:
- bisa exclude status `running`
- rekomendasi MVP: untuk summary historis, hitung session yang sudah stop saja
- untuk realtime widget, running session bisa diperlakukan terpisah

---

## 9.4 interruption_count_today
### Definisi
Jumlah total interruption hari ini.

### Rule
Sum `interruption_count` dari session hari ini.

### UI use case
- insight fokus
- evaluasi kualitas sesi, bukan hanya durasi

---

## 9.5 focus efficiency
### Definisi
```text
focused_duration_seconds / total_duration_seconds × 100
```

### Rule
Jika `total_duration_seconds = 0` → efficiency = 0

### Contoh
- focused = 1200
- total = 1500
- efficiency = 80%

### UI use case
- habit detail / focus dashboard
- insight kualitas sesi

---

## 10. Notification Metrics Specification

## 10.1 unread_count
Sudah dijelaskan di dashboard metric.

---

## 10.2 notifications_today
### Definisi
Jumlah notifikasi yang dibuat hari ini.

### Rule
- `user_id = current user`
- `whereDate(created_at, today())`

### UI use case
- optional summary
- audit ringan

---

## 10.3 latest_notifications
### Definisi
Daftar notifikasi terbaru user, default 5–10 item.

### Rule
- `latest()`
- `take(limit)`

---

## 10.4 reminder_notifications_today
### Definisi
Jumlah notif type `habit_reminder` hari ini.

### Tujuan
Memeriksa aktivitas cron dan reminder engine.

---

## 11. Weekly Metrics Specification

## 11.1 Range definition
Untuk dashboard mingguan MVP, gunakan:
- 7 hari terakhir termasuk hari ini

Contoh:
- start = today()->subDays(6)
- end = today()

---

## 11.2 daily_completion_series
### Definisi
Series jumlah `completed` per hari selama 7 hari terakhir.

### Output target
```json
[
  { "date": "2026-04-02", "completed_count": 2 },
  { "date": "2026-04-03", "completed_count": 4 },
  { "date": "2026-04-04", "completed_count": 1 }
]
```

### Query strategy
- ambil logs range 7 hari
- group by `log_date`
- isi hari kosong dengan `0` di service layer

### Acceptance criteria
- semua 7 hari harus muncul walau count = 0
- user scope wajib
- hanya status completed yang dihitung bila chart yang ditampilkan adalah completion chart

---

## 11.3 daily_focus_series
### Definisi
Series total focus minutes per hari selama 7 hari terakhir.

### Output target
```json
[
  { "date": "2026-04-02", "focus_minutes": 45 },
  { "date": "2026-04-03", "focus_minutes": 120 }
]
```

### Rule
- group berdasarkan `session_date`
- sum `focused_duration_seconds`
- konversi ke menit
- isi hari kosong dengan 0

---

## 11.4 weekly_completion_rate
### Definisi
Completion rate level user untuk 7 hari terakhir.

### Formula
```text
completed_logs / (active_habits * 7) * 100
```

### Catatan
Jika `active_habits = 0`, return 0.

---

## 12. Query Strategy Recommendations

## 12.1 DashboardStatsService responsibilities
Service ini sebaiknya bertanggung jawab atas:
- total_active_habits
- completed_today
- current_streak
- focus_minutes_today
- unread_notifications
- optional weekly summary

---

## 12.2 Jangan query berulang di Blade
Blade tidak boleh menghitung metrik sendiri.  
Semua angka dan derived fields harus disiapkan di:
- controller
- service
- DTO/resource/transformer jika diperlukan

---

## 12.3 Gunakan eager loading untuk daftar habit
Untuk section habit list / dashboard:
- eager load logs hari ini
- hindari query per item

---

## 12.4 Gunakan service untuk metrik kompleks
Jangan memaksa SQL mentah untuk:
- streak
- filling missing date series
- focus efficiency
- mixed derived fields

Lakukan di service agar logic mudah dibaca dan diubah.

---

## 13. Performance Considerations

## 13.1 MVP assumption
Untuk MVP, data user relatif kecil:
- puluhan habit
- ratusan log
- puluhan focus session per minggu

Jadi query langsung masih aman.

---

## 13.2 Area yang berpotensi berat
- dashboard dengan banyak widget sekaligus
- habit list + today logs + streak per habit
- weekly chart dengan series fill
- notifications polling terlalu sering

---

## 13.3 Optimization path nanti
Jika nanti berat:
- tambahkan caching pendek untuk dashboard
- buat precomputed summary harian
- buat query scope lebih spesifik
- kurangi polling interval
- pagination pada list panjang

---

## 14. Acceptance Criteria Per Metrik

## 14.1 total_active_habits
- hanya hitung habit aktif
- archived tidak dihitung
- user scope benar

## 14.2 completed_today
- hanya status completed
- hanya hari ini
- tidak menghitung user lain

## 14.3 current_streak
- tidak error saat tidak ada log
- hasil konsisten dengan definisi product
- logic terpusat di service

## 14.4 focus_minutes_today
- memakai focused duration saja
- unit output dalam menit
- pembulatan konsisten

## 14.5 unread_notifications
- sinkron dengan is_read
- update langsung saat mark as read / mark all as read

## 14.6 daily_completion_series
- 7 titik data selalu ada
- tanggal terurut ascending
- hari kosong bernilai 0

## 14.7 daily_focus_series
- hari tanpa sesi tetap muncul dengan 0
- fokus diambil dari focused duration, bukan total duration

---

## 15. Example Dashboard Payload

Contoh payload yang sudah siap dipakai UI:
```json
{
  "total_active_habits": 5,
  "completed_today": 3,
  "current_streak": 7,
  "focus_minutes_today": 120,
  "unread_notifications": 2,
  "weekly_completion_rate": 71.43,
  "daily_completion_series": [
    { "date": "2026-04-02", "completed_count": 2 },
    { "date": "2026-04-03", "completed_count": 4 },
    { "date": "2026-04-04", "completed_count": 1 },
    { "date": "2026-04-05", "completed_count": 3 },
    { "date": "2026-04-06", "completed_count": 2 },
    { "date": "2026-04-07", "completed_count": 5 },
    { "date": "2026-04-08", "completed_count": 3 }
  ],
  "daily_focus_series": [
    { "date": "2026-04-02", "focus_minutes": 30 },
    { "date": "2026-04-03", "focus_minutes": 90 },
    { "date": "2026-04-04", "focus_minutes": 20 },
    { "date": "2026-04-05", "focus_minutes": 60 },
    { "date": "2026-04-06", "focus_minutes": 50 },
    { "date": "2026-04-07", "focus_minutes": 130 },
    { "date": "2026-04-08", "focus_minutes": 120 }
  ]
}
```

---

## 16. Task Breakdown Implementasi Query & Metrics

## Fase 1 - Dashboard Base Metrics
- implement total_active_habits
- implement completed_today
- implement focus_minutes_today
- implement unread_notifications

## Fase 2 - Streak
- implement current_streak_for_user
- implement current_streak_for_habit
- implement longest_streak_for_habit

## Fase 3 - Today Habits
- implement eager loaded today habits
- tambahkan `is_completed_today`
- tambahkan `today_log`

## Fase 4 - Weekly Charts
- implement daily_completion_series
- implement daily_focus_series
- isi gap tanggal dengan nol

## Fase 5 - Completion Rate
- implement weekly_completion_rate
- implement per-habit completion rate bila dibutuhkan

## Fase 6 - Optimization
- cek N+1
- tambah scope/query helper bila perlu
- pertimbangkan cache ringan

---

## 17. Checklist Verifikasi

### Dashboard
- semua angka tampil sesuai data DB
- habit archived tidak merusak count
- fokus tampil dalam menit

### Today Habits
- habit aktif muncul semua
- completed hari ini terdeteksi
- log hari ini terbaca benar

### Weekly Charts
- 7 hari selalu muncul
- tanggal urut
- nilai kosong menjadi 0

### Streak
- current streak tidak negatif
- longest streak benar pada data berurutan
- gap hari memutus streak

### Notifications
- unread count sinkron dengan mark-as-read
- latest notification ordering benar

---

## 18. Summary
Dokumen ini menetapkan:
- definisi final metrik dashboard
- aturan derived field
- query summary harian dan mingguan
- definisi streak
- definisi completion rate
- output series untuk chart
- acceptance criteria tiap metrik

Dengan dokumen ini, Codex akan lebih mudah memahami:
- angka apa yang harus dihitung
- field turunan apa yang perlu disiapkan
- query mana yang cukup dengan ORM
- logic mana yang harus berada di service

Langkah dokumentasi berikut yang paling berdampak adalah:
1. **Testing Strategy & Acceptance Criteria Master Doc**
2. **Project Structure & Naming Convention Master Doc**
3. **Implementation Roadmap untuk Codex handoff**
