
# Habit Tracker - Testing Strategy & Acceptance Criteria Master Document

## 1. Tujuan
Dokumen ini mendefinisikan:
- strategi testing sistem
- acceptance criteria global dan per modul
- skenario uji utama (happy path & edge case)
- standar validasi behavior

Dokumen ini dibuat agar:
- Codex dapat menghasilkan code yang **testable**
- developer memiliki acuan verifikasi yang jelas
- bug logic bisa dicegah sejak awal
- semua modul tervalidasi sebelum deploy

---

# 2. Testing Philosophy

## 2.1 Layer Testing
Aplikasi ini menggunakan pendekatan:

1. **Unit Test (Service level)**
2. **Feature Test (Controller + flow)**
3. **Integration Behavior (End-to-end flow manual / semi otomatis)**

---

## 2.2 Prioritas Testing
Urutan prioritas:
1. Business logic (Service)
2. Critical flow (Habit log, Focus, Reminder)
3. API response contract
4. UI behavior (manual)

---

## 2.3 Prinsip Utama
- Semua logic penting harus deterministic
- Tidak boleh ada ambiguity dalam hasil
- Test harus mencerminkan use case spec
- Test harus mencerminkan edge case

---

# 3. Global Acceptance Criteria

## 3.1 Authentication
- user tidak login → tidak bisa akses resource
- user login → hanya bisa akses data miliknya

---

## 3.2 Data Integrity
- tidak ada duplicate habit log per hari
- tidak ada duplicate reminder dalam satu hari
- focus session hanya satu yang running

---

## 3.3 Response Consistency
- semua JSON response punya:
  - success
  - message
- success response punya data
- error response punya errors

---

## 3.4 Time Consistency
- semua perhitungan berdasarkan date (bukan datetime)
- reminder toleransi ±1 menit

---

# 4. Habit Module Testing

## 4.1 Create Habit

### Test Case
- valid input → success
- missing title → validation error
- default value applied

### Acceptance
- habit tersimpan
- is_active = true
- target_count default benar

---

## 4.2 Update Habit

### Test Case
- update milik sendiri → success
- update milik orang lain → 403

---

## 4.3 Archive Habit

### Acceptance
- is_active = false
- archived_at terisi

---

# 5. Habit Log Testing

## 5.1 Create Log

### Case
- belum ada log → create

### Acceptance
- 1 record per hari

---

## 5.2 Update Existing Log

### Case
- sudah ada log → update

### Acceptance
- tidak membuat record baru
- qty berubah

---

## 5.3 Duplicate Prevention

### Case
- 2x request di hari sama

### Acceptance
- tetap 1 record

---

## 5.4 Invalid Ownership

### Case
- akses habit milik user lain

### Acceptance
- 403

---

# 6. Focus Session Testing

## 6.1 Start Session

### Case
- tidak ada running → create
- ada running → return existing

### Acceptance
- hanya 1 session running

---

## 6.2 Stop Session

### Case
- status running → success
- status bukan running → 422

### Acceptance
- end_time terisi
- duration dihitung benar

---

## 6.3 Duration Calculation

### Acceptance
- total = focused + unfocused
- nilai tidak negatif

---

## 6.4 Multi Monitor Behavior

### Case
- tab tidak aktif

### Acceptance
- timer tetap berjalan
- unfocused_duration bertambah

---

# 7. Notification Testing

## 7.1 Reminder Creation

### Case
- habit aktif + waktu cocok → create notif

### Acceptance
- notif masuk DB

---

## 7.2 Duplicate Prevention

### Case
- command jalan 2x

### Acceptance
- hanya 1 notif

---

## 7.3 Completed Habit

### Case
- habit sudah completed

### Acceptance
- tidak kirim notif

---

## 7.4 Mark as Read

### Acceptance
- is_read = true
- read_at terisi

---

# 8. Dashboard Testing

## 8.1 Metrics Accuracy

### Acceptance
- total_active_habits benar
- completed_today benar
- focus_minutes benar
- unread_notifications benar

---

## 8.2 Today Habits

### Acceptance
- semua habit aktif muncul
- status completed benar

---

# 9. Query & Metrics Testing

## 9.1 Completion Rate

### Case
- 5/7 hari completed

### Acceptance
- hasil 71.43%

---

## 9.2 Weekly Series

### Acceptance
- 7 hari selalu ada
- missing day = 0

---

## 9.3 Streak

### Case
- hari berurutan → streak naik
- gap → streak reset

---

# 10. API Contract Testing

## 10.1 Success Response

### Acceptance
- success = true
- data tersedia

---

## 10.2 Error Response

### Acceptance
- success = false
- message jelas

---

## 10.3 Validation Error

### Acceptance
- errors field berisi detail

---

# 11. Edge Case Testing

## 11.1 Double Click
- tidak duplicate log

## 11.2 Cron Delay
- reminder tetap terkirim

## 11.3 Empty State
- UI tetap stabil

## 11.4 Large Data
- query tidak crash

---

# 12. Performance Testing (Basic)

## 12.1 Dashboard Load
- < 500ms untuk dataset kecil

## 12.2 Query Efficiency
- tidak ada N+1 query

---

# 13. Manual QA Checklist

## Habit
- create → edit → archive → delete

## Focus
- start → stop → duration benar

## Notification
- muncul → mark read

## Dashboard
- angka sesuai DB

---

# 14. Test Data Strategy

Gunakan data:
- 3–5 habits
- 7 hari logs
- beberapa focus session
- beberapa notif

---

# 15. Final Acceptance Criteria

Sistem dianggap valid jika:
- semua use case berjalan sesuai spec
- tidak ada duplicate data
- semua metrik sesuai query spec
- semua API sesuai contract
- UI menampilkan data dengan benar

---

# 16. Summary

Dokumen ini memastikan:
- semua modul tervalidasi
- behavior konsisten
- siap untuk AI-generated code
- meminimalkan bug sebelum deploy

---

# 17. Next Step
Dokumen berikut yang disarankan:
1. Project Structure & Naming Convention Master Doc
2. Codex Handoff Instruction Document
