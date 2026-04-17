# Task 10: Google Form Integration

## Mục tiêu
Implement Google Form submission,包括如何获取form endpoint, mapping fields, và xử lý response.

## Công việc cần làm

### 1. Research Google Form Structure
Cần tìm hiểu Google Form HTML structure để lấy entry IDs.

**Cách lấy entry IDs**:
1. Mở Google Form trong browser
2. Right-click → View Page Source
3. Tìm input fields: `<input type="text" name="entry.123456789">`
4. `entry.123456789` là field ID

Ví dụ:
```
Summary field: <input name="entry.111111111">
Tomorrow: <input name="entry.222222222">
Task Title: <input name="entry.333333333">
Task Progress: <input name="entry.444444444">
Task Time: <input name="entry.555555555">
```

### 2. Tạo GoogleFormService (đã có ở Task 5)
File: `app/Infrastructure/Services/GoogleFormSubmitter.php` (update)

```php
<?php

namespace App\Infrastructure\Services;

use App\Domain\Logwork\Models\DailyLog;
use App\Domain\Logwork\Services\SettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleFormSubmitter
{
    protected array $fieldMapping;

    public function __construct()
    {
        // Load field mapping từ config
        $this->fieldMapping = config('logwork.google_form_mapping', [
            'summary' => env('GOOGLE_FORM_ENTRY_SUMMARY', 'entry.xxxxx'),
            'tomorrow_plan' => env('GOOGLE_FORM_ENTRY_TOMORROW', 'entry.yyyyy'),
            'date' => env('GOOGLE_FORM_ENTRY_DATE', 'entry.wwwww'),
            'tasks_prefix' => env('GOOGLE_FORM_ENTRY_TASKS_PREFIX', 'entry.ttttt'),
        ]);
    }

    public function submit(DailyLog $dailyLog): array
    {
        $formUrl = SettingService::getGoogleFormUrl();

        if (!$formUrl) {
            throw new \RuntimeException('Google Form URL chưa được cấu hình trong Global Settings');
        }

        // Convert view URL to formResponse URL
        $submitUrl = $this->prepareFormUrl($formUrl);

        $formData = $this->buildFormData($dailyLog);

        Log::info('Submitting to Google Form', [
            'url' => $submitUrl,
            'data_keys' => array_keys($formData),
        ]);

        $response = Http::asForm()->timeout(30)->post($submitUrl, $formData);

        if ($response->successful()) {
            Log::info('Google Form submission successful', [
                'daily_log_id' => $dailyLog->id,
                'status' => $response->status(),
            ]);
        } else {
            Log::warning('Google Form submission returned error', [
                'daily_log_id' => $dailyLog->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return [
            'status' => $response->status(),
            'body' => $response->body(),
            'headers' => $response->headers(),
        ];
    }

    protected function prepareFormUrl(string $url): string
    {
        // Google Form có 2 dạng URL:
        // View: https://docs.google.com/forms/d/e/{FORM_ID}/viewform
        // Submit: https://docs.google.com/forms/d/e/{FORM_ID}/formResponse
        
        if (str_contains($url, '/viewform')) {
            return str_replace('/viewform', '/formResponse', $url);
        }
        
        if (str_contains($url, '/edit')) {
            throw new \RuntimeException('Vui lòng sử dụng view URL hoặc formResponse URL, không phải edit URL');
        }

        return $url;
    }

    protected function buildFormData(DailyLog $dailyLog): array
    {
        $data = [];

        // Date field
        $data[$this->fieldMapping['date']] = $dailyLog->date->format('Y-m-d');

        // Summary
        $data[$this->fieldMapping['summary']] = $dailyLog->summary ?? 'No summary';

        // Tomorrow plan
        $data[$this->fieldMapping['tomorrow_plan']] = $dailyLog->tomorrow_plan ?? 'No plan';

        // Tasks (mỗi task là nhiều fields)
        $tasks = $dailyLog->tasks;
        $prefix = $this->fieldMapping['tasks_prefix'];

        foreach ($tasks as $index => $task) {
            // Ví dụ: nếu form có 3 inputs cho mỗi task:
            // entry.tasks_0_title, entry.tasks_0_progress, entry.tasks_0_time
            $data["{$prefix}_{$index}_title"] = $task->title;
            $data["{$prefix}_{$index}_progress"] = $task->progress_percent . '%';
            $data["{$prefix}_{$index}_time"] = $task->estimated_time . ' phút';
        }

        // Submit button name (bắt buộc cho Google Form)
        $data['submit'] = 'Submit';

        return $data;
    }
}
```

### 3. Tạo Config Form Mapping
**File**: `config/logwork.php` (update)

```php
<?php

return [
    'google_form_mapping' => [
        // Entry IDs lấy từ Google Form HTML
        'summary' => env('GOOGLE_FORM_ENTRY_SUMMARY', 'entry.xxxxx'),
        'tomorrow_plan' => env('GOOGLE_FORM_ENTRY_TOMORROW', 'entry.yyyyy'),
        'date' => env('GOOGLE_FORM_ENTRY_DATE', 'entry.wwwww'),
        'tasks_prefix' => env('GOOGLE_FORM_ENTRY_TASKS_PREFIX', 'entry.ttttt'),
    ],

    'queue' => [
        'default_connection' => env('QUEUE_CONNECTION', 'database'),
        'retry_attempts' => env('QUEUE_RETRY_ATTEMPTS', 3),
    ],

    'submission' => [
        'auto_submit' => env('AUTO_SUBMIT_ENABLED', false),
        'default_time' => env('DEFAULT_SUBMIT_TIME', '17:00:00'),
    ],
];
```

