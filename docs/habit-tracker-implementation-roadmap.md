
# Habit Tracker - Detailed Implementation Roadmap & Build Order

## 1. Tujuan Dokumen
Dokumen ini menetapkan **urutan implementasi yang sangat detail** untuk proyek **Habit Tracker Laravel 12** agar:
- Codex membangun sistem secara bertahap dan terkontrol
- tidak ada lompatan fase yang membuat fondasi kacau
- setiap output fase bisa diverifikasi sebelum lanjut
- dependency antar modul jelas
- tim manusia maupun AI punya build order yang sama

Dokumen ini harus dibaca bersama:
- Codex Handoff Instruction Document
- Project Structure & Naming Convention Master Doc
- Use Case & Behavioral Specification
- API Contract & Response Schema Specification
- Query & Metrics Specification
- Testing Strategy & Acceptance Criteria Master Document

Dokumen ini juga tetap selaras dengan workflow frontend modern berbasis `resources/`, Vite, dan asset build terstruktur. fileciteturn0file0  
Arah UI juga tetap mengikuti referensi visual hangat-editorial yang kamu upload. fileciteturn1file0

---

## 2. Prinsip Build Order

## 2.1 Fondasi dulu, fitur belakangan
Jangan mulai dari:
- dashboard kompleks
- polling notification
- chart
- efek UI

Mulai dari:
- struktur project
- migration
- model
- request validation
- services
- controller flow dasar

---

## 2.2 Setiap fase harus “usable”
Idealnya setiap fase menghasilkan sesuatu yang:
- bisa dijalankan
- bisa dites
- punya output nyata

Contoh:
- setelah fase migration → DB siap
- setelah fase habit CRUD → habit bisa dikelola
- setelah fase focus session → timer API bisa dipakai
- setelah fase reminder → notif bisa muncul lewat cron

---

## 2.3 Jangan implement semua sekaligus
Codex harus bekerja secara bertahap:
1. fondasi
2. domain
3. interaction
4. background jobs
5. UI polish
6. testing & hardening

---

## 2.4 Jangan mengoptimasi terlalu cepat
Untuk MVP:
- jangan tambahkan cache kompleks terlalu awal
- jangan tambah repository layer
- jangan buat abstraction yang belum dipakai

Fokus:
- benar dulu
- konsisten dulu
- testable dulu

---

## 3. Roadmap Global

Roadmap final dibagi menjadi:

1. Phase 0 — Project Bootstrap
2. Phase 1 — Database Foundation
3. Phase 2 — Model & Relationship Layer
4. Phase 3 — Validation & Response Foundation
5. Phase 4 — Habit Core Module
6. Phase 5 — Habit Logging Module
7. Phase 6 — Dashboard Metrics Foundation
8. Phase 7 — Focus Session Module
9. Phase 8 — Notification Module
10. Phase 9 — Cron Reminder Engine
11. Phase 10 — UI Foundation
12. Phase 11 — Feature UI Pages
13. Phase 12 — JavaScript Enhancement
14. Phase 13 — Testing & Verification
15. Phase 14 — Pre-Deployment Hardening
16. Phase 15 — Codex Handoff Completion Checklist

---

## 4. Phase 0 — Project Bootstrap

## 4.1 Tujuan
Menyiapkan skeleton Laravel 12 yang sesuai dengan standar proyek.

## 4.2 Output fase
- Laravel project siap jalan
- Vite + Tailwind siap
- struktur folder sesuai blueprint
- auth foundation terpilih
- config awal aman untuk development

## 4.3 Task detail
1. pastikan Laravel 12 + PHP 8.2
2. siapkan `.env`
3. setup DB connection
4. setup Vite
5. setup Tailwind
6. buat struktur folder awal:
   - `app/Services`
   - `app/Http/Requests/...`
   - `resources/views/...`
   - `resources/js/modules`
