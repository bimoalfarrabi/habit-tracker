
# Habit Tracker - Laravel 12 Documentation: Request Validation & Controller Skeleton

## 1. Tujuan Dokumen
Dokumen ini melanjutkan blueprint teknis **Habit Tracker Laravel 12** dengan fokus pada:
- struktur request validation
- aturan validasi per modul
- controller skeleton
- action method yang direkomendasikan
- service responsibility
- response pattern untuk web dan AJAX
- urutan implementasi setelah migration dan model

Dokumen ini diasumsikan berjalan di stack:
- Laravel 12
- PHP 8.2
- MySQL
- Vite + Tailwind CSS
- shared hosting friendly
- notifikasi berbasis cron
- tanpa Reverb

---

## 2. Prinsip Validasi

### 2.1 Validasi harus dipisah dari controller
Gunakan **Form Request** agar:
- controller tetap tipis
- aturan validasi reusable
- authorization lebih rapi
- error handling konsisten

### 2.2 Validasi harus domain-aware
Contoh:
- user hanya boleh log habit miliknya sendiri
- user tidak boleh mengubah habit user lain
- session fokus hanya boleh dihentikan oleh pemilik session
- notifikasi hanya boleh dibaca oleh user yang memiliki notifikasi itu

### 2.3 Validasi minimum dulu, business rule di service
Contoh:
- format `reminder_time` → validasi request
- cek duplikasi reminder / logika streak → service layer
- pencegahan duplicate log → gabungan validasi + DB constraint + service

---

## 3. Struktur File yang Direkomendasikan

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── HabitController.php
│   │   ├── HabitLogController.php
│   │   ├── FocusSessionController.php
│   │   └── NotificationController.php
│   └── Requests/
│       ├── Habit/
│       │   ├── StoreHabitRequest.php
│       │   └── UpdateHabitRequest.php
│       ├── HabitLog/
│       │   ├── StoreHabitLogRequest.php
│       │   └── UpdateHabitLogRequest.php
│       ├── FocusSession/
│       │   ├── StartFocusSessionRequest.php
│       │   └── StopFocusSessionRequest.php
│       └── Notification/
│           └── MarkNotificationAsReadRequest.php
├── Services/
│   ├── DashboardStatsService.php
│   ├── HabitReminderService.php
│   ├── HabitLogService.php
│   ├── FocusSessionService.php
│   └── NotificationService.php
```

---

## 4. Request Validation Planning

## 4.1 StoreHabitRequest

### Tujuan
Validasi saat user membuat habit baru.

### Rules
- `title`: required|string|max:255
- `description`: nullable|string
- `frequency`: required|in:daily,weekly
- `target_count`: required|integer|min:1|max:1000
- `reminder_time`: nullable|date_format:H:i
- `color`: nullable|string|max:30
- `icon`: nullable|string|max:50
- `is_active`: nullable|boolean

### Authorization
User harus sudah login.

### Skeleton
```php
namespace App\Http\Requests\Habit;

use Illuminate\Foundation\Http\FormRequest;

class StoreHabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency' => ['required', 'in:daily,weekly'],
            'target_count' => ['required', 'integer', 'min:1', 'max:1000'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
```

---

## 4.2 UpdateHabitRequest

### Tujuan
Validasi saat user mengubah habit.

### Rules
Sama seperti `StoreHabitRequest`.

### Authorization
Pastikan habit milik user yang login.

### Skeleton
```php
namespace App\Http\Requests\Habit;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Habit;

class UpdateHabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $habit = $this->route('habit');

        return auth()->check()
            && $habit instanceof Habit
            && $habit->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency' => ['required', 'in:daily,weekly'],
            'target_count' => ['required', 'integer', 'min:1', 'max:1000'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
```

---

## 4.3 StoreHabitLogRequest

### Tujuan
Validasi saat user melakukan check-in atau membuat log habit.

### Rules
- `habit_id`: required|exists:habits,id
- `log_date`: required|date
- `status`: required|in:completed,skipped,missed
- `qty`: nullable|integer|min:1|max:100000
- `note`: nullable|string

### Authorization
Habit yang di-log harus milik user sendiri.

### Skeleton
```php
namespace App\Http\Requests\HabitLog;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Habit;

class StoreHabitLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $habitId = $this->input('habit_id');

        return Habit::where('id', $habitId)
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function rules(): array
    {
        return [
            'habit_id' => ['required', 'exists:habits,id'],
            'log_date' => ['required', 'date'],
            'status' => ['required', 'in:completed,skipped,missed'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'note' => ['nullable', 'string'],
        ];
    }
}
```

### Catatan
Untuk MVP, kamu juga bisa memaksa `log_date = today()` di service layer agar user tidak sembarang menulis histori masa lalu/future.

---

## 4.4 UpdateHabitLogRequest

### Tujuan
Validasi saat user mengedit log yang sudah ada.

### Authorization
Pastikan log milik user yang login.

### Skeleton
```php
namespace App\Http\Requests\HabitLog;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\HabitLog;

class UpdateHabitLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $habitLog = $this->route('habitLog');

        return auth()->check()
            && $habitLog instanceof HabitLog
            && $habitLog->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:completed,skipped,missed'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'note' => ['nullable', 'string'],
        ];
    }
}
```

---

## 4.5 StartFocusSessionRequest

### Tujuan
Validasi saat user memulai sesi fokus.

### Rules
- `habit_id`: nullable|exists:habits,id
- `planned_duration_minutes`: nullable|integer|min:1|max:1440
- `note`: nullable|string

### Authorization
Jika `habit_id` ada, habit harus milik user.

### Skeleton
```php
namespace App\Http\Requests\FocusSession;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Habit;

class StartFocusSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $habitId = $this->input('habit_id');

        if (!$habitId) {
            return true;
        }

        return Habit::where('id', $habitId)
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function rules(): array
    {
        return [
            'habit_id' => ['nullable', 'exists:habits,id'],
            'planned_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'note' => ['nullable', 'string'],
        ];
    }
}
```

---

## 4.6 StopFocusSessionRequest

### Tujuan
Validasi saat user menghentikan sesi fokus.

### Rules
- `focused_duration_seconds`: required|integer|min:0
- `unfocused_duration_seconds`: required|integer|min:0
- `interruption_count`: required|integer|min:0
- `status`: required|in:completed,cancelled

### Authorization
Sesi fokus harus milik user.

### Skeleton
```php
namespace App\Http\Requests\FocusSession;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\FocusSession;

class StopFocusSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $focusSession = $this->route('focusSession');

        return auth()->check()
            && $focusSession instanceof FocusSession
            && $focusSession->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'focused_duration_seconds' => ['required', 'integer', 'min:0'],
            'unfocused_duration_seconds' => ['required', 'integer', 'min:0'],
            'interruption_count' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:completed,cancelled'],
        ];
    }
}
```

---

## 4.7 MarkNotificationAsReadRequest

### Tujuan
Validasi saat user menandai notifikasi sebagai sudah dibaca.

### Authorization
Notifikasi harus milik user.

### Skeleton
```php
namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserNotification;

class MarkNotificationAsReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $notification = $this->route('notification');

        return auth()->check()
            && $notification instanceof UserNotification
            && $notification->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [];
    }
}
```

---

## 5. Controller Planning

## 5.1 DashboardController

### Tanggung jawab
- menampilkan dashboard utama
- memanggil service statistik
- menampilkan ringkasan habit, focus, dan notifikasi

### Method yang disarankan
- `index()`

### Skeleton
```php
namespace App\Http\Controllers;

use App\Services\DashboardStatsService;

class DashboardController extends Controller
{
    public function index(DashboardStatsService $dashboardStatsService)
    {
        $stats = $dashboardStatsService->getForUser(auth()->user());

        return view('dashboard.index', compact('stats'));
    }
}
```

### Output view
Dashboard minimal berisi:
- total habit aktif
- completed today
- current streak
- focus duration hari ini
- unread notifications

---

## 5.2 HabitController

### Tanggung jawab
- menampilkan daftar habit
- menampilkan form create/edit
- menyimpan habit baru
- mengubah habit
- archive / delete habit

### Method yang disarankan
- `index()`
- `create()`
- `store(StoreHabitRequest $request)`
- `show(Habit $habit)` optional
- `edit(Habit $habit)`
- `update(UpdateHabitRequest $request, Habit $habit)`
- `destroy(Habit $habit)`
- `archive(Habit $habit)` optional
- `toggleActive(Habit $habit)` optional

### Skeleton
```php
namespace App\Http\Controllers;

use App\Http\Requests\Habit\StoreHabitRequest;
use App\Http\Requests\Habit\UpdateHabitRequest;
use App\Models\Habit;

class HabitController extends Controller
{
    public function index()
    {
        $habits = Habit::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('habits.index', compact('habits'));
    }

    public function create()
    {
        return view('habits.create');
    }

    public function store(StoreHabitRequest $request)
    {
        Habit::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('habits.index')
            ->with('success', 'Habit berhasil dibuat.');
    }

    public function edit(Habit $habit)
    {
        abort_unless($habit->user_id === auth()->id(), 403);

        return view('habits.edit', compact('habit'));
    }

    public function update(UpdateHabitRequest $request, Habit $habit)
    {
        $habit->update($request->validated());

        return redirect()
            ->route('habits.index')
            ->with('success', 'Habit berhasil diperbarui.');
    }

