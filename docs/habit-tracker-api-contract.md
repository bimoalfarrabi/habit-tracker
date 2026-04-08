
# Habit Tracker - API Contract & Response Schema Specification

## 1. Tujuan
Dokumen ini mendefinisikan kontrak internal untuk endpoint yang dipakai oleh:
- Blade + AJAX
- polling notifications
- focus timer
- quick habit check-in
- future frontend enhancement

Dokumen ini dibuat agar:
- struktur request/response konsisten
- Codex dapat generate controller, service, dan JS client tanpa ambiguity
- format error dan success seragam
- field naming final lebih stabil

Dokumen ini mengikuti blueprint Laravel 12 yang sudah disusun sebelumnya, dengan workflow frontend modern berbasis `resources/`, Vite, dan asset build terstruktur. fileciteturn0file0

---

## 2. Scope API
Dokumen ini **bukan** public API contract.  
Ini adalah **internal web contract** untuk endpoint aplikasi Habit Tracker.

Endpoint yang dicakup:
1. Focus Session
2. Notifications
3. Habit Log quick action
4. Dashboard async widgets (opsional)
5. Shared response envelope
6. Shared error schema

---

## 3. Prinsip Umum API Contract

### 3.1 JSON dipakai untuk interaction ringan
Gunakan JSON untuk:
- start focus session
- stop focus session
- polling notification
- mark notification as read
- quick check-in habit
- widget async kecil

### 3.2 Blade-first, AJAX-second
Aplikasi tetap Blade-first.
AJAX hanya dipakai untuk:
- interaksi yang butuh respons cepat
- update bagian kecil UI
- menghindari full page reload saat tidak perlu

### 3.3 Naming convention
Gunakan:
- snake_case untuk field backend JSON
- status string yang eksplisit
- timestamp dalam format ISO standard jika perlu datetime
- date field dalam `YYYY-MM-DD`
- time field dalam `HH:MM` atau `HH:MM:SS` sesuai konteks

### 3.4 Ownership selalu implicit
Semua endpoint di bawah `auth`.
User ID **tidak** dikirim dari client untuk resource user-owned.
Backend menentukan dari session user yang login.

---

## 4. Shared JSON Response Pattern

## 4.1 Success response standard
Untuk endpoint JSON, gunakan pola:

```json
{
  "success": true,
  "message": "Human-readable message",
  "data": {}
}
```

### Keterangan
- `success`: boolean
- `message`: pesan singkat untuk UI atau debugging ringan
- `data`: payload utama

---

## 4.2 Error response standard
Untuk endpoint JSON, gunakan pola:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {}
}
```

### Keterangan
- `success`: selalu `false`
- `message`: pesan error global
- `errors`: field-level validation error atau object kosong bila tidak ada

---

## 4.3 Validation error schema
Untuk validation error 422, gunakan bentuk:

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "title": [
      "The title field is required."
    ],
    "frequency": [
      "The frequency field must be one of daily or weekly."
    ]
  }
}
```

---

## 4.4 Domain error schema
Untuk domain/state error, misalnya stop focus session saat status bukan running:

```json
{
  "success": false,
  "message": "Sesi fokus sudah tidak aktif.",
  "errors": {}
}
```

---

## 4.5 Unauthorized / forbidden schema
Untuk akses resource milik user lain:

```json
{
  "success": false,
  "message": "You are not allowed to access this resource.",
  "errors": {}
}
```

---

## 5. Status Code Convention

| HTTP Code | Use Case |
|----------|----------|
| 200 | GET success / update success / action success |
| 201 | create success bila perlu dipisahkan eksplisit |
| 204 | opsional untuk no-content action, tapi untuk konsistensi lebih baik tetap 200 JSON |
| 401 | user belum login |
| 403 | user tidak berhak akses resource |
| 404 | resource tidak ditemukan |
| 422 | validation gagal / domain state tidak valid |
| 500 | unexpected server error |

### Rekomendasi
Untuk konsistensi internal:
- pakai `200` untuk hampir semua success AJAX
- pakai `422` untuk validation/domain problem
- pakai `403` untuk ownership violation

