
# Habit Tracker - Codex Handoff Instruction Document

## 1. Tujuan Dokumen
Dokumen ini adalah **briefing master** untuk Codex agar dapat mengimplementasikan proyek **Habit Tracker Laravel 12** secara konsisten, terarah, dan sesuai blueprint yang sudah disusun.

Dokumen ini harus diperlakukan sebagai:
- instruction layer utama untuk AI coding agent
- ringkasan eksekusi dari seluruh dokumentasi proyek
- guardrail untuk mencegah implementasi yang menyimpang
- acuan build order, architecture, dan coding style

Dokumen ini mengacu pada workflow frontend modern berbasis `resources/`, Vite, asset build terstruktur, dan pemisahan resource/build yang rapi. fileciteturn0file0  
Arah UI juga tetap mengikuti visual system hangat-editorial dengan parchment background, serif headline, terracotta accent, neutral hangat, dan soft card treatment. fileciteturn1file0

---

## 2. Project Identity

### Nama proyek
Habit Tracker

### Stack final
- Laravel 12
- PHP 8.2
- MySQL
- Blade
- Vite
- Tailwind CSS

### Hosting target
- shared hosting friendly
- tidak bergantung pada long-running process
- tidak menggunakan Reverb
- tidak menggunakan WebSocket
- tidak membutuhkan queue worker permanen

### Notification strategy
- cron-based notifications
- Laravel Scheduler
- artisan command
- database notifications
- optional polling di frontend

---

## 3. Codex Mission

Codex harus mengimplementasikan aplikasi web Habit Tracker yang:
- server-rendered
- modular
- clean
- testable
- konsisten dengan dokumentasi
- cukup sederhana untuk MVP
- cukup rapi untuk dikembangkan setelah deploy awal

Codex **tidak** boleh menganggap proyek ini sebagai:
- SPA React/Vue
- API-first public product
- real-time app
- microservice system
- enterprise over-engineered architecture

Ini adalah:
- monolith Laravel
- Blade-first
- AJAX hanya untuk interaksi tertentu
- service-oriented business logic
- cron-based reminders

---

## 4. Non-Negotiable Rules

Bagian ini adalah rule yang **wajib dipatuhi**.

### 4.1 Architecture Rules
- gunakan Laravel standard structure sejauh mungkin
- gunakan Blade untuk UI utama
- gunakan Form Request untuk validasi
- gunakan Service layer untuk business logic
- gunakan Eloquent relationships
- gunakan route model binding
- gunakan Vite + Tailwind untuk asset/frontend
- jangan pindahkan logic ke Blade
- jangan menaruh business logic besar di controller
- jangan membuat repository layer tambahan untuk MVP
- jangan membuat abstraction berlebihan yang belum dibutuhkan

### 4.2 Feature Rules
- satu user hanya boleh mengakses resource miliknya sendiri
- satu habit hanya boleh punya satu log per tanggal
- satu user hanya boleh punya satu focus session `running`
- reminder tidak boleh duplicate dalam hari/slot yang sama
- habit archived tidak dihitung sebagai habit aktif
- timer tidak auto-pause saat tab hidden; sistem hanya mencatat focused vs unfocused duration
- semua endpoint JSON harus mengikuti contract response yang konsisten

### 4.3 Infrastructure Rules
- jangan gunakan Reverb
- jangan gunakan WebSocket
- jangan gunakan queue worker permanen
- gunakan cron hosting + scheduler Laravel
- gunakan command `habit:check-reminders`
- reminder engine harus idempotent

### 4.4 Code Style Rules
- codebase dalam English
- UI copy boleh Indonesian
- nama class, method, variable, dan file harus konsisten dengan blueprint
- jangan membuat nama generik seperti `HelperService`, `DataManager`, `ProcessController`
- semua file harus diletakkan di folder final yang sudah ditentukan

---

## 5. What Codex Must Read as Source of Truth

Jika Codex diberi seluruh dokumen proyek, prioritas interpretasi adalah:

### Prioritas 1 — Rule behavior & contract
1. Use Case & Behavioral Specification
2. API Contract & Response Schema
3. Query & Metrics Specification
4. Testing Strategy & Acceptance Criteria

### Prioritas 2 — Architecture & implementation
5. Migration & Model Documentation
6. Request Validation & Controller Skeleton
7. Service Class Skeleton & Business Logic Flow
8. Command, Scheduler & Cron Reminder Documentation
9. Project Structure & Naming Convention

### Prioritas 3 — UI & presentation
10. Blade Layout, Page Flow & UI Design System
11. Wireframe Documentation

Jika ada conflict:
- behavior spec menang atas asumsi Codex
- API contract menang atas format JSON default Codex
- project structure doc menang atas improvisasi folder structure
- use case doc menang atas “smart guess” AI

---

## 6. Project Summary for Codex

Implement a **Laravel 12 Blade-first Habit Tracker** with these core modules:

1. Authentication
2. Habit CRUD
3. Daily habit logging
4. Focus session timer
5. Cron-based notifications
6. Dashboard metrics
7. Warm editorial UI using Tailwind