7. setup auth scaffolding (misalnya Breeze bila dipilih)
8. pastikan `@vite()` sudah dipakai di layout
9. siapkan `resources/css/app.css`
10. siapkan `resources/js/app.js`

## 4.4 Verification checklist
- `php artisan serve` jalan
- `npm run dev` jalan
- auth page tampil
- Tailwind aktif
- route dasar bisa diakses

## 4.5 Jangan lakukan dulu
- jangan buat business logic besar
- jangan buat dashboard penuh
- jangan buat cron logic dulu

---

## 5. Phase 1 — Database Foundation

## 5.1 Tujuan
Membangun tabel inti yang stabil untuk seluruh sistem.

## 5.2 Output fase
Tabel siap:
- users
- habits
- habit_logs
- focus_sessions
- notifications

## 5.3 Task detail
1. buat migration `habits`
2. buat migration `habit_logs`
3. buat migration `focus_sessions`
4. buat migration `notifications`
5. tambahkan foreign key
6. tambahkan index yang dibutuhkan
7. tambahkan unique constraint untuk `habit_id + log_date`
8. review kolom agar sesuai blueprint
9. jalankan migrate

## 5.4 Verification checklist
- semua tabel berhasil dibuat
- semua foreign key valid
- unique constraint untuk habit log aktif
- struktur sesuai dokumen migration & model

## 5.5 Dependencies
Phase ini bergantung pada:
- Phase 0 selesai

---

## 6. Phase 2 — Model & Relationship Layer

## 6.1 Tujuan
Membangun model Eloquent dan relasi yang menjadi basis semua query.

## 6.2 Output fase
Model siap:
- `Habit`
- `HabitLog`
- `FocusSession`
- `UserNotification`

## 6.3 Task detail
1. buat model `Habit`
2. tambahkan `fillable`
3. tambahkan `casts`
4. tambahkan relasi `user()` dan `logs()`
5. buat scope `active()`
6. buat model `HabitLog`
7. tambahkan relasi `habit()` dan `user()`
8. buat model `FocusSession`
9. tambahkan relasi `habit()` dan `user()`
10. buat model `UserNotification`
11. map ke tabel `notifications`
12. tambahkan relasi `user()`
13. tambahkan relasi di `User` model:
   - `habits()`
   - `habitLogs()`
   - `focusSessions()`
   - `notifications()`

## 6.4 Verification checklist
- relasi berjalan di Tinker
- model create/update berhasil
- `UserNotification` tidak bentrok dengan Notification bawaan Laravel
- scope `active()` bekerja

## 6.5 Dependencies
- Phase 1 selesai

---

## 7. Phase 3 — Validation & Response Foundation

## 7.1 Tujuan
Membangun fondasi request validation dan JSON response yang konsisten.

## 7.2 Output fase
- semua Form Request dasar siap
- trait/helper response siap
- error/success schema konsisten

## 7.3 Task detail
1. buat `StoreHabitRequest`
2. buat `UpdateHabitRequest`
3. buat `StoreHabitLogRequest`
4. buat `UpdateHabitLogRequest`
5. buat `StartFocusSessionRequest`
6. buat `StopFocusSessionRequest`
7. buat `MarkNotificationAsReadRequest`
8. buat `ApiResponse` trait
9. definisikan helper:
   - `successResponse()`
   - `errorResponse()`
10. pastikan JSON error schema konsisten
11. pastikan ownership check dilakukan di `authorize()` bila relevan

## 7.4 Verification checklist
- invalid payload return 422
- forbidden ownership return 403
- success JSON mengikuti contract
- semua request class berada di folder domain yang benar

## 7.5 Dependencies
- Phase 2 selesai

---

## 8. Phase 4 — Habit Core Module

## 8.1 Tujuan
Menyelesaikan CRUD habit sebagai modul domain pertama.

## 8.2 Output fase
User bisa:
- melihat daftar habit
- membuat habit
- mengedit habit
- menghapus/arsipkan habit