    public function destroy(Habit $habit)
    {
        abort_unless($habit->user_id === auth()->id(), 403);

        $habit->delete();

        return redirect()
            ->route('habits.index')
            ->with('success', 'Habit berhasil dihapus.');
    }
}
```

### Catatan
Kalau kamu ingin histori tetap aman, lebih baik gunakan `archive()` daripada `delete()`.

---

## 5.3 HabitLogController

### Tanggung jawab
- membuat log habit
- update log habit
- menampilkan histori log jika diperlukan

### Method yang disarankan
- `store(StoreHabitLogRequest $request)`
- `update(UpdateHabitLogRequest $request, HabitLog $habitLog)`
- `destroy(HabitLog $habitLog)` optional
- `history(Habit $habit)` optional

### Skeleton
```php
namespace App\Http\Controllers;

use App\Http\Requests\HabitLog\StoreHabitLogRequest;
use App\Http\Requests\HabitLog\UpdateHabitLogRequest;
use App\Models\HabitLog;
use App\Services\HabitLogService;

class HabitLogController extends Controller
{
    public function store(StoreHabitLogRequest $request, HabitLogService $habitLogService)
    {
        $habitLogService->storeForUser(auth()->user(), $request->validated());

        return redirect()
            ->back()
            ->with('success', 'Log habit berhasil disimpan.');
    }

    public function update(UpdateHabitLogRequest $request, HabitLog $habitLog, HabitLogService $habitLogService)
    {
        $habitLogService->updateForUser(auth()->user(), $habitLog, $request->validated());

        return redirect()
            ->back()
            ->with('success', 'Log habit berhasil diperbarui.');
    }
}
```

### Kenapa pakai service?
Karena log habit punya business rule:
- satu habit satu log per tanggal
- update jika sudah ada
- memengaruhi streak
- memengaruhi statistik

Semua itu lebih bersih di service.

---

## 5.4 FocusSessionController

### Tanggung jawab
- memulai sesi fokus
- menghentikan sesi fokus
- menampilkan histori sesi fokus

### Method yang disarankan
- `index()` optional
- `start(StartFocusSessionRequest $request)`
- `stop(StopFocusSessionRequest $request, FocusSession $focusSession)`
- `show(FocusSession $focusSession)` optional

### Skeleton
```php
namespace App\Http\Controllers;

use App\Http\Requests\FocusSession\StartFocusSessionRequest;
use App\Http\Requests\FocusSession\StopFocusSessionRequest;
use App\Models\FocusSession;
use App\Services\FocusSessionService;

class FocusSessionController extends Controller
{
    public function start(StartFocusSessionRequest $request, FocusSessionService $focusSessionService)
    {
        $session = $focusSessionService->start(auth()->user(), $request->validated());

        return response()->json([
            'message' => 'Sesi fokus dimulai.',
            'data' => $session,
        ]);
    }

    public function stop(
        StopFocusSessionRequest $request,
        FocusSession $focusSession,
        FocusSessionService $focusSessionService
    ) {
        $session = $focusSessionService->stop(auth()->user(), $focusSession, $request->validated());

        return response()->json([
            'message' => 'Sesi fokus dihentikan.',
            'data' => $session,
        ]);
    }
}
```

### Catatan
Flow timer lebih cocok pakai AJAX/JSON daripada form biasa.

---

## 5.5 NotificationController

### Tanggung jawab
- menampilkan daftar notifikasi
- mengambil notif untuk polling
- mark as read
- mark all as read

### Method yang disarankan
- `index()`
- `list()` untuk AJAX/polling
- `markAsRead(MarkNotificationAsReadRequest $request, UserNotification $notification)`
- `markAllAsRead()`

### Skeleton
```php
namespace App\Http\Controllers;