### 4. Tạo Env Variables
**File**: `.env` (add)

```env
# Google Form
GOOGLE_FORM_URL=https://docs.google.com/forms/d/e/FORM_ID/viewform
GOOGLE_FORM_ENTRY_SUMMARY=entry.111111111
GOOGLE_FORM_ENTRY_TOMORROW=entry.222222222
GOOGLE_FORM_ENTRY_DATE=entry.333333333
GOOGLE_FORM_ENTRY_TASKS_PREFIX=entry.444444444

# Submission
DEFAULT_SUBMIT_TIME=17:00:00
AUTO_SUBMIT_ENABLED=false
QUEUE_RETRY_ATTEMPTS=3
```

### 5. Test với Google Form Mẫu
Tạo test form:

1. Vào https://docs.google.com/forms
2. Tạo form mới với fields:
   - Date (Short answer)
   - Summary (Paragraph)
   - Tomorrow Plan (Paragraph)
   - Task Title (Short answer) - có thể thêm 3 lần
   - Task Progress (Short answer)
   - Task Time (Short answer)
3. Get Form ID từ URL:
   `https://docs.google.com/forms/d/e/1FAIpQLS.../viewform`
4. Lấy entry IDs từ Page Source

**Test script**:
```bash
php artisan tinker
>>> $log = App\Models\DailyLog::first();
>>> $submitter = new App\Infrastructure\Services\GoogleFormSubmitter();
>>> $result = $submitter->submit($log);
>>> dd($result);
```

### 6. Tạo Command Test Submission
**File**: `app/Console/Commands/TestSubmission.php`

```php
<?php

namespace App\Console\Commands;

use App\Domain\Logwork\Models\DailyLog;
use App\Infrastructure\Services\GoogleFormSubmitter;
use Illuminate\Console\Command;

class TestSubmission extends Command
{
    protected $signature = 'test:submission {dailyLogId?}';
    protected $description = 'Test Google Form submission for a daily log';

    public function handle(GoogleFormSubmitter $submitter): int
    {
        $dailyLogId = $this->argument('dailyLogId') ?? DailyLog::latest()->first()?->id;

        if (!$dailyLogId) {
            $this->error('No daily log found. Create one first.');
            return self::FAILURE;
        }

        $dailyLog = DailyLog::with(['tasks'])->findOrFail($dailyLogId);

        $this->info("Testing submission for log ID: {$dailyLogId}");
        $this->info("Date: {$dailyLog->date}");
        $this->info("Tasks: " . $dailyLog->tasks->count());

        try {
            $result = $submitter->submit($dailyLog);
            $this->info('Submission successful!');
            $this->info('Status: ' . $result['status']);
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Submission failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
```

Register command trong `app/Console/Kernel.php`:
```php
protected $commands = [
    \App\Console\Commands\TestSubmission::class,
];
```

### 7. Retry Job (nếu failed) - Đã có trong SubmitGoogleFormJob
Kiểm tra logic retry với exponential backoff.

### 8. Failed Job Handling
Tạo Notification cho admin khi submission failed:

**File**: `app/Notifications/SubmissionFailed.php`

```php
<?php

namespace App\Notifications;

use App\Domain\Logwork\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubmissionFailed extends Notification
{
    use Queueable;

    public function __construct(
        public Submission $submission,
        public string $error
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Google Form Submission Failed')
            ->line('Submission for logwork dated ' . $this->submission->dailyLog->date->format('d/m/Y') . ' failed.')
            ->line('Error: ' . $this->error)
            ->line('Retry count: ' . $this->submission->retry_count)
            ->action('View Submission', url('/admin/submissions/' . $this->submission->id))
            ->line('Please check and retry manually.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'submission_id' => $this->submission->id,
            'daily_log_id' => $this->submission->daily_log_id,
            'error' => $this->error,
        ];
    }
}
```

Update `SubmitGoogleFormJob::failed()`:
```php
public function failed(Throwable $exception): void
{
    Log::critical('Google Form submission job failed permanently', [
        'daily_log_id' => $this->dailyLogId,
        'submission_id' => $this->submissionId,
        'error' => $exception->getMessage(),
    ]);

    // Send notification to admin users
    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        $admin->notify(new SubmissionFailed($submission, $exception->getMessage()));
    }
}
```

## Files cần tạo
- Update `app/Infrastructure/Services/GoogleFormSubmitter.php`
- Update `config/logwork.php`
- Update `.env` với Google Form vars
- `app/Console/Commands/TestSubmission.php`
- `app/Notifications/SubmissionFailed.php`
- Edit `app/Jobs/SubmitGoogleFormJob.php` (update failed() method)

## Kiểm tra
```bash
# Compile config
php artisan config:cache

# Test command
php artisan test:submission [dailyLogId]

# Check logs
tail -f storage/logs/laravel.log
```

## Notes
- **Entry IDs**: Phải lấy từ Google Form source HTML, không phải URL
- **FormResponse endpoint**: Luôn là `/formResponse`, không phải `/viewform`
- **HTTP method**: POST với `application/x-www-form-urlencoded`
- **Response**: Google Form trả về 200 + redirect page, không có JSON
- **Rate limits**: Google Form có limit ~1000 submissions/ngày/IP
- **CSRF**: Google Form không dùng CSRF token
- **CAPTCHA**: Nếu form bật CAPTCHA, cần xử lý riêng (bất khả thi với auto submit)

---

**Status**: ⏳ Pending  
**Priority**: High  
**Dependencies**: Task 5 (Queue), Task 4 (Services)  
**Estimated time**: 25 phút
