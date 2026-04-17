# Task 14: Additional Features & Polish

## Mục tiêu
Bổ sung các tính năng nhỏ, polish UX/UI, error handling, notifications, và optimization.

## Công việc cần làm

### 1. Real-time Notifications (Browser Push)
**Package**: `laravel/echo` + `pusher/pusher-php-server` hoặc `reverb/reverb`

```bash
composer require laravel/reverb
npm install laravel-echo pusher-js
```

**File**: `resources/js/bootstrap.js` (update)

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: true,
    disableStats: true,
});
```

**Broadcast event** khi submission hoàn thành:
```php
event(new SubmissionCompleted($submission));
```

**Frontend**:
```js
.Echo.private(`user.${userId}`)
    .notification((notification) => {
        alert(`${notification.title}: ${notification.message}`);
    });
```

### 2. Email Notifications
**Mail**: Submission success/failure notifications.

```bash
composer require illuminate/mail  # đã có sẵn
```

**File**: `app/Mail/SubmissionCompleted.php`

```php
<?php

namespace App\Mail;

use App\Domain\Logwork\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public bool $success
    ) {}

    public function build()
    {
        $subject = $this->success 
            ? 'Logwork Submitted Successfully'
            : 'Logwork Submission Failed';

        return $this->subject($subject)
            ->view('emails.submission-notification')
            ->with([
                'submission' => $this->submission,
                'success' => $this->success,
            ]);
    }
}
```

**Blade view**: `resources/views/emails/submission-notification.blade.php`

### 3. Browser Notification API
**File**: `resources/js/notifications.js`

```js
// Request permission
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                console.log('Notifications enabled');
            }
        });
    }
}

// Show notification
function showNotification(title, body, icon = '/favicon.ico') {
    if (Notification.permission === 'granted') {
        new Notification(title, { body, icon });
    }
}