---

## 6. Resource Field Naming

## 6.1 Habit JSON shape
```json
{
  "id": 12,
  "title": "Minum air putih",
  "description": "Minimal 8 gelas per hari",
  "frequency": "daily",
  "target_count": 8,
  "reminder_time": "08:00:00",
  "color": "terracotta",
  "icon": "droplet",
  "is_active": true,
  "archived_at": null,
  "created_at": "2026-04-08T07:10:00+07:00",
  "updated_at": "2026-04-08T07:10:00+07:00"
}
```

### Catatan
- `user_id` tidak wajib dikirim ke client kecuali memang dibutuhkan
- `color` dan `icon` boleh `null`
- `reminder_time` berasal dari kolom DB dan bisa berbentuk `HH:MM:SS`

---

## 6.2 Habit Log JSON shape
```json
{
  "id": 100,
  "habit_id": 12,
  "log_date": "2026-04-08",
  "status": "completed",
  "qty": 8,
  "note": "Target tercapai",
  "created_at": "2026-04-08T08:05:00+07:00",
  "updated_at": "2026-04-08T08:05:00+07:00"
}
```

---

## 6.3 Focus Session JSON shape
```json
{
  "id": 55,
  "habit_id": 12,
  "session_date": "2026-04-08",
  "start_time": "2026-04-08T09:00:00+07:00",
  "end_time": "2026-04-08T09:25:00+07:00",
  "planned_duration_minutes": 25,
  "total_duration_seconds": 1500,
  "focused_duration_seconds": 1200,
  "unfocused_duration_seconds": 300,
  "interruption_count": 2,
  "status": "completed",
  "note": "Pomodoro pagi"
}
```

---

## 6.4 Notification JSON shape
Gunakan model `UserNotification` yang memetakan tabel `notifications`.

```json
{
  "id": 201,
  "type": "habit_reminder",
  "title": "Reminder Habit",
  "message": "Jangan lupa: Minum air putih",
  "data": {
    "habit_id": 12,
    "habit_title": "Minum air putih"
  },
  "is_read": false,
  "read_at": null,
  "scheduled_for": "2026-04-08T08:00:00+07:00",
  "created_at": "2026-04-08T08:00:10+07:00",
  "updated_at": "2026-04-08T08:00:10+07:00"
}
```

---

## 7. Endpoint Contract - Focus Session

## 7.1 Start Focus Session

### Endpoint
```http
POST /ajax/focus-sessions/start
```

### Tujuan
Memulai sesi fokus baru atau mengembalikan sesi `running` yang sudah ada.

### Request body
```json
{
  "habit_id": 12,
  "planned_duration_minutes": 25,
  "note": "Pomodoro pagi"
}
```

### Request rules
- `habit_id`: nullable
- `planned_duration_minutes`: nullable
- `note`: nullable

### Success response
```json
{
  "success": true,
  "message": "Sesi fokus dimulai.",
  "data": {
    "session": {
      "id": 55,
      "habit_id": 12,
      "session_date": "2026-04-08",
      "start_time": "2026-04-08T09:00:00+07:00",
      "end_time": null,
      "planned_duration_minutes": 25,
      "total_duration_seconds": 0,
      "focused_duration_seconds": 0,
      "unfocused_duration_seconds": 0,
      "interruption_count": 0,
      "status": "running",
      "note": "Pomodoro pagi"
    },
    "reused_existing_session": false
  }
}
```

### Existing running session response
```json
{
  "success": true,
  "message": "Sesi fokus yang sedang berjalan ditemukan.",
  "data": {
    "session": {
      "id": 55,
      "habit_id": 12,
      "session_date": "2026-04-08",
      "start_time": "2026-04-08T09:00:00+07:00",
      "end_time": null,
      "planned_duration_minutes": 25,
      "total_duration_seconds": 0,
      "focused_duration_seconds": 0,
      "unfocused_duration_seconds": 0,
      "interruption_count": 0,
      "status": "running",
      "note": "Pomodoro pagi"
    },
    "reused_existing_session": true
  }
}
```

