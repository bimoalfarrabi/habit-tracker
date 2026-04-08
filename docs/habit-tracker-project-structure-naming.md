
# Habit Tracker - Project Structure & Naming Convention Master Document

## 1. Tujuan
Dokumen ini menetapkan standar final untuk:
- struktur folder project Laravel 12
- penamaan file, class, method, route, dan view
- boundary antar layer
- aturan organisasi codebase
- dependency map utama
- coding convention yang stabil untuk handoff ke Codex

Dokumen ini dibuat agar:
- struktur project konsisten sejak awal
- Codex menghasilkan file di lokasi yang benar
- naming tidak berubah-ubah di tengah implementasi
- developer manusia dan AI punya acuan yang sama
- maintainability project tetap tinggi saat fitur bertambah

Dokumen ini mengikuti arah stack Laravel 12, PHP 8.2, Vite, Tailwind, workflow frontend modern berbasis `resources/`, dan output asset build yang rapi. fileciteturn0file0

Arah UI dan naming visual juga tetap selaras dengan dokumentasi desain hangat-editorial yang terinspirasi dari dokumen desain referensi. fileciteturn1file0

---

## 2. Prinsip Umum Struktur Project

## 2.1 Laravel-first, bukan custom framework
Struktur project harus mengikuti konvensi Laravel sejauh mungkin.  
Jangan membuat struktur terlalu “unik” jika tidak benar-benar perlu.

Tujuan:
- onboarding lebih cepat
- package ecosystem tetap kompatibel
- Codex lebih mudah menebak lokasi file
- developer Laravel lain cepat memahami project

---

## 2.2 Blade-first architecture
Karena aplikasi ini:
- server-rendered
- shared-hosting friendly
- AJAX hanya untuk interaksi tertentu

maka struktur frontend tetap mengutamakan:
- Blade views
- partials / components
- Vite untuk asset bundling
- Tailwind untuk styling

Bukan SPA penuh.

---

## 2.3 Service-oriented business logic
Business logic penting tidak boleh menumpuk di:
- controller
- model
- Blade

Semua logic inti ditempatkan pada:
- `app/Services`

---

## 2.4 Naming harus eksplisit
Hindari nama generik seperti:
- `HelperService`
- `DataManager`
- `CommonController`
- `ProcessHandler`

Gunakan nama yang:
- domain-specific
- mudah dicari
- langsung menjelaskan tanggung jawab

Contoh baik:
- `HabitLogService`
- `DashboardStatsService`
- `FocusSessionController`
- `StoreHabitRequest`

---

## 3. Struktur Folder Final

## 3.1 Root structure
```text
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
vite.config.js
package.json
composer.json
```

Ini mengikuti workflow frontend modern berbasis asset bundling dan pemisahan resource/build yang sudah dijelaskan pada guide yang kamu upload. fileciteturn0file0

---

## 3.2 App folder structure
```text
app/
├── Console/
│   ├── Commands/
│   │   └── CheckHabitReminders.php
│   └── Kernel.php
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── HabitController.php
│   │   ├── HabitLogController.php
│   │   ├── FocusSessionController.php
│   │   └── NotificationController.php
│   ├── Requests/
│   │   ├── Habit/
│   │   │   ├── StoreHabitRequest.php
│   │   │   └── UpdateHabitRequest.php
│   │   ├── HabitLog/
│   │   │   ├── StoreHabitLogRequest.php
│   │   │   └── UpdateHabitLogRequest.php
│   │   ├── FocusSession/
│   │   │   ├── StartFocusSessionRequest.php
│   │   │   └── StopFocusSessionRequest.php
│   │   └── Notification/
│   │       └── MarkNotificationAsReadRequest.php
│   ├── Middleware/
│   └── Resources/
│       ├── HabitResource.php
│       ├── HabitLogResource.php
│       ├── FocusSessionResource.php
│       └── UserNotificationResource.php
├── Models/
│   ├── Habit.php
│   ├── HabitLog.php
│   ├── FocusSession.php
│   ├── User.php
│   └── UserNotification.php
├── Services/
│   ├── DashboardStatsService.php
│   ├── FocusSessionService.php
│   ├── HabitLogService.php
│   ├── HabitReminderService.php
│   ├── HabitService.php
│   ├── NotificationService.php
│   └── StreakService.php
├── Support/
│   ├── Concerns/
│   │   └── ApiResponse.php
│   ├── Enums/
│   ├── Helpers/
│   └── ValueObjects/
└── View/
    └── Components/
```

---