use App\Http\Requests\Notification\MarkNotificationAsReadRequest;
use App\Models\UserNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = UserNotification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function list()
    {
        $notifications = UserNotification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'data' => $notifications,
        ]);
    }

    public function markAsRead(
        MarkNotificationAsReadRequest $request,
        UserNotification $notification
    ) {
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ]);
    }

    public function markAllAsRead()
    {
        UserNotification::query()
            ->where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()
            ->back()
            ->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
```

---

## 6. Service Planning

## 6.1 DashboardStatsService

### Tanggung jawab
- hitung statistik dashboard
- satukan query dari beberapa modul
- return data siap pakai ke view

### Method yang disarankan
- `getForUser(User $user): array`

### Output contoh
```php
[
    'total_active_habits' => 5,
    'completed_today' => 3,
    'current_streak' => 7,
    'focus_minutes_today' => 120,
    'unread_notifications' => 2,
]
```

---

## 6.2 HabitLogService

### Tanggung jawab
- simpan atau update log habit
- cegah duplicate log
- sinkronkan `user_id`
- siapkan hook untuk streak nanti

### Method yang disarankan
- `storeForUser(User $user, array $data): HabitLog`
- `updateForUser(User $user, HabitLog $habitLog, array $data): HabitLog`

### Catatan
Pada `storeForUser`, kamu bisa gunakan `updateOrCreate()` berdasarkan:
- `habit_id`
- `log_date`

---

## 6.3 FocusSessionService

### Tanggung jawab
- memulai sesi
- menghentikan sesi
- menghitung total duration
- menjaga status sesi agar valid

### Method yang disarankan
- `start(User $user, array $data): FocusSession`
- `stop(User $user, FocusSession $session, array $data): FocusSession`

### Rule penting
- user tidak boleh punya dua sesi `running` sekaligus
- `stop()` hanya berlaku untuk sesi status `running`

---

## 6.4 NotificationService

### Tanggung jawab
- membuat notification manual jika diperlukan
- menandai dibaca
- ambil unread count
- bantu controller tetap tipis

### Method yang disarankan
- `createForUser(User $user, array $payload): UserNotification`
- `markAsRead(UserNotification $notification): void`
- `markAllAsRead(User $user): int`
- `getLatestForUser(User $user, int $limit = 10): Collection`

---

## 6.5 HabitReminderService

### Tanggung jawab
- dipanggil oleh command cron
- cek habit yang butuh reminder
- cek log hari ini
- cegah duplikasi reminder
- buat notifikasi

### Method yang disarankan
- `run(): void`
- `processUser(User $user): void`
- `shouldSendReminder(Habit $habit, Carbon $now): bool`

---

## 7. Response Pattern

## 7.1 Web flow
Gunakan redirect + flash message untuk:
- habit CRUD
- halaman notifikasi
- form biasa

Contoh:
```php
return redirect()
    ->route('habits.index')
    ->with('success', 'Habit berhasil dibuat.');
```

## 7.2 JSON flow
Gunakan JSON untuk:
- start/stop focus timer
- polling notifications
- action UI kecil yang tidak perlu reload

Contoh:
```php
return response()->json([
    'message' => 'OK',
    'data' => $payload,
]);
```

---

## 8. Routing Plan

### Web routes
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('habits', HabitController::class);

    Route::post('/habit-logs', [HabitLogController::class, 'store'])->name('habit-logs.store');
    Route::patch('/habit-logs/{habitLog}', [HabitLogController::class, 'update'])->name('habit-logs.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');
});
```

### AJAX / API-like routes
```php
Route::middleware(['auth'])->prefix('ajax')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'list'])->name('ajax.notifications.list');

    Route::post('/focus-sessions/start', [FocusSessionController::class, 'start'])
        ->name('focus-sessions.start');
    Route::post('/focus-sessions/{focusSession}/stop', [FocusSessionController::class, 'stop'])
        ->name('focus-sessions.stop');
});
```

---

## 9. Urutan Implementasi

### Step 1
- buat semua Form Request
- test authorization

### Step 2
- buat controller kosong
- pasang route dasar

### Step 3
- implement DashboardController
- implement HabitController

### Step 4
- implement HabitLogService + HabitLogController

### Step 5
- implement FocusSessionService + AJAX endpoint

### Step 6
- implement NotificationController + polling endpoint

### Step 7
- rapikan flash message, validation error, dan JSON response

---

## 10. Checklist Verifikasi

### Request validation
- invalid input gagal tervalidasi
- user tidak bisa akses resource milik user lain
- format waktu reminder valid
- focus session payload valid

### Controller
- method CRUD berjalan
- redirect sesuai
- JSON response sesuai
- unauthorized access ditolak

### Service
- duplicate habit log tidak dibuat ganda
- sesi fokus ganda tidak boleh
- mark notification read berjalan
- statistik dashboard muncul benar

---

## 11. Rekomendasi Implementasi Praktis

Untuk urutan paling aman:
1. `StoreHabitRequest`, `UpdateHabitRequest`
2. `HabitController`
3. `StoreHabitLogRequest`
4. `HabitLogService` + `HabitLogController`
5. `StartFocusSessionRequest`, `StopFocusSessionRequest`
6. `FocusSessionService` + `FocusSessionController`
7. `NotificationController`
8. `DashboardStatsService`
9. `HabitReminderService` untuk command cron

Dengan urutan ini, kamu bisa dapat:
- CRUD habit lebih dulu
- check-in lebih dulu
- dashboard dasar lebih dulu
- timer dan notifikasi belakangan tanpa merusak fondasi

---

## 12. Summary
Dokumen ini menetapkan fondasi implementasi untuk:
- Form Request validation
- controller skeleton Laravel 12
- service boundaries
- response pattern
- routing plan
- urutan implementasi

Setelah dokumen ini, langkah paling logis berikutnya adalah:
1. membuat **service class skeleton**
2. membuat **Blade layout + halaman utama**
3. atau langsung membuat **starter code Laravel** untuk module Habit dan HabitLog