## 8.3 Task detail
1. buat `HabitService`
2. implement:
   - `createForUser`
   - `updateForUser`
   - `archiveForUser`
   - `toggleActiveForUser`
3. buat `HabitController`
4. implement:
   - `index`
   - `create`
   - `store`
   - `edit`
   - `update`
   - `destroy`
   - `archive` optional
5. buat route `habits.*`
6. siapkan view dasar:
   - `habits/index`
   - `habits/create`
   - `habits/edit`
7. tambahkan flash success/error message
8. pastikan ownership rule berjalan

## 8.4 Verification checklist
- user bisa buat habit
- user bisa edit habit sendiri
- user tidak bisa edit habit milik user lain
- archive mengubah `is_active` + `archived_at`
- daftar habit hanya menampilkan habit milik user

## 8.5 Dependencies
- Phase 3 selesai

---

## 9. Phase 5 — Habit Logging Module

## 9.1 Tujuan
Menangani check-in dan log habit harian dengan aturan anti-duplikasi.

## 9.2 Output fase
- habit bisa di-log per hari
- quick check-in bisa create/update log
- duplicate per tanggal dicegah

## 9.3 Task detail
1. buat `HabitLogService`
2. implement:
   - `storeForUser`
   - `updateForUser`
   - `deleteForUser`
3. gunakan `updateOrCreate()` untuk one-log-per-day
4. buat `HabitLogController`
5. implement:
   - `store`
   - `update`
   - `quickCheckin`
6. buat endpoint:
   - `/habit-logs`
   - `/ajax/habit-logs/quick-checkin`
7. pastikan `log_date = today()` untuk quick check-in
8. buat partial/button di UI untuk check-in cepat
9. sambungkan ke API contract

## 9.4 Verification checklist
- request pertama membuat log
- request kedua di hari yang sama mengupdate log
- tidak ada record duplikat
- qty dan status tersimpan benar
- resource ownership valid

## 9.5 Dependencies
- Phase 4 selesai

---

## 10. Phase 6 — Dashboard Metrics Foundation

## 10.1 Tujuan
Menyediakan angka dashboard yang konsisten dan siap dipakai UI.

## 10.2 Output fase
Dashboard bisa menampilkan:
- total_active_habits
- completed_today
- current_streak
- focus_minutes_today
- unread_notifications

## 10.3 Task detail
1. buat `StreakService`
2. implement:
   - `getCurrentStreakForHabit`
   - `getLongestStreakForHabit`
   - `getCurrentStreakForUser`
3. buat `DashboardStatsService`
4. implement `getForUser`
5. buat query:
   - total active habits
   - completed today
   - focus minutes today
   - unread notifications
6. buat `DashboardController@index`
7. siapkan payload summary untuk view
8. optional:
   - route async `/ajax/dashboard/summary`

## 10.4 Verification checklist
- semua angka sesuai data DB
- archived habit tidak ikut active metrics
- streak tidak error saat data kosong
- payload dashboard sesuai query spec

## 10.5 Dependencies
- Phase 5 selesai

---

## 11. Phase 7 — Focus Session Module

## 11.1 Tujuan
Membangun timer dan domain logic sesi fokus.

## 11.2 Output fase
User bisa:
- memulai sesi fokus
- menghentikan sesi fokus
- melihat ringkasan sesi
- hanya punya satu session running

## 11.3 Task detail
1. buat `FocusSessionService`
2. implement:
   - `start`
   - `stop`
   - `cancel` optional
   - `getTodaySummary` optional
3. enforce one-running-session rule
4. buat `FocusSessionController`
5. implement:
   - `index`
   - `start`
   - `stop`
6. buat endpoint:
   - `/ajax/focus-sessions/start`
   - `/ajax/focus-sessions/{focusSession}/stop`
7. kembalikan response JSON sesuai contract
8. siapkan view `focus-sessions/index`
9. siapkan placeholder UI timer