### Failure cases
- `403` jika `habit_id` bukan milik user
- `422` jika payload invalid

---

## 7.2 Stop Focus Session

### Endpoint
```http
POST /ajax/focus-sessions/{focusSession}/stop
```

### Tujuan
Menghentikan sesi fokus yang sedang berjalan.

### Request body
```json
{
  "focused_duration_seconds": 1200,
  "unfocused_duration_seconds": 300,
  "interruption_count": 2,
  "status": "completed"
}
```

### Request rules
- `focused_duration_seconds`: required integer
- `unfocused_duration_seconds`: required integer
- `interruption_count`: required integer
- `status`: `completed` atau `cancelled`

### Success response
```json
{
  "success": true,
  "message": "Sesi fokus dihentikan.",
  "data": {
    "session": {
      "id": 55,
      "habit_id": 12,
      "session_date": "2026-04-08",
      "start_time": "2026-04-08T09:00:00+07:00",
      "end_time": "2026-04-08T09:25:00+07:00",
      "planned_duration_minutes": 25,
      "total_duration_seconds": 1500,
      "focused_duration_seconds": 1200,
      "unfocused_duration_seconds": 300,
      "interruption_count": 2,
      "status": "completed",
      "note": "Pomodoro pagi"
    }
  }
}
```

### Domain error response
```json
{
  "success": false,
  "message": "Sesi fokus sudah tidak aktif.",
  "errors": {}
}
```

---

## 7.3 Focus Session Today Summary (opsional)
Jika nanti butuh widget async:

### Endpoint
```http
GET /ajax/focus-sessions/today-summary
```

### Success response
```json
{
  "success": true,
  "message": "Focus summary loaded.",
  "data": {
    "total_sessions": 3,
    "focus_minutes_today": 95,
    "background_minutes_today": 20,
    "interruption_count_today": 4
  }
}
```

---

## 8. Endpoint Contract - Notifications

## 8.1 List Latest Notifications

### Endpoint
```http
GET /ajax/notifications
```

### Tujuan
Mengambil notifikasi terbaru untuk navbar dropdown atau polling.

### Query params
- `limit` optional, default `10`

### Success response
```json
{
  "success": true,
  "message": "Notifications loaded.",
  "data": {
    "notifications": [
      {
        "id": 201,
        "type": "habit_reminder",
        "title": "Reminder Habit",
        "message": "Jangan lupa: Minum air putih",
        "data": {
          "habit_id": 12,
          "habit_title": "Minum air putih"
        },
        "is_read": false,
        "read_at": null,
        "scheduled_for": "2026-04-08T08:00:00+07:00",
        "created_at": "2026-04-08T08:00:10+07:00"
      }
    ],
    "unread_count": 3
  }
}
```

---

## 8.2 Mark Single Notification as Read

### Endpoint
```http
POST /notifications/{notification}/read
```

### Tujuan
Menandai satu notif sebagai dibaca.

### Request body
Tidak wajib.

### Success response
```json
{
  "success": true,
  "message": "Notifikasi ditandai sudah dibaca.",
  "data": {
    "notification_id": 201,
    "is_read": true,
    "read_at": "2026-04-08T08:03:00+07:00"
  }
}
```

### Failure cases
- `403` jika notif bukan milik user
- `404` jika notif tidak ditemukan

---

## 8.3 Mark All Notifications as Read

### Endpoint
```http
POST /notifications/read-all
```

### Tujuan
Menandai semua notif unread milik user sebagai dibaca.

### Success response
```json
{
  "success": true,
  "message": "Semua notifikasi ditandai sudah dibaca.",
  "data": {
    "updated_count": 5
  }
}
```

### Catatan
Jika endpoint ini dipakai dari form Blade biasa, boleh redirect.
Namun jika dipakai AJAX, gunakan JSON contract di atas.

---

## 8.4 Unread Notification Count Only (opsional)
Jika nanti ingin endpoint lebih ringan:

### Endpoint
```http
GET /ajax/notifications/unread-count
```

