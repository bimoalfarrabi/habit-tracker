# Habit Tracker

Habit Tracker is a Laravel 12 web app for building consistent daily routines with a warm editorial UI style.

The app includes habit and todo management, quick check-ins, focus session tracking, notifications, and profile management with photo upload.

## Main Features

- Authentication (Laravel Breeze): login, register, reset password, email verification.
- Dashboard metrics: active habits, completed today, streak, focus minutes, unread notifications.
- Habit management:
  - create, edit, archive, activate/deactivate,
  - color picker + icon field,
  - daily/weekly frequency and target count.
- Habit check-in:
  - quick check-in from dashboard/list,
  - log status and quantity.
- Todo management:
  - create, edit, delete todos,
  - pending/completed/overdue filters,
  - mark done / return to pending,
  - optional due date + reminder time.
- Focus sessions:
  - start/stop timer,
  - focused vs background duration,
  - interruption count and session history.
- Notifications:
  - list notifications,
  - mark single/read all,
  - unread badge in navbar.
- Profile:
  - update name/email,
  - update password,
  - delete account,
  - profile photo upload/remove,
  - activity tab (habit, check-in, focus, notification history).
- Subtle page transition on internal navigation (without target-page flicker).

## Tech Stack

- PHP 8.2+
- Laravel 12
- Blade + Tailwind CSS + Alpine.js
- MySQL (local via XAMPP)
- Vite (frontend build tool)
- PHPUnit (feature + unit tests)

## Requirements

- XAMPP (PHP + MySQL)
- Composer 2+
- Node.js 18+ and npm

For this project, the local PHP binary used is:

```bash
/opt/lampp/bin/php
```

## Local Setup (XAMPP + MySQL)

1. Clone repository and enter project directory.

```bash
git clone <your-repo-url> habit-tracker
cd habit-tracker
```

2. Install PHP dependencies.

```bash
composer install
```

3. Prepare environment file.

```bash
cp .env.example .env
```

4. Configure database in `.env`.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=habit-tracker
DB_USERNAME=root
DB_PASSWORD=
```

5. Create database (XAMPP MySQL).

```bash
/opt/lampp/bin/mysql -h 127.0.0.1 -P 3306 -u root -e "CREATE DATABASE IF NOT EXISTS \`habit-tracker\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

6. Generate app key and run migrations.

```bash
/opt/lampp/bin/php artisan key:generate
/opt/lampp/bin/php artisan migrate
```

7. Link public storage (required for profile photos).

```bash
/opt/lampp/bin/php artisan storage:link
```

8. Install frontend dependencies and build assets.

```bash
npm install
npm run build
```

9. Run local app.

```bash
/opt/lampp/bin/php artisan serve
```

Open: `http://127.0.0.1:8000`

## Development Commands

Run Vite in dev mode:

```bash
npm run dev
```

Run tests:

```bash
/opt/lampp/bin/php artisan test
```

Run scheduler loop in local terminal:

```bash
/opt/lampp/bin/php artisan schedule:work
```

Manual reminder check command:

```bash
/opt/lampp/bin/php artisan habit:check-reminders
/opt/lampp/bin/php artisan todo:check-reminders
```

## Scheduler / Cron

The app schedules `habit:check-reminders` and `todo:check-reminders` every minute in `app/Console/Kernel.php`.

Production cron example:

```cron
* * * * * /opt/lampp/bin/php /path/to/habit-tracker/artisan schedule:run >> /dev/null 2>&1
```

## Main Routes (Blade Pages)

All routes below require authentication unless stated otherwise.

| Method | Path | Purpose |
| --- | --- | --- |
| `GET` | `/` | Redirect to login or dashboard |
| `GET` | `/dashboard` | Main dashboard page |
| `GET` | `/habits` | Habit list page |
| `GET` | `/habits/create` | Create habit form |
| `GET` | `/habits/{habit}` | Habit detail page |
| `GET` | `/habits/{habit}/edit` | Edit habit form |
| `GET` | `/todos` | Todo list page |
| `GET` | `/todos/create` | Create todo form |
| `GET` | `/todos/{todo}/edit` | Edit todo form |
| `GET` | `/focus-sessions` | Focus timer and history page |
| `GET` | `/notifications` | Notifications page |
| `GET` | `/profile` | Profile settings and activity page |

