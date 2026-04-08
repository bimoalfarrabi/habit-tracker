
# Habit Tracker - Use Case & Behavioral Specification

## 1. Tujuan
Dokumen ini mendefinisikan:
- use case utama
- expected behavior sistem
- edge cases
- constraint eksplisit

Dokumen ini dibuat agar:
- tidak ada ambiguity saat implementasi
- AI (Codex) dapat generate code dengan benar
- behavior konsisten di seluruh sistem

---

# 2. Global Rules

## 2.1 Authentication
- Semua fitur (kecuali auth) membutuhkan user login
- Semua data selalu scoped ke `user_id`

---

## 2.2 Date Handling
- Default timezone mengikuti server
- Semua perhitungan harian berdasarkan `date` (bukan datetime)

---

## 2.3 Ownership Rule
User hanya boleh:
- mengakses habit miliknya
- mengakses log miliknya
- mengakses focus session miliknya
- mengakses notification miliknya

Jika tidak → `403`

---

# 3. Habit Use Cases

## 3.1 Create Habit
IF request valid
→ create habit dengan `user_id`

DEFAULT:
- is_active = true
- target_count = 1

---

## 3.2 Update Habit
IF habit milik user
→ update

ELSE
→ 403

---

## 3.3 Archive Habit
→ set:
- is_active = false
- archived_at = now()

---

## 3.4 Delete Habit
MVP:
→ boleh delete

Future:
→ prefer archive

---

# 4. Habit Log Use Cases

## 4.1 Check-in Habit

CASE 1: belum ada log hari ini
→ create log

CASE 2: sudah ada log hari ini
→ update log (tidak create baru)

---

## 4.2 Status Rules
Allowed:
- completed
- skipped
- missed

DEFAULT:
- completed

---

## 4.3 Quantity Rules
IF qty tidak dikirim
→ default = 1

---

## 4.4 Duplicate Prevention
Constraint:
- 1 habit = 1 log per date

---

## 4.5 Invalid Ownership
IF habit bukan milik user
→ 403

---

# 5. Focus Session Use Cases

## 5.1 Start Session

IF tidak ada session running
→ create session

IF ada session running
→ return existing session

---

## 5.2 Stop Session

IF session.status != running
→ 422 error

ELSE:
→ update:
- end_time
- focused_duration
- unfocused_duration
- total_duration = sum
- status

---

## 5.3 Multi Monitor Behavior
Timer:
- tidak auto pause
- tetap jalan

System hanya mencatat:
- focused_duration
- unfocused_duration

---

## 5.4 Invalid Ownership
IF session bukan milik user
→ 403

---

# 6. Notification Use Cases

## 6.1 Reminder Creation

IF:
- habit aktif
- reminder_time match
- belum completed hari ini
- belum ada notif hari ini

→ create notification

---

## 6.2 Prevent Duplicate
CHECK:
- user_id
- habit_id (di data)
- type = habit_reminder
- date sama

IF exists → skip

---

## 6.3 Mark as Read
→ set:
- is_read = true
- read_at = now()

---

## 6.4 Mark All as Read
→ update semua notif unread user

---

# 7. Dashboard Use Cases

## 7.1 Metrics

### total_active_habits
count habits:
- user_id
- is_active = true

---

### completed_today
count habit_logs:
- user_id
- log_date = today
- status = completed

---

### focus_minutes_today
sum:
- focused_duration_seconds / 60

---

### unread_notifications
count:
- is_read = false

---

# 8. Reminder Cron Behavior

## 8.1 Execution
Cron → scheduler → command → service

---

## 8.2 Time Matching
MATCH:
- hour:minute

WITH tolerance:
±1 minute

---

## 8.3 Idempotency
Command boleh dijalankan berkali-kali tanpa efek duplikasi

---

# 9. Edge Cases

## 9.1 Double Click Check-in
→ update existing log

---

## 9.2 Refresh Saat Timer Jalan
→ session tetap running

---

## 9.3 Cron Delay
→ gunakan tolerance waktu

---

## 9.4 Deleted Habit
→ log tetap ada
→ habit tidak diproses lagi

---

## 9.5 Empty Data
→ tampilkan empty state UI

---

# 10. Error Handling

## 403
- akses resource milik user lain

## 422
- invalid state (stop session bukan running)

---

# 11. API Behavior (Internal AJAX)

## Focus Start
POST /focus/start
→ return session

## Focus Stop
POST /focus/{id}/stop
→ return updated session

## Notifications
GET /notifications
→ return list

---

# 12. Summary

Dokumen ini memastikan:
- behavior sistem jelas
- tidak ambiguous
- siap untuk AI code generation
- semua edge case ter-cover