// Schedule notification before auto-submit
function scheduleReminder(submitTime) {
    const remindTime = new Date(submitTime);
    remindTime.setMinutes(remindTime.getMinutes() - 10);
    
    setTimeout(() => {
        showNotification(
            'Auto-submit reminder',
            'Your logwork will be submitted in 10 minutes'
        );
    }, remindTime - Date.now());
}
```

**Blade**: Add script to enable notifications nếu `notify_before_submit` enabled.

### 4. Export Data (Excel/PDF)
**Package**: `maatwebsite/excel`

```bash
composer require maatwebsite/excel
```

**Export DailyLogs**:
```php
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyLogExport implements FromCollection, WithHeadings, WithMapping
{
    protected $logs;

    public function __construct($logs)
    {
        $this->logs = $logs;
    }

    public function collection()
    {
        return $this->logs;
    }

    public function map($log): array
    {
        return [
            $log->date->format('d/m/Y'),
            $log->summary,
            $log->tomorrow_plan,
            $log->tasks->sum('estimated_time') . ' phút',
            $log->tasks->count() . ' tasks',
            $log->is_submit_chat ? 'Yes' : 'No',
            $log->submitted_at?->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return ['Date', 'Summary', 'Tomorrow Plan', 'Total Time', 'Task Count', 'Submitted', 'Submitted At'];
    }
}
```

**Route**: `Route::get('/export', [ExportController::class, 'logs'])->name('export.logs');`

### 5. Dashboard Analytics (Charts)
**Package**: `livewire/livewire` (already có) + `consoletv/charts` hoặc `apexcharts`

```bash
npm install chart.js vue-chartjs  # Nếu dùng Vue/React
# OR
composer require consoletv/charts  # cho Laravel Blade
```

**Widget**: `app/Filament/Widgets/WeeklyProgressChart.php`

```php
<?php

use App\Domain\Logwork\Models\DailyLog;
use Filament\Widgets\ChartWidget;

class WeeklyProgressChart extends ChartWidget
{
    protected static ?string $heading = 'Weekly Progress';

    protected function getData(): array
    {
        $dates = collect();
        $progress = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $logs = DailyLog::whereDate('date', $date)->get();
            
            $avgProgress = $logs->isEmpty() ? 0 : $logs->avg(fn($log) => $log->tasks->avg('progress_percent'));
            
            $dates->push($date->format('D'));
            $progress->push(round($avgProgress, 1));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Avg Progress %',
                    'data' => $progress->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $dates->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
```

### 6. Dark Mode Toggle
**Tailwind**: Đã hỗ trợ dark mode.

`tailwind.config.js`:
```js
darkMode: 'class', // manual toggle
```

**Blade layout**:
```blade
<button @click="darkMode = !darkMode" class="p-2">
    <svg x-show="!darkMode" ...>Sun icon</svg>
    <svg x-show="darkMode" ...>Moon icon</svg>
</button>

<html :class="{ 'dark': darkMode }">
```

### 7. Mobile Responsive Enhancements
- Sử dụng Tailwind responsive classes: `md:`, `lg:`, `sm:`
- Task table trên mobile: Chuyển thành card layout
- Pagination: `flex justify-center`

### 8. Offline Mode (PWA)
```bash
# Laravel PWA
composer require silviolleite/laravelpwa
```

`config/laravelpwa.php`:
```php
'name' => 'Daily Logwork',
'short_name' => 'Logwork',
'start_url' => '/',
'background_color' => '#ffffff',
'theme_color' => '#3b82f6',
'display' => 'standalone',
'icons' => [
    '192x192' => '/icons/icon-192x192.png',
    '512x512' => '/icons/icon-512x512.png',
],
```

### 9. Keyboard Shortcuts
**File**: `resources/js/shortcuts.js`

```js
document.addEventListener('keydown', (e) => {
    // Ctrl + S = Save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.getElementById('logwork-form')?.submit();
    }
    
    // Ctrl + P = Parse AI
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        document.getElementById('parse-btn')?.click();
    }
    
    // Esc = Close modal (future)
    if (e.key === 'Escape') {
        // Close modals
    }
});
```

### 10. Activity Log
**Package**: `spatie/laravel-activitylog`

```bash
composer require spatie/laravel-activitylog
```

**Log actions**:
```php
use Spatie\Activitylog\Traits\LogsActivity;

class DailyLog extends Model
{
    use LogsActivity;
    
    protected static $logFillable = true;
    protected static $logName = 'daily_log';
}
```

**View activity** trong Filament Resource.

### 11. File Attachments (optional)
Nếu muốn upload file cho từng task.

```bash
composer require intervention/image  # image processing
```

**File**: `app/Models/Task.php` - add `attachment_path` field.

### 12. Localization (i18n)
Hỗ trợ tiếng Việt/Anh.

**Files**: `resources/lang/vi/validation.php`, `resources/lang/en/validation.php`

```php
// vi/validation.php
'required' => 'Trường :attribute là bắt buộc.',
```

**Config**: `config/app.php`
```php
'locale' => 'vi',
'fallback_locale' => 'en',
```

### 13. API Documentation (Swagger/OpenAPI)
**Package**: `darkaonline/l5-swagger`

```bash
composer require darkaonline/l5-swagger
```

Generate API docs tại `/api/documentation`.

### 14. Rate Limiting Enhancements
Throttle per user, IP:

```php
// In RouteServiceProvider
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('parse-ai', function (Request $request) {
    return Limit::perHour(30)->by($request->user()->id);
});
```

### 15. Webhook Retry Strategy (Enhanced)
Better backoff với jitter:

```php
public function backoff(): array
{
    // Random jitter: 5±2s, 25±5s, 125±25s
    return [
        rand(3, 7),
        rand(20, 30),
        rand(100, 150),
    ];
}
```

### 16. Better Error Pages
Create custom error pages:
- `resources/views/errors/404.blade.php`
- `resources/views/errors/403.blade.php`
- `resources/views/errors/500.blade.php`

### 17. Search Functionality
**File**: `app/Filament/Resources/DailyLogResource.php` - add search globally.

```php
public static function getGloballySearchableAttributes(): array
{
    return ['summary', 'tasks.title'];
}
```

### 18. Bulk Actions
**File**: `DailyLogResource.php` - bulk delete, bulk export.

```php
Tables\Actions\BulkActionGroup::make([
    Tables\Actions\DeleteBulkAction::make(),
    \App\Filament\Resources\DailyLogResource\Actions\BulkExportAction::make(),
]);
```

### 19. Demo Mode (tạm)
Tạo button "Fill with Demo Data" trên UI để user test nhanh.

### 20. CLI Tools
**Artisan Commands** (đã có mấy cái):
```bash
php artisan logwork:today          # Show today log
php artisan logwork:submit         # Force submit today
php artisan logwork:sync-google    # Sync unsent logs
php artisan logwork:stats          # Show stats
```

### 21. Activity Feed
Widget hiển thị recent activities:
```php
activity()
    ->inLog('daily_log')
    ->latest()
    ->take(10)
    ->get();
```

### 22. Custom Validation Rules
**File**: `app/Rules/ValidTaskProgress.php`

```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidTaskProgress implements Rule
{
    public function passes($attribute, $value)
    {
        return is_numeric($value) && $value >= 0 && $value <= 100;
    }
}
```

### 23. API Rate Limiting per Endpoint
```php
Route::middleware(['throttle:60,1'])->group(...); // General
Route::post('/logworks/parse')->middleware('throttle:10,1'); // AI parse
```

### 24. Queue Priorities
High priority cho submission:
```php
SubmitGoogleFormJob::dispatch($logId, $subId)->onQueue('high');
```

### 25. Soft Deletes (optional)
**File**: Add `use SoftDeletes` vào models, migration add `deleted_at`.

---

## Files cần tạo (optional)
- `resources/js/bootstrap.js` (Echo setup)
- `resources/js/notifications.js`
- `resources/js/shortcuts.js`
- `app/Mail/SubmissionCompleted.php`
- `resources/views/emails/submission-notification.blade.php`
- `app/Jobs/ProcessSubmissionJob.php` (nếu cần)
- Custom error pages

## Kiểm tra
```bash
# Test notifications
php artisan queue:work

# Check charts
php artisan serve

# Test shortcuts
Open browser console
```

## Notes
- **Progressive enhancement**: Từ cơ bản → nâng cao
- **Performance**: Optimize DB queries, eager loading, caching
- **UX**: Keyboard shortcuts, dark mode, mobile-first
- **Monitoring**: Logging, notifications, health checks

---

**Status**: ⏳ Pending  
**Priority**: Low (polish sau core done)  
**Dependencies**: Core system hoàn thành  
**Estimated time**: Tùy feature, từ 1-3 ngày