## API Endpoints (Detailed)

These are internal JSON endpoints consumed by Blade + JS modules.

### Auth and Headers

- All `/ajax/*` endpoints require authenticated session (`web` guard).
- For `POST/PATCH` requests, send CSRF token from `<meta name="csrf-token">`.
- Recommended header for API calls:
  - `Accept: application/json`
  - `X-CSRF-TOKEN: <token>`

### Shared JSON Response Shape

Success:

```json
{
  "success": true,
  "message": "Human-readable message",
  "data": {}
}
```

Error:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {}
}
```

Common status codes:

- `200` success
- `401` unauthenticated
- `403` forbidden (ownership/authorization)
- `404` resource not found
- `422` validation/domain error

### Dashboard API

| Method | Path | Request | Success `data` |
| --- | --- | --- | --- |
| `GET` | `/ajax/dashboard/summary` | none | `total_active_habits`, `completed_today`, `current_streak`, `focus_minutes_today`, `unread_notifications`, `pending_todos`, `due_today_todos` |
| `GET` | `/ajax/dashboard/today-habits` | none | `habits[]`, `notifications_preview[]` |

`habits[]` item shape:

- `id`, `title`, `frequency`, `target_count`, `reminder_time`
- `today_log` (`id`, `status`, `qty`) or `null`
- `is_completed_today` (boolean)

### Habit Log API

| Method | Path | Request Body | Success `data` |
| --- | --- | --- | --- |
| `POST` | `/ajax/habit-logs/quick-checkin` | `habit_id` (required), `status` (`completed/skipped/missed`, optional), `qty` (optional), `note` (optional), `log_date` (optional) | `action` (`created/updated`), `log` (`HabitLogResource`) |

Notes:

- Quick check-in always writes to **today's date**.
- `status` defaults to `completed` and `qty` defaults to `1` if omitted.

### Focus Session API

| Method | Path | Request Body | Success `data` |
| --- | --- | --- | --- |
| `POST` | `/ajax/focus-sessions/start` | `habit_id` (optional, must belong to user), `planned_duration_minutes` (`1..1440`, optional), `note` (optional) | `session` (`FocusSessionResource`), `reused_existing_session` (boolean) |
| `POST` | `/ajax/focus-sessions/{focusSession}/stop` | `focused_duration_seconds` (required), `unfocused_duration_seconds` (required), `interruption_count` (required), `status` (`completed/cancelled`) | `session` (`FocusSessionResource`) |
| `GET` | `/ajax/focus-sessions/today-summary` | none | `total_sessions`, `focus_minutes_today`, `background_minutes_today`, `interruption_count_today` |

Notes:

- `start` returns `reused_existing_session=true` if a running session already exists.
- `stop` returns `422` with message `Sesi fokus sudah tidak aktif.` if the session is no longer running.

### Notification API

| Method | Path | Request | Success `data` |
| --- | --- | --- | --- |
| `GET` | `/ajax/notifications` | Query: `limit` (`1..50`, default `10`) | `notifications[]`, `unread_count` |
| `GET` | `/ajax/notifications/unread-count` | none | `unread_count` |

`notifications[]` item shape:

- `id`, `type`, `title`, `message`, `data`
- `is_read`, `read_at`, `scheduled_for`, `created_at`, `updated_at`

### Hybrid Endpoints (Redirect or JSON)

These endpoints are in web routes and return redirect by default. If request `expectsJson()`, they return JSON envelope.

| Method | Path | Purpose |
| --- | --- | --- |
| `POST` | `/notifications/{notification}/read` | Mark one notification as read |
| `POST` | `/notifications/read-all` | Mark all notifications as read |

## Project References

Implementation docs are available in [`docs/`](docs), including:

- `habit-tracker-implementation-roadmap.md`
- `habit-tracker-project-structure-naming.md`
- `habit-tracker-laravel12-service-business-flow.md`
- `habit-tracker-api-contract.md`
- `habit-tracker-testing-strategy.md`
- `shared-hosting-deploy-guide.md` (deployment workflow for shared hosting)

## Testing Status

Current test suite includes feature and unit tests across:

- auth flow,
- habits,
- habit logs,
- focus sessions,
- notifications,
- dashboard metrics,
- profile (including photo upload/remove).

## License

MIT