## 3.3 Resources folder structure
```text
resources/
├── css/
│   └── app.css
├── js/
│   ├── app.js
│   ├── modules/
│   │   ├── focus-timer.js
│   │   ├── notifications.js
│   │   └── quick-checkin.js
│   └── utils/
│       ├── api.js
│       └── dom.js
└── views/
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── components/
    │   ├── button.blade.php
    │   ├── card.blade.php
    │   ├── empty-state.blade.php
    │   ├── habit-card.blade.php
    │   ├── metric-card.blade.php
    │   └── notification-item.blade.php
    ├── dashboard/
    │   └── index.blade.php
    ├── focus-sessions/
    │   └── index.blade.php
    ├── habits/
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   ├── index.blade.php
    │   └── show.blade.php
    ├── layouts/
    │   ├── app.blade.php
    │   ├── guest.blade.php
    │   └── partials/
    │       ├── flash-message.blade.php
    │       ├── footer.blade.php
    │       ├── head.blade.php
    │       ├── navbar.blade.php
    │       └── page-header.blade.php
    └── notifications/
        └── index.blade.php
```

---

## 3.4 Database folder structure
```text
database/
├── factories/
│   ├── HabitFactory.php
│   ├── HabitLogFactory.php
│   ├── FocusSessionFactory.php
│   └── UserNotificationFactory.php
├── migrations/
│   ├── xxxx_xx_xx_xxxxxx_create_habits_table.php
│   ├── xxxx_xx_xx_xxxxxx_create_habit_logs_table.php
│   ├── xxxx_xx_xx_xxxxxx_create_focus_sessions_table.php
│   └── xxxx_xx_xx_xxxxxx_create_notifications_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── DemoHabitSeeder.php
    └── DemoFocusSeeder.php
```

---

## 3.5 Tests folder structure
```text
tests/
├── Feature/
│   ├── Auth/
│   ├── Dashboard/
│   │   └── DashboardMetricsTest.php
│   ├── Habits/
│   │   ├── CreateHabitTest.php
│   │   ├── UpdateHabitTest.php
│   │   └── ArchiveHabitTest.php
│   ├── HabitLogs/
│   │   ├── QuickCheckinTest.php
│   │   └── UpdateHabitLogTest.php
│   ├── FocusSessions/
│   │   ├── StartFocusSessionTest.php
│   │   └── StopFocusSessionTest.php
│   └── Notifications/
│       ├── ListNotificationsTest.php
│       └── MarkNotificationReadTest.php
└── Unit/
    ├── Services/
    │   ├── DashboardStatsServiceTest.php
    │   ├── FocusSessionServiceTest.php
    │   ├── HabitLogServiceTest.php
    │   ├── HabitReminderServiceTest.php
    │   ├── HabitServiceTest.php
    │   ├── NotificationServiceTest.php
    │   └── StreakServiceTest.php
    └── Support/
```

---

## 4. Naming Convention - Models

## 4.1 Rule umum
- gunakan singular PascalCase
- nama model harus merepresentasikan entitas domain
- nama tabel tetap plural snake_case sesuai Laravel convention

### Contoh
| Model | Table |
|------|-------|
| User | users |
| Habit | habits |
| HabitLog | habit_logs |
| FocusSession | focus_sessions |
| UserNotification | notifications |

---

## 4.2 Kenapa `UserNotification`
Laravel punya konsep Notification bawaan.  
Agar tidak bentrok secara naming dan konteks, model tabel `notifications` harus bernama:

- `UserNotification`

### Rule final
- class model: `UserNotification`
- table: `notifications`

---

## 4.3 Relasi naming
Gunakan nama relasi deskriptif dan natural.

### Di `User`
- `habits()`
- `habitLogs()`
- `focusSessions()`
- `notifications()`

### Di `Habit`
- `user()`
- `logs()`

### Di `HabitLog`
- `user()`
- `habit()`

### Di `FocusSession`
- `user()`
- `habit()`

### Di `UserNotification`
- `user()`

---

## 5. Naming Convention - Controllers

## 5.1 Rule umum
- gunakan singular domain + `Controller`
- satu controller untuk satu bounded module
- controller menyesuaikan use case utama, bukan entity dump

### Final controller list
- `DashboardController`
- `HabitController`
- `HabitLogController`
- `FocusSessionController`
- `NotificationController`

---

## 5.2 Naming action method
Gunakan nama konvensional Laravel bila sesuai:
- `index`
- `create`
- `store`
- `show`
- `edit`
- `update`
- `destroy`

Gunakan nama eksplisit untuk custom actions:
- `archive`
- `toggleActive`
- `start`
- `stop`
- `list`
- `markAsRead`
- `markAllAsRead`