### Core domain entities
- User
- Habit
- HabitLog
- FocusSession
- UserNotification

### Core services
- HabitService
- HabitLogService
- FocusSessionService
- NotificationService
- HabitReminderService
- DashboardStatsService
- StreakService

### Core controllers
- DashboardController
- HabitController
- HabitLogController
- FocusSessionController
- NotificationController

---

## 7. Required Final Folder Structure

Codex must place files in this structure:

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

Frontend/resource structure:

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
    ├── components/
    ├── dashboard/
    ├── focus-sessions/
    ├── habits/
    ├── layouts/
    └── notifications/
```

Do not invent parallel structures unless absolutely necessary.

---

## 8. Domain Rules Codex Must Implement Correctly

## 8.1 Habit rules
- habit belongs to one user
- `is_active` defaults to true
- `target_count` defaults to 1
- archived habit sets:
  - `is_active = false`
  - `archived_at = now()`
- archived habit is excluded from active metrics and reminder processing

## 8.2 Habit log rules
- one habit log per habit per date
- if log exists for today, update instead of creating a second record
- allowed statuses:
  - `completed`
  - `skipped`
  - `missed`
- default status is `completed`
- default qty is `1`
- ownership must always be validated

## 8.3 Focus session rules
- one user can only have one `running` focus session
- if start is called and a running session exists, return the existing session
- stop only works if status is `running`
- `total_duration_seconds = focused_duration_seconds + unfocused_duration_seconds`
- store:
  - focused duration
  - unfocused duration
  - interruption count
- do not auto-pause when app/tab is hidden

## 8.4 Notification rules
- notifications are stored in DB
- reminder creation must check:
  - habit active
  - reminder time matches with tolerance
  - habit not completed today
  - no similar reminder already created today
- mark as read must set:
  - `is_read = true`
  - `read_at = now()`

## 8.5 Dashboard rules
At minimum dashboard must show:
- total_active_habits
- completed_today
- current_streak
- focus_minutes_today
- unread_notifications

---

## 9. API Contract Rules

Codex must return consistent JSON for AJAX/internal endpoints.

### Success shape
```json
{
  "success": true,
  "message": "Human-readable message",
  "data": {}
}
```

### Error shape
```json
{
  "success": false,
  "message": "Error message",
  "errors": {}
}
```

### Required JSON endpoints
- `POST /ajax/focus-sessions/start`
- `POST /ajax/focus-sessions/{focusSession}/stop`
- `GET /ajax/notifications`
- `POST /notifications/{notification}/read`
- `POST /notifications/read-all`
- `POST /ajax/habit-logs/quick-checkin`
- optional dashboard async endpoints

### Response consistency
- always include `success`
- always include `message`
- use `data` on success
- use `errors` on failure
- use `403` for ownership violation
- use `422` for validation/domain state errors

---

## 10. Query & Metrics Rules

Codex must implement metrics using the documented definitions.

### Required metric definitions
- `total_active_habits`
- `completed_today`
- `focus_minutes_today`
- `unread_notifications`
- `current_streak`

### Today habits derived fields
- `is_completed_today`
- `today_log`

### Weekly metrics (if implemented in MVP or next phase)
- `daily_completion_series`
- `daily_focus_series`
- `weekly_completion_rate`

### Important
Do not improvise alternative metric definitions.
If unsure, use the exact definitions from Query & Metrics Specification.

---

## 11. UI Direction Rules

Codex does **not** need to fully invent a visual style.  
It must follow the documented UI direction:

- warm parchment-like background
- serif headings
- sans-serif UI/body text
- terracotta primary accent
- warm neutral palette
- soft rounded cards
- subtle warm ring shadows
- calm editorial layout rhythm

### UI implementation constraints
- use Tailwind
- keep components reusable
- do not create cold corporate dashboard styling
- do not use bright tech-blue palette except focus states when needed
- do not overcomplicate Blade with logic

---

## 12. Codex Build Order

Codex should implement in this exact order unless instructed otherwise.

## Phase 1 — Foundation
1. install/configure Laravel stack assumptions
2. set up Tailwind/Vite structure
3. configure folder structure
4. create migrations
5. create models + relationships

## Phase 2 — Domain Logic
6. create Form Requests
7. create services
8. create resource/response helpers
9. create controllers
10. create routes

## Phase 3 — UI Base
11. create layouts
12. create shared Blade components
13. create auth pages
14. create dashboard skeleton
15. create habits pages

## Phase 4 — Interactive Features
16. implement quick check-in
17. implement focus timer AJAX flow
18. implement notification list + mark-as-read
19. implement JS modules

## Phase 5 — Background Logic
20. create `CheckHabitReminders` command
21. wire command to scheduler
22. implement reminder duplicate prevention
23. verify cron-ready behavior

## Phase 6 — Verification
24. implement feature tests
25. implement unit tests for services
26. verify API contract
27. verify metrics and acceptance criteria

---

## 13. What Codex Should Avoid

Codex must avoid:
- adding repository pattern for no reason
- converting app into SPA
- adding websockets/realtime stack
- adding queue worker dependencies
- introducing over-generalized helpers
- placing queries directly in Blade
- placing streak/reminder logic directly in controller
- using `Notification` model name instead of `UserNotification`
- inventing inconsistent route names
- generating duplicate endpoints for same action
- changing route semantics without reason
- using different JSON envelopes across controllers

---

## 14. File-by-File Implementation Expectations

## 14.1 Models
Each model must include:
- fillable
- casts
- relationships
- simple scopes when needed

### Required models
- `Habit`
- `HabitLog`
- `FocusSession`
- `UserNotification`

---

## 14.2 Form Requests
Each request class must include:
- `authorize()`
- `rules()`

### Required request classes
- `StoreHabitRequest`
- `UpdateHabitRequest`
- `StoreHabitLogRequest`
- `UpdateHabitLogRequest`
- `StartFocusSessionRequest`
- `StopFocusSessionRequest`
- `MarkNotificationAsReadRequest`

---

## 14.3 Services
Each service must:
- have domain-specific methods
- keep controllers thin
- implement documented behavior
- be testable independently

---

## 14.4 Controllers
Each controller must:
- delegate logic to services
- return Blade views or JSON
- not contain heavy business logic
- enforce ownership through request/service/model binding flow

---

## 14.5 Blade
Blade files must:
- stay presentational
- use components/partials where repeated
- avoid inline DB queries
- avoid embedded heavy logic

---

## 14.6 JS modules
JS should be:
- small
- feature-scoped
- organized by domain
- safe for Blade-first enhancement

---

## 15. Testing Requirements for Codex

Codex should generate tests for critical behavior.

### Must-test service behaviors
- habit create/update/archive
- habit log duplicate prevention
- focus session single-running rule
- focus session stop invalid-state handling
- reminder duplicate prevention
- dashboard metrics calculation
- streak calculation basic correctness

### Must-test feature flows
- authenticated user can manage own habit
- user cannot access other user habit
- quick check-in creates or updates correctly
- focus start returns existing running session
- focus stop returns expected payload
- notifications list is scoped correctly
- mark-as-read updates correct notification

### API tests
- success shape correct
- validation error shape correct
- forbidden shape correct

---

## 16. Acceptance Conditions for Codex Output

Codex output is considered acceptable only if:
- file structure matches the master structure
- naming matches the naming convention doc
- all core modules work with documented rules
- no prohibited architecture choices are introduced
- AJAX responses follow the contract
- critical business rules are enforced
- tests cover the core flows
- UI reflects the documented direction reasonably well

---

## 17. Suggested Prompt Template for Codex

Below is the recommended prompt style when handing this project to Codex.

```text
You are implementing a Laravel 12 Habit Tracker project.