## 11.4 Verification checklist
- start membuat sesi baru bila tidak ada running session
- start mengembalikan sesi existing bila sudah ada running
- stop gagal bila status bukan running
- `total_duration_seconds = focused + unfocused`
- interruption tersimpan

## 11.5 Dependencies
- Phase 6 selesai

---

## 12. Phase 8 — Notification Module

## 12.1 Tujuan
Menyediakan list notif, unread badge, dan aksi mark-as-read.

## 12.2 Output fase
User bisa:
- melihat daftar notif
- melihat unread count
- mark single notif as read
- mark all notif as read

## 12.3 Task detail
1. buat `NotificationService`
2. implement:
   - `createForUser`
   - `markAsRead`
   - `markAllAsRead`
   - `getLatestForUser`
   - `getUnreadCount`
3. buat `NotificationController`
4. implement:
   - `index`
   - `list`
   - `markAsRead`
   - `markAllAsRead`
5. buat route:
   - `/notifications`
   - `/notifications/{notification}/read`
   - `/notifications/read-all`
   - `/ajax/notifications`
6. siapkan view `notifications/index`
7. siapkan unread badge di navbar

## 12.4 Verification checklist
- notif list hanya menampilkan notif user sendiri
- unread count benar
- mark as read mengubah `is_read` dan `read_at`
- mark all as read mengubah semua notif unread milik user

## 12.5 Dependencies
- Phase 7 selesai

---

## 13. Phase 9 — Cron Reminder Engine

## 13.1 Tujuan
Membangun reminder berbasis scheduler dan cron.

## 13.2 Output fase
- command `habit:check-reminders` siap
- scheduler siap
- duplicate reminder dicegah
- habit completed tidak dikirimi reminder

## 13.3 Task detail
1. buat `HabitReminderService`
2. implement:
   - `run`
   - `processHabit`
   - `shouldSendReminder`
3. tambahkan tolerance waktu ±1 menit
4. cek duplicate notif berdasarkan:
   - user_id
   - type
   - habit_id dalam payload/data
   - tanggal sama
5. buat command `CheckHabitReminders`
6. inject `HabitReminderService`
7. return summary:
   - processed
   - created
   - skipped
8. daftarkan di `Kernel`
9. gunakan `everyMinute()`
10. optional: `withoutOverlapping()`

## 13.4 Verification checklist
- reminder dibuat untuk habit aktif yang eligible
- habit completed hari ini tidak dikirimi notif
- notif tidak dobel saat command jalan 2x
- command manual berhasil
- scheduler manual berhasil

## 13.5 Dependencies
- Phase 8 selesai

---

## 14. Phase 10 — UI Foundation

## 14.1 Tujuan
Membangun layout, partial, komponen dasar, dan design tokens.

## 14.2 Output fase
- layout app
- layout guest
- navbar
- page header
- flash message
- button/card components
- Tailwind theme foundation

## 14.3 Task detail
1. setup warna custom di `tailwind.config.js`
2. setup font family
3. setup shadow/radius custom
4. buat `layouts/app.blade.php`
5. buat `layouts/guest.blade.php`
6. buat partial:
   - head
   - navbar
   - flash-message
   - page-header
7. buat Blade component:
   - button
   - card
   - metric-card
   - habit-card
   - notification-item
   - empty-state
8. rapikan `resources/css/app.css`
9. terapkan visual direction warm-editorial

## 14.4 Verification checklist
- layout tampil konsisten
- Tailwind color tokens terpakai
- navbar muncul di halaman auth/non-auth sesuai layout
- button/card component reusable

## 14.5 Dependencies
- Phase 4–9 tidak wajib selesai total, tapi minimal fondasi route dan auth sudah ada

---

## 15. Phase 11 — Feature UI Pages

## 15.1 Tujuan
Menyelesaikan halaman UI utama agar usable end-to-end.

## 15.2 Output fase
Halaman siap:
- dashboard
- habits index/create/edit/show
- focus sessions
- notifications
- auth pages

## 15.3 Task detail
1. implement dashboard page:
   - page header
   - metrics grid
   - today habits
   - notifications preview
   - focus preview