### Contoh
| Controller | Method |
|-----------|--------|
| HabitController | `index`, `create`, `store`, `edit`, `update`, `destroy`, `archive`, `toggleActive` |
| HabitLogController | `store`, `update`, `quickCheckin` |
| FocusSessionController | `index`, `start`, `stop` |
| NotificationController | `index`, `list`, `markAsRead`, `markAllAsRead` |

---

## 5.3 Hal yang dilarang
Jangan gunakan method seperti:
- `doStore`
- `saveData`
- `process`
- `runAction`
- `handleThing`

Alasan:
- terlalu generik
- tidak mudah dibaca
- sulit dipetakan ke route dan use case

---

## 6. Naming Convention - Form Request

## 6.1 Rule umum
Gunakan format:
- `Verb + Domain + Request`

### Contoh
- `StoreHabitRequest`
- `UpdateHabitRequest`
- `StoreHabitLogRequest`
- `UpdateHabitLogRequest`
- `StartFocusSessionRequest`
- `StopFocusSessionRequest`
- `MarkNotificationAsReadRequest`

---

## 6.2 Folder grouping
Group berdasarkan domain:
```text
app/Http/Requests/Habit/
app/Http/Requests/HabitLog/
app/Http/Requests/FocusSession/
app/Http/Requests/Notification/
```

### Kenapa
- mudah ditemukan
- scalable
- mengurangi tumpukan file di satu folder

---

## 7. Naming Convention - Services

## 7.1 Rule umum
Gunakan format:
- `Domain + Service`

### Final service names
- `HabitService`
- `HabitLogService`
- `FocusSessionService`
- `NotificationService`
- `HabitReminderService`
- `DashboardStatsService`
- `StreakService`

---

## 7.2 Tanggung jawab per service
### `HabitService`
- create/update/archive/toggle habit

### `HabitLogService`
- create/update log harian
- prevent duplicate log

### `FocusSessionService`
- start/stop/cancel focus session
- enforce one running session

### `NotificationService`
- create notif
- mark read
- unread count
- latest notifications

### `HabitReminderService`
- cron reminder engine
- reminder eligibility
- duplicate prevention reminder

### `DashboardStatsService`
- dashboard metrics aggregation
- today/weekly summary

### `StreakService`
- current streak
- longest streak
- streak helper logic

---

## 7.3 Rule method naming pada service
Gunakan nama yang:
- eksplisit
- domain-driven
- menunjukkan actor dan intent

### Contoh final
- `createForUser(User $user, array $data)`
- `updateForUser(User $user, Habit $habit, array $data)`
- `archiveForUser(User $user, Habit $habit)`
- `storeForUser(User $user, array $data)`
- `start(User $user, array $data)`
- `stop(User $user, FocusSession $session, array $data)`
- `getForUser(User $user)`
- `getUnreadCount(User $user)`
- `markAsRead(UserNotification $notification)`
- `run()`
- `processHabit(Habit $habit)`

### Hindari
- `handle`
- `execute`
- `processData`
- `runTask`
kecuali untuk case yang memang tidak punya nama domain yang lebih tepat

---

## 8. Naming Convention - Routes

## 8.1 Route path
Gunakan:
- lowercase
- kebab-case untuk multi-word path
- plural untuk resource collection
- prefix `ajax/` untuk endpoint JSON internal non-public

### Final route examples
```text
/dashboard
/habits
/habits/create
/habits/{habit}/edit
/habit-logs
/notifications
/notifications/{notification}/read
/notifications/read-all

/ajax/focus-sessions/start
/ajax/focus-sessions/{focusSession}/stop
/ajax/notifications
/ajax/habit-logs/quick-checkin
/ajax/dashboard/summary
```

---

## 8.2 Route name
Gunakan:
- dot notation
- plural resource naming
- action terakhir eksplisit

### Final route naming examples
```php
dashboard
habits.index
habits.create
habits.store
habits.edit
habits.update
habits.destroy
habits.archive
habits.toggle-active

habit-logs.store
habit-logs.update
habit-logs.quick-checkin

notifications.index
notifications.read
notifications.read-all

ajax.notifications.list
ajax.focus-sessions.start
ajax.focus-sessions.stop
ajax.dashboard.summary
```

---

## 8.3 Route model binding parameter
Gunakan nama singular camelCase sesuai model:
- `{habit}`
- `{habitLog}`
- `{focusSession}`
- `{notification}`

Jangan gunakan:
- `{id}`
- `{habit_id}`
di route utama yang memakai model binding Laravel

---

## 9. Naming Convention - Views & Blade Components

## 9.1 View file naming
Gunakan:
- lowercase
- kebab-case hanya bila perlu
- file sederhana seperti `index.blade.php`, `create.blade.php`, `edit.blade.php`