Follow these rules strictly:
1. Use Blade-first architecture, not SPA.
2. Use Laravel Form Requests for validation.
3. Put business logic in Services.
4. Use Eloquent relationships and route model binding.
5. Use cron-based notifications only. No Reverb, no WebSocket, no queue worker.
6. Use the documented folder structure and naming conventions exactly.
7. Use UserNotification as the model for the notifications table.
8. JSON endpoints must use the shared response contract:
   success/message/data for success,
   success/message/errors for failure.
9. Enforce these rules:
   - one habit log per habit per day
   - one running focus session per user
   - no duplicate reminder notification in the same day/slot
10. Keep Blade presentational and thin.
11. Keep controllers thin and delegate logic to services.
12. Follow the documented build order.

Implement step by step and do not invent architecture outside the documented blueprint.
```

---

## 18. Suggested Handoff Strategy

Saat benar-benar melempar ke Codex, lakukan seperti ini:

### Opsi A — Bertahap
Berikan:
1. Handoff doc
2. Project structure doc
3. Migration/model doc
4. Request/controller doc
5. Service/business logic doc
6. API contract doc
7. Query metrics doc
8. Testing doc
9. UI doc

Lalu minta implementasi bertahap per fase.

### Opsi B — Bundle penuh
Berikan semua dokumen sekaligus jika context window Codex cukup besar, lalu instruksikan:
- baca semua
- anggap dokumen sebagai source of truth
- mulai dari fase foundation

### Rekomendasi
Paling aman:
- **gunakan handoff bertahap**
- minta output per fase
- review sebelum lanjut fase berikutnya

---

## 19. Final Guidance for Codex

Codex should prioritize:
1. correctness over cleverness
2. consistency over abstraction
3. readability over premature optimization
4. documented behavior over assumption
5. Laravel convention over custom structure

If any ambiguity appears:
- prefer the documented rule
- do not invent architecture silently
- keep implementation simple and explicit

---

## 20. Summary
Dokumen ini adalah **instruction master** untuk Codex.

Fungsi utamanya:
- mengunci rule penting
- memberi arah implementasi
- menjaga konsistensi antar layer
- mengurangi improvisasi yang salah
- memastikan semua dokumentasi sebelumnya benar-benar dipakai

Dengan dokumen ini, Codex seharusnya bisa:
- memahami proyek dengan cepat
- membangun codebase secara bertahap
- mengikuti architecture, API contract, metrics, dan behavior spec
- menghasilkan implementasi yang lebih dekat ke kebutuhan final sejak iterasi pertama
