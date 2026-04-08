
# Habit Tracker - Laravel 12: Wireframe Documentation (Markdown)

## 1. Tujuan
Dokumen ini berisi **wireframe berbasis teks (ASCII layout)** untuk setiap halaman utama.

Tujuan:
- mempermudah implementasi Blade
- jadi blueprint cepat untuk UI
- tidak perlu design tool (Figma) di awal
- cocok untuk workflow backend-first

---

## 2. Notasi Wireframe

Simbol:
- `[ ]` = container / card
- `---` = separator
- `( )` = button
- `*` = highlight / important
- `...` = repeated item

---

## 3. Global Layout

```
[ NAVBAR ]
-----------------------------------------

[ PAGE HEADER ]
-----------------------------------------

[ CONTENT AREA ]

-----------------------------------------
```

---

## 4. Dashboard Wireframe

```
[ Navbar ]

-----------------------------------------

[ Page Header ]
  Title: Dashboard
  Subtitle: Ringkasan ritme hari ini
  ( + Tambah Habit )

-----------------------------------------

[ Metrics ]
  [ Total Habit ]   [ Completed Today ]
  [ Streak ]        [ Focus Minutes ]

-----------------------------------------

[ Today Habits ]
  -------------------------------------
  [ Habit Card ]
    Title
    Target / Frequency
    ( Check-in )
  -------------------------------------
  [ Habit Card ]
  -------------------------------------

-----------------------------------------

[ Focus Card ]
  Timer: 00:25:12
  Status: Running
  ( Start ) ( Stop )

-----------------------------------------

[ Notifications Preview ]
  - Notification item
  - Notification item
  ( Lihat semua )
```

---

## 5. Habits Index Wireframe

```
[ Page Header ]
  Title: Habits
  ( + Create Habit )

-----------------------------------------

[ Filter Bar ]
  ( All ) ( Active ) ( Archived )

-----------------------------------------

[ Habit List ]

  -------------------------------------
  [ Habit Card ]
    Title
    Description
    Target
    Reminder
    ( Edit ) ( Archive ) ( Check-in )
  -------------------------------------

  ...
```

---

## 6. Habit Form Wireframe

```
[ Page Header ]
  Title: Create Habit

-----------------------------------------

[ Form Card ]

  Title Input
  Description Input

  Frequency Dropdown
  Target Input

  Reminder Time Picker

  ( Save ) ( Cancel )
```

---

## 7. Focus Session Wireframe

```
[ Page Header ]
  Title: Focus Session

-----------------------------------------

[ Timer Card - Dark ]

  00:25:12
  Status: Running

  ( Start ) ( Stop )

  Focused: XX min
  Background: XX min

-----------------------------------------

[ Session History ]

  - Session item
  - Session item
```

---

## 8. Notifications Wireframe

```
[ Page Header ]
  Title: Notifications
  ( Mark All as Read )

-----------------------------------------

[ Notification List ]

  -------------------------------------
  [ Notification Item ]
    Title
    Message
    Time
    ( Mark as read )
  -------------------------------------

  ...
```

---

## 9. Auth (Login) Wireframe

```
[ Centered Card ]

  Title: Login

  Email Input
  Password Input

  ( Login )

  Link: Register
```

---

## 10. Summary

Wireframe ini:
- cukup untuk langsung implement Blade
- tidak over-detail
- fokus ke struktur & hierarchy

Langkah berikut:
👉 implement langsung ke Blade + Tailwind
👉 atau buat reusable component dulu