### Success response
```json
{
  "success": true,
  "message": "Unread notification count loaded.",
  "data": {
    "unread_count": 3
  }
}
```

---

## 9. Endpoint Contract - Habit Log Quick Action

## 9.1 Quick Check-in Habit

### Endpoint
```http
POST /ajax/habit-logs/quick-checkin
```

### Tujuan
Membuat atau mengupdate log habit untuk hari ini tanpa reload penuh.

### Request body
```json
{
  "habit_id": 12,
  "status": "completed",
  "qty": 1,
  "note": "Selesai pagi"
}
```

### Rule behavior
- backend selalu pakai `log_date = today()`
- jika log hari ini belum ada → create
- jika log hari ini sudah ada → update
- response harus memberi tahu apakah action create atau update

### Success response - created
```json
{
  "success": true,
  "message": "Log habit berhasil dibuat.",
  "data": {
    "action": "created",
    "log": {
      "id": 100,
      "habit_id": 12,
      "log_date": "2026-04-08",
      "status": "completed",
      "qty": 1,
      "note": "Selesai pagi"
    }
  }
}
```

### Success response - updated
```json
{
  "success": true,
  "message": "Log habit berhasil diperbarui.",
  "data": {
    "action": "updated",
    "log": {
      "id": 100,
      "habit_id": 12,
      "log_date": "2026-04-08",
      "status": "completed",
      "qty": 2,
      "note": "Update sore"
    }
  }
}
```

### Failure cases
- `403` jika habit bukan milik user
- `422` jika payload invalid

---

## 9.2 Update Specific Habit Log (opsional AJAX)
### Endpoint
```http
PATCH /ajax/habit-logs/{habitLog}
```

### Tujuan
Edit log tertentu jika nanti histori log editable via modal.

### Request body
```json
{
  "status": "skipped",
  "qty": 1,
  "note": "Sedang tidak memungkinkan"
}
```

### Success response
```json
{
  "success": true,
  "message": "Log habit berhasil diperbarui.",
  "data": {
    "log": {
      "id": 100,
      "habit_id": 12,
      "log_date": "2026-04-08",
      "status": "skipped",
      "qty": 1,
      "note": "Sedang tidak memungkinkan"
    }
  }
}
```

---

## 10. Endpoint Contract - Dashboard Async Widgets

## 10.1 Dashboard Summary
Jika dashboard suatu saat ingin partial async load:

### Endpoint
```http
GET /ajax/dashboard/summary
```

### Success response
```json
{
  "success": true,
  "message": "Dashboard summary loaded.",
  "data": {
    "total_active_habits": 5,
    "completed_today": 3,
    "current_streak": 7,
    "focus_minutes_today": 120,
    "unread_notifications": 2
  }
}
```

---

## 10.2 Today Habits Widget
### Endpoint
```http
GET /ajax/dashboard/today-habits
```

### Success response
```json
{
  "success": true,
  "message": "Today habits loaded.",
  "data": {
    "habits": [
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
    ]
  }
}
```

### Catatan
`is_completed_today` adalah derived field yang membantu UI.
`today_log` boleh `null` jika belum ada.

---

## 11. Shared Pagination Contract

Untuk endpoint list penuh (jika nanti AJAX pagination dipakai), gunakan schema:

```json
{
  "success": true,
  "message": "Data loaded.",
  "data": {
    "items": [],
    "meta": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 10,
      "total": 48
    }
  }
}
```

### Rekomendasi
- untuk polling notif, tidak perlu pagination dulu
- untuk halaman penuh Blade, pagination server-side biasa lebih aman

---

## 12. Date & Time Contract

## 12.1 Date-only field
Gunakan:
- `YYYY-MM-DD`

Contoh:
- `log_date`
- `session_date`

## 12.2 Time-only field
Gunakan:
- `HH:MM:SS` untuk payload yang berasal dari DB time
- `HH:MM` untuk form input user jika perlu

Contoh:
- `reminder_time`

## 12.3 Datetime field
Gunakan ISO 8601 standard.
Contoh:
- `2026-04-08T09:25:00+07:00`