2. implement habits index
3. implement create habit page
4. implement edit habit page
5. implement optional habit detail page
6. implement focus session page
7. implement notifications page
8. implement empty states
9. implement responsive refinement

## 15.4 Verification checklist
- semua halaman bisa dibuka
- data tampil sesuai service/controller payload
- empty state tampil saat data kosong
- visual sesuai design direction
- layout tidak terasa seperti dashboard korporat dingin

## 15.5 Dependencies
- Phase 10 selesai
- modul backend inti sudah tersedia

---

## 16. Phase 12 — JavaScript Enhancement

## 16.1 Tujuan
Menambahkan enhancement frontend untuk interaksi yang tidak perlu full reload.

## 16.2 Output fase
JS modules siap:
- focus timer
- notification polling
- quick check-in

## 16.3 Task detail
1. buat `resources/js/utils/api.js`
2. buat `resources/js/utils/dom.js`
3. buat `resources/js/modules/focus-timer.js`
4. implement:
   - start request
   - stop request
   - local timer display
   - visibility change handling
   - focused vs unfocused accumulation
5. buat `resources/js/modules/notifications.js`
6. implement:
   - polling latest notifications
   - update unread badge
7. buat `resources/js/modules/quick-checkin.js`
8. implement:
   - AJAX quick check-in
   - update card state
9. import modules di `app.js`

## 16.4 Verification checklist
- focus timer start/stop berfungsi
- notifikasi polling tidak error
- unread badge update
- quick check-in update UI dengan benar
- JS enhancement tidak merusak fallback Blade flow

## 16.5 Dependencies
- Phase 11 selesai atau minimal halaman dan endpoint sudah tersedia

---

## 17. Phase 13 — Testing & Verification

## 17.1 Tujuan
Menyelaraskan code dengan acceptance criteria dan memastikan semua critical flow aman.

## 17.2 Output fase
- unit tests service inti
- feature tests flow penting
- API contract tests dasar
- manual QA checklist tervalidasi

## 17.3 Task detail
1. buat unit test:
   - HabitServiceTest
   - HabitLogServiceTest
   - FocusSessionServiceTest
   - NotificationServiceTest
   - HabitReminderServiceTest
   - DashboardStatsServiceTest
   - StreakServiceTest
2. buat feature tests:
   - CreateHabitTest
   - UpdateHabitTest
   - ArchiveHabitTest
   - QuickCheckinTest
   - StartFocusSessionTest
   - StopFocusSessionTest
   - ListNotificationsTest
   - MarkNotificationReadTest
   - DashboardMetricsTest
3. validasi JSON envelope
4. validasi forbidden ownership
5. validasi duplicate prevention
6. validasi query metrics

## 17.4 Verification checklist
- core tests pass
- habit log duplicate prevention pass
- focus single-running rule pass
- reminder duplicate prevention pass
- dashboard metrics akurat
- API contract sesuai dokumen

## 17.5 Dependencies
- seluruh backend utama sudah tersedia

---

## 18. Phase 14 — Pre-Deployment Hardening

## 18.1 Tujuan
Menyiapkan sistem untuk deploy ke shared hosting dengan risiko minimal.

## 18.2 Output fase
- app production-ready dasar
- cron setup diketahui
- asset build siap
- env checklist jelas

## 18.3 Task detail
1. jalankan `npm run build`
2. pastikan `public/build` siap
3. review `APP_ENV`, `APP_DEBUG`, `APP_URL`
4. review DB config
5. review writable storage
6. review scheduler command
7. siapkan cron command hosting:
   - `php /path/to/artisan schedule:run`
8. test command manual di production-like environment
9. review notification polling interval
10. review query dasar agar tidak boros

## 18.4 Verification checklist
- asset build termuat
- scheduler command diketahui
- app tidak bergantung pada websocket/reverb
- reminder flow siap diuji setelah deploy
- halaman inti tetap bekerja saat JS enhancement gagal

