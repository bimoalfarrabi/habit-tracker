# Habit Tracker - Laravel 12 (Detailed Planning - Cron Based)

## 1. Overview

Aplikasi Habit Tracker berbasis Laravel 12 untuk membantu user: -
membangun kebiasaan - tracking progres harian - melihat statistik -
menerima reminder berbasis cron

Target: - Shared hosting friendly - Tanpa realtime dependency - Scalable
ke VPS di masa depan

------------------------------------------------------------------------

## 2. Tech Stack

-   Laravel 12
-   PHP 8.2
-   MySQL
-   Vite
-   Tailwind CSS
-   Vanilla JS (focus timer)

------------------------------------------------------------------------

## 3. Architecture

### Type

Monolith Laravel (MVC)

### Constraints

-   Tidak ada long-running process
-   Tidak ada websocket
-   Cron sebagai background job

### Flow High-Level

User → Controller → Model → DB\
Cron → Scheduler → Command → DB (notifications)

------------------------------------------------------------------------

## 4. Modules

### 4.1 Auth

-   Register
-   Login
-   Logout
-   Session based

### 4.2 Habit

-   Create habit
-   Edit habit
-   Delete habit
-   Set reminder_time

### 4.3 Habit Log

-   Daily check-in
-   Optional note
-   Status complete/incomplete

### 4.4 Dashboard

-   Total habits
-   Completed today
-   Current streak
-   Longest streak
-   Completion rate

### 4.5 Focus Timer

-   Start / Stop session
-   Track duration
-   Detect tab visibility
-   Store:
    -   focused_duration
    -   unfocused_duration

### 4.6 Notification

-   Generated via cron
-   Stored in DB
-   Polling fetch
-   Read/unread state

------------------------------------------------------------------------

## 5. Database Design

### users

-   id
-   name
-   email
-   password

### habits

-   id
-   user_id
-   title
-   description
-   frequency (daily/weekly)
-   reminder_time
-   is_active

### habit_logs

-   id
-   habit_id
-   user_id
-   log_date
-   status
-   note

### notifications

-   id
-   user_id
-   type
-   message
-   is_read
-   created_at

------------------------------------------------------------------------

## 6. Relationships (Eloquent)

User: - hasMany Habits - hasMany HabitLogs - hasMany Notifications

Habit: - belongsTo User - hasMany HabitLogs

HabitLog: - belongsTo Habit - belongsTo User

Notification: - belongsTo User

------------------------------------------------------------------------

## 7. Notification System (Cron)

### Flow Detail

1.  Cron trigger `schedule:run`
2.  Scheduler run command
3.  Command:
    -   cek habit user
    -   cek belum check-in hari ini
    -   cek reminder_time
4.  Insert notification
5.  Frontend fetch via polling

### Prevent Duplicate

-   cek notification hari ini sebelum insert

------------------------------------------------------------------------

## 8. Focus Timer Logic

### Behavior

-   timer tetap jalan walau tab hidden
-   track:
    -   focused time
    -   unfocused time

### JS Event

-   visibilitychange

### Data Output

-   start_time
-   end_time
-   focused_duration
-   unfocused_duration

------------------------------------------------------------------------

## 9. Routing Plan

### Web Routes

-   /login
-   /register
-   /dashboard
-   /habits
-   /habits/{id}
-   /notifications

### API (optional AJAX)

-   GET /api/notifications
-   POST /api/habit-log

------------------------------------------------------------------------

## 10. Controller Plan

-   AuthController
-   DashboardController
-   HabitController
-   HabitLogController
-   NotificationController

------------------------------------------------------------------------

## 11. Phase Breakdown

### Phase 1 -- Setup

-   install Laravel
-   setup Vite & Tailwind

### Phase 2 -- Auth

-   Breeze install
-   login/register

### Phase 3 -- Habit

-   CRUD
-   reminder_time

### Phase 4 -- Logging

-   check-in system

### Phase 5 -- Dashboard

-   stats + query aggregation

### Phase 6 -- Focus Timer

-   JS timer
-   visibility logic

### Phase 7 -- Notification Engine

-   artisan command
-   logic reminder

### Phase 8 -- Scheduler

-   Kernel config
-   cron setup

### Phase 9 -- Notification UI

-   list + polling

### Phase 10 -- Optimization

-   query optimization
-   caching simple

### Phase 11 -- Deployment

-   build assets
-   upload
-   set cron

------------------------------------------------------------------------

## 12. Deployment Notes

-   npm run build
-   set .env
-   storage writable
-   cron aktif

------------------------------------------------------------------------

## 13. Future Upgrade

-   VPS
-   Queue worker
-   Reverb
-   Push notification

------------------------------------------------------------------------

## 14. Summary

-   Fully shared hosting compatible
-   No realtime dependency
-   Clean upgrade path