Field:
- `created_at`
- `updated_at`
- `start_time`
- `end_time`
- `read_at`
- `scheduled_for`

---

## 13. Error Message Guidelines

## 13.1 Prinsip
Error message harus:
- singkat
- jelas
- tidak terlalu teknis untuk user
- tetap bisa dipakai debugging ringan

## 13.2 Contoh
### Validation
- `Validation failed.`

### Forbidden
- `You are not allowed to access this resource.`

### Domain state
- `Sesi fokus sudah tidak aktif.`
- `Habit tidak ditemukan atau tidak dimiliki user.`

### Generic server error
- `An unexpected error occurred.`

---

## 14. Controller Response Recommendations

## 14.1 Gunakan helper internal response jika perlu
Agar semua controller JSON konsisten, disarankan membuat helper/trait response.

### Contoh trait
```php
trait ApiResponse
{
    protected function successResponse(string $message, array $data = [], int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(string $message, array $errors = [], int $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
```

### Manfaat
- response semua endpoint seragam
- mempermudah Codex generate controller
- memudahkan JS client parsing

---

## 15. JavaScript Consumption Guidelines

## 15.1 Focus timer JS
JS client harus mengasumsikan:
- `success` boolean selalu ada
- `data.session` ada untuk response focus session
- error akan punya `message`

### Contoh flow
```js
if (response.success) {
  const session = response.data.session
  // update UI
} else {
  // show toast dengan response.message
}
```

---

## 15.2 Notification polling JS
Polling endpoint harus dibaca seperti:
- `data.notifications`
- `data.unread_count`

### Contoh flow
```js
if (response.success) {
  updateNotificationList(response.data.notifications)
  updateNotificationBadge(response.data.unread_count)
}
```

---

## 15.3 Quick check-in JS
Client harus membaca:
- `data.action`
- `data.log`

### Contoh
```js
if (response.success) {
  if (response.data.action === 'created') {
    // show created feedback
  } else {
    // show updated feedback
  }
}
```

---

## 16. Task Breakdown Implementasi API Contract

## Fase 1 - Foundation
- tetapkan success/error schema
- buat response trait/helper
- pastikan validation JSON konsisten

## Fase 2 - Focus Session
- implement start endpoint
- implement stop endpoint
- return response sesuai kontrak

## Fase 3 - Notifications
- implement latest notifications endpoint
- implement mark one as read
- implement mark all as read

## Fase 4 - Habit Quick Check-in
- implement quick check-in endpoint
- pastikan create/update dibedakan di response

## Fase 5 - Dashboard Async
- implement summary endpoint opsional
- implement today habits widget opsional

## Fase 6 - Frontend Binding
- sambungkan JS polling ke notification endpoint
- sambungkan focus timer ke endpoint start/stop
- sambungkan quick check-in button ke endpoint quick-checkin

---

## 17. Checklist Verifikasi

### Response shape
- semua endpoint JSON punya `success`
- semua endpoint JSON punya `message`
- success endpoint punya `data`
- error endpoint punya `errors`

### Focus session
- start return `session`
- stop return updated `session`
- running session reuse terdeteksi

### Notifications
- polling return list + unread count
- mark as read return `notification_id`
- mark all return `updated_count`

### Habit quick check-in
- create dan update dibedakan
- log_date selalu hari ini
- ownership dicek

### Frontend integration
- JS mudah parsing response
- error handling tidak ambigu
- UI state sinkron dengan payload

---

## 18. Summary
Dokumen ini menetapkan:
- kontrak endpoint internal
- schema request/response final
- naming field JSON
- status code convention
- error schema
- AJAX integration pattern
- response helper recommendation

Dengan dokumen ini, Codex akan lebih mudah memahami:
- payload apa yang diharapkan
- response seperti apa yang harus dikembalikan
- bagaimana frontend akan mengonsumsi endpoint

Langkah dokumentasi berikut yang paling berdampak adalah:
1. **Query & Metrics Specification**
2. **Testing Strategy & Acceptance Criteria**
3. **Project Structure & Naming Convention Master Doc**