## 18.5 Dependencies
- Phase 13 selesai atau minimal stabil secara fungsional

---

## 19. Phase 15 — Codex Handoff Completion Checklist

## 19.1 Tujuan
Memastikan semua materi siap dilempar ke Codex tanpa ambiguity besar.

## 19.2 Output fase
- package dokumentasi final
- urutan kerja Codex jelas
- file prioritas jelas
- scope MVP terkunci

## 19.3 Checklist
Codex handoff dianggap siap jika:
- ada handoff doc
- ada project structure doc
- ada migration/model doc
- ada request/controller doc
- ada service/business flow doc
- ada API contract doc
- ada query & metrics doc
- ada testing doc
- ada UI design doc
- ada wireframe doc
- ada implementation roadmap doc ini

## 19.4 Rekomendasi handoff strategy
### Mode terbaik
Lempar ke Codex per fase:
1. foundation
2. database + model
3. services + controllers
4. UI
5. cron reminder
6. tests

### Kenapa
- lebih mudah dikontrol
- lebih mudah review
- lebih kecil risiko divergence

---

## 20. Deliverable Checklist Per Fase

## Phase 0 deliverables
- Laravel app bootable
- Tailwind/Vite configured
- auth foundation ready

## Phase 1 deliverables
- migrations completed
- database migrated

## Phase 2 deliverables
- models + relationships completed

## Phase 3 deliverables
- form requests completed
- API response helper ready

## Phase 4 deliverables
- habit CRUD backend completed
- habit basic views completed

## Phase 5 deliverables
- habit logging backend completed
- quick check-in endpoint ready

## Phase 6 deliverables
- dashboard stats service completed
- dashboard summary endpoint or payload ready

## Phase 7 deliverables
- focus session backend ready
- focus start/stop endpoints ready

## Phase 8 deliverables
- notification backend ready
- list/read/read-all endpoints ready

## Phase 9 deliverables
- reminder service completed
- command completed
- scheduler wired

## Phase 10 deliverables
- layout system completed
- shared UI components ready

## Phase 11 deliverables
- all main pages implemented

## Phase 12 deliverables
- JS modules implemented

## Phase 13 deliverables
- core tests implemented and passing

## Phase 14 deliverables
- build ready
- cron-ready deployment notes verified

## Phase 15 deliverables
- final documentation package ready for Codex

---

## 21. Rules for Stopping a Phase

Codex or developer should stop and ask for review when:
- a phase changes architecture unexpectedly
- a rule conflict appears between docs
- a module requires a product decision not yet documented
- implementation starts requiring non-MVP abstractions
- a test fails because behavior spec is ambiguous

Jika tidak ada hal-hal di atas, lanjutkan fase berikutnya.

---

## 22. Suggested Codex Execution Prompt for This Roadmap

```text
Implement this Laravel 12 Habit Tracker project phase by phase.

Follow the Detailed Implementation Roadmap exactly.
Do not skip phases.
At the end of each phase:
1. summarize what was implemented,
2. list created/updated files,
3. mention any assumptions,
4. stop before moving to the next phase unless instructed.

Prioritize:
- correctness,
- consistency with documentation,
- Laravel conventions,
- thin controllers,
- service-based business logic,
- Blade-first UI.

Do not introduce:
- Reverb,
- WebSocket,
- queue worker dependency,
- repository abstraction,
- SPA architecture.
```

---

## 23. Summary
Dokumen ini menetapkan:
- urutan implementasi yang sangat detail
- dependency antar fase
- deliverable per fase
- verification checklist per fase
- strategi handoff Codex yang aman

Dengan dokumen ini, Codex tidak hanya tahu **apa** yang harus dibangun, tapi juga **kapan** dan **dalam urutan apa** semuanya harus dibangun.

Ini mengurangi risiko:
- lompatan fase
- naming kacau
- fondasi bocor
- fitur setengah jadi
- codebase sulit dirawat