### Contoh
```text
resources/views/dashboard/index.blade.php
resources/views/habits/index.blade.php
resources/views/habits/create.blade.php
resources/views/focus-sessions/index.blade.php
resources/views/notifications/index.blade.php
```

---

## 9.2 Layout naming
Gunakan:
- `layouts/app.blade.php`
- `layouts/guest.blade.php`

Partial:
- `layouts/partials/navbar.blade.php`
- `layouts/partials/flash-message.blade.php`
- `layouts/partials/page-header.blade.php`

---

## 9.3 Component naming
Gunakan nama generik tapi jelas.

### Final component list
- `button`
- `card`
- `metric-card`
- `habit-card`
- `notification-item`
- `empty-state`

### Blade usage
```blade
<x-button variant="primary">Simpan</x-button>
<x-card>
  ...
</x-card>
<x-metric-card label="Completed Today" value="3" />
```

---

## 10. Naming Convention - JavaScript Modules

## 10.1 File naming
Gunakan:
- lowercase
- kebab-case
- domain-based

### Final JS modules
- `focus-timer.js`
- `notifications.js`
- `quick-checkin.js`
- `api.js`
- `dom.js`

---

## 10.2 JS function naming
Gunakan camelCase.

### Contoh
- `startFocusSession()`
- `stopFocusSession()`
- `fetchNotifications()`
- `updateNotificationBadge()`
- `submitQuickCheckin()`
- `renderTimerState()`

---

## 10.3 Data attribute naming
Gunakan format:
- `data-*` kebab-case

### Contoh
- `data-habit-id`
- `data-focus-session-id`
- `data-notification-id`
- `data-action`

---

## 11. Naming Convention - Database

## 11.1 Tabel
Gunakan plural snake_case.

### Final table names
- `users`
- `habits`
- `habit_logs`
- `focus_sessions`
- `notifications`

---

## 11.2 Column naming
Gunakan snake_case.

### Contoh
- `user_id`
- `target_count`
- `reminder_time`
- `is_active`
- `archived_at`
- `log_date`
- `focused_duration_seconds`
- `unfocused_duration_seconds`
- `interruption_count`
- `is_read`
- `scheduled_for`

---

## 11.3 Status values
Gunakan lowercase string yang eksplisit.

### Habit log status
- `completed`
- `skipped`
- `missed`

### Focus session status
- `running`
- `completed`
- `cancelled`

### Notification type
- `habit_reminder`
- `streak_warning`
- `daily_summary`

---

## 12. Dependency Map Final

## 12.1 Allowed dependency direction
Gunakan arah dependency berikut:

```text
Request
→ Controller
→ Service
→ Model
→ Database
```

Tambahan:
```text
Controller
→ Resource / Response Helper
Controller
→ View
Service
→ Service (hanya bila masuk akal dan tidak membentuk circular dependency)
Command
→ Service
```

---

## 12.2 Yang tidak boleh
Jangan lakukan:
- Blade memanggil query berat langsung
- Model memanggil controller
- Service mengembalikan view
- Controller menghitung streak manual panjang
- Command berisi business logic utama reminder

---

## 12.3 Dependency map per modul
### Dashboard
```text
DashboardController
→ DashboardStatsService
→ Habit / HabitLog / FocusSession / UserNotification / StreakService / NotificationService
```

### Habit
```text
HabitController
→ HabitService
→ Habit
```

### Habit Log
```text
HabitLogController
→ HabitLogService
→ Habit / HabitLog
```

### Focus Session
```text
FocusSessionController
→ FocusSessionService
→ FocusSession / Habit
```

### Notification
```text
NotificationController
→ NotificationService
→ UserNotification
```

### Reminder Cron
```text
CheckHabitReminders Command
→ HabitReminderService
→ NotificationService
→ Habit / HabitLog / UserNotification
```

---

## 13. API Resource / Transformer Convention

## 13.1 Kenapa perlu resource
Walau aplikasi ini Blade-first, endpoint JSON internal akan lebih konsisten bila memakai:
- API Resource
atau
- transformer/helper response

### Rekomendasi
Gunakan `JsonResource` bila payload mulai kompleks.

---

## 13.2 Naming
- `HabitResource`
- `HabitLogResource`
- `FocusSessionResource`
- `UserNotificationResource`

---

## 13.3 Rule
- resource hanya mengatur output JSON
- jangan taruh query besar di resource
- resource boleh menambahkan derived field ringan yang sudah disiapkan dari service

---

## 14. Support Layer Convention

## 14.1 `Support/Concerns`
Dipakai untuk trait atau concern lintas modul.

### Contoh
- `ApiResponse`

---

## 14.2 `Support/Enums`
Opsional jika nanti ingin memindahkan status string ke enum PHP.

### Contoh nanti
- `HabitLogStatus`
- `FocusSessionStatus`
- `NotificationType`

Untuk MVP, string literal masih boleh, tapi enum-friendly naming harus dijaga.

---

## 14.3 `Support/ValueObjects`
Opsional untuk struktur kecil yang lebih formal, misalnya:
- `DateRange`
- `DashboardSummary`

Tidak wajib untuk fase awal.

---

## 15. Testing Naming Convention

## 15.1 Feature test
Gunakan format:
- `Action + Domain + Test`

### Contoh
- `CreateHabitTest`
- `ArchiveHabitTest`
- `QuickCheckinTest`
- `StartFocusSessionTest`
- `MarkNotificationReadTest`

---

## 15.2 Unit test
Gunakan nama service + `Test`

### Contoh
- `HabitServiceTest`
- `FocusSessionServiceTest`
- `DashboardStatsServiceTest`

---

## 15.3 Test method naming
Gunakan pola kalimat deskriptif.

### Contoh
```php
public function test_user_can_create_habit(): void
public function test_user_cannot_update_habit_owned_by_other_user(): void
public function test_running_focus_session_is_reused_when_start_called_again(): void
```

---

## 16. Coding Convention for Codex Handoff

## 16.1 File placement wajib sesuai struktur
Codex harus selalu:
- menaruh class di folder yang sesuai domain
- tidak membuat file duplikat di lokasi lain
- tidak mencampur concerns yang berbeda dalam satu file

---

## 16.2 Jangan membuat abstraction berlebihan
Untuk MVP, Codex **tidak** perlu membuat:
- repository layer tambahan
- event bus kompleks
- DTO berlapis-lapis
- interface yang belum dibutuhkan

Fokus:
- jelas
- sederhana
- testable
- mudah dibaca

---

## 16.3 Hormati konvensi Laravel
Codex harus:
- memakai route model binding
- memakai Form Request untuk validasi
- memakai Eloquent relationship
- memakai service class untuk business logic
- memakai Blade components untuk UI berulang

---

## 16.4 Konsistensi bahasa
Gunakan:
- English untuk class, method, file, route name, variable penting
- Bahasa Indonesia boleh untuk copy UI jika memang dipilih demikian

### Final rule
Codebase = English  
UI copy = boleh Indonesian

---

## 17. Sample Final Structure Snapshot

```text
app/
├── Console/
│   └── Commands/
│       └── CheckHabitReminders.php
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── HabitController.php
│   │   ├── HabitLogController.php
│   │   ├── FocusSessionController.php
│   │   └── NotificationController.php
│   ├── Requests/
│   │   ├── Habit/
│   │   ├── HabitLog/
│   │   ├── FocusSession/
│   │   └── Notification/
│   └── Resources/
├── Models/
│   ├── Habit.php
│   ├── HabitLog.php
│   ├── FocusSession.php
│   ├── User.php
│   └── UserNotification.php
├── Services/
│   ├── DashboardStatsService.php
│   ├── FocusSessionService.php
│   ├── HabitLogService.php
│   ├── HabitReminderService.php
│   ├── HabitService.php
│   ├── NotificationService.php
│   └── StreakService.php
└── Support/
    └── Concerns/
        └── ApiResponse.php
```

---

## 18. Acceptance Criteria for Structure Consistency

Sebuah implementasi dianggap konsisten jika:
- semua file berada di folder yang sesuai
- tidak ada penamaan ganda untuk konsep yang sama
- tidak ada `Notification` model yang bentrok dengan `UserNotification`
- semua route name mengikuti dot notation final
- semua endpoint AJAX berada di bawah prefix `ajax/`
- service berisi business logic inti
- Blade tidak memuat query atau logic berat
- response JSON mengikuti helper/contract yang konsisten

---

## 19. Summary
Dokumen ini menetapkan fondasi final untuk:
- struktur folder project
- naming standard lintas layer
- dependency direction
- organisasi codebase
- aturan handoff ke Codex

Dengan dokumen ini, Codex akan lebih mudah:
- meletakkan file di lokasi yang benar
- mempertahankan konsistensi nama
- mengikuti boundary antar layer
- menghasilkan code yang sesuai dengan blueprint project

Langkah dokumentasi berikut yang paling berdampak adalah:
1. **Codex Handoff Instruction Document**
2. **Implementation Roadmap / Build Order**
3. **Deployment Checklist Master Doc**
