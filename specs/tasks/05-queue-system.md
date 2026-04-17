# Task 5: Queue System & Jobs

## Mục tiêu
Setup Laravel Queue để xử lý async Google Form submission, với retry policy và error handling.

## Công việc cần làm

### 1. Cấu hình Queue Driver
**File**: `.env`

```env
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database
```

### 2. Tạo Job: SubmitGoogleFormJob
**File**: `app/Jobs/SubmitGoogleFormJob.php`

```php
<?php

namespace App\Jobs;

use App\Domain\Logwork\Models\DailyLog;
use App\Domain\Logwork\Models\Submission;
use App\Infrastructure\Services\GoogleFormSubmitter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubmitGoogleFormJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $dailyLogId;
    public int $submissionId;
    public int $tries = 3; // Max retries = 3
    public int $timeout = 60; // 60 seconds timeout

    public function __construct(int $dailyLogId, int $submissionId)
    {
        $this->dailyLogId = $dailyLogId;
        $this->submissionId = $submissionId;
    }

    public function handle(GoogleFormSubmitter $submitter): void
    {
        $dailyLog = DailyLog::with(['tasks', 'user'])->findOrFail($this->dailyLogId);
        $submission = Submission::findOrFail($this->submissionId);

        // Mark as processing
        $submission->update([
            'scheduled_at' => now(),
        ]);

        try {
            $response = $submitter->submit($dailyLog);

            $submission->markAsSuccess($response);

            // Update daily log submitted_at
            $dailyLog->update(['submitted_at' => now()]);

            Log::info('Google Form submitted successfully', [
                'daily_log_id' => $this->dailyLogId,
                'submission_id' => $this->submissionId,
            ]);
        } catch (Throwable $e) {
            Log::error('Google Form submission failed', [
                'daily_log_id' => $this->dailyLogId,
                'submission_id' => $this->submissionId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Retry nếu chưa hết lần
            if ($this->attempts() < $this->tries) {
                $submission->retry();
                $this->release(5); // Release sau 5s để retry
            } else {
                $submission->markAsFailed($e->getMessage(), $this->attempts());
            }

            throw $e; // Re-throw để Laravel Queue biết job failed
        }
    }

    public function failed(Throwable $exception): void
    {
        // Log khi job failed hẳn (retry max)
        Log::critical('Google Form submission job failed permanently', [
            'daily_log_id' => $this->dailyLogId,
            'submission_id' => $this->submissionId,
            'error' => $exception->getMessage(),
        ]);

        // TODO: Gửi notification cho admin (email/Slack)
    }

    public function backoff(): array
    {
        // Exponential backoff: 5s, 25s, 125s
        return [5, 25, 125];
    }
}
```

### 3. Tạo GoogleFormSubmitter Service
**File**: `app/Infrastructure/Services/GoogleFormSubmitter.php`

```php
<?php

namespace App\Infrastructure\Services;

use App\Domain\Logwork\Models\DailyLog;
use App\Domain\Logwork\Services\SettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleFormSubmitter
{
    public function submit(DailyLog $dailyLog): array
    {
        $formUrl = SettingService::getGoogleFormUrl();

        if (!$formUrl) {
            throw new \Exception('Google Form URL chưa được cấu hình trong settings');
        }

        // Parse Google Form endpoint URL
        // Google Form thường có form: https://docs.google.com/forms/d/e/{FORM_ID}/formResponse
        $submitUrl = str_replace('/viewform', '/formResponse', $formUrl);

        // Build form data
        $formData = $this->buildFormData($dailyLog);

        // Submit POST request
        $response = Http::asForm()->post($submitUrl, $formData);

        if (!$response->successful()) {
            throw new \Exception('Google Form submit failed: ' . $response->status());
        }

        return [
            'status' => $response->status(),
            'body' => $response->body(),
            'headers' => $response->headers(),
        ];
    }

    protected function buildFormData(DailyLog $dailyLog): array
    {
        $data = [];

        // Map fields theo Google Form field IDs
        // Cần lấy entry IDs từ Google Form HTML
        // Ví dụ: entry.123456789 = summary
        $fieldMapping = config('logwork.google_form_mapping', [
            'summary' => 'entry.xxxxx',
            'tomorrow_plan' => 'entry.yyyyy',
            'tasks' => 'entry.zzzzz', // Có thể là multiple entries
            'date' => 'entry.wwwww',
        ]);

        // Date field
        $data[$fieldMapping['date']] = $dailyLog->date->format('Y-m-d');

        // Summary
        $data[$fieldMapping['summary']] = $dailyLog->summary ?? '';

        // Tomorrow plan
        $data[$fieldMapping['tomorrow_plan']] = $dailyLog->tomorrow_plan ?? '';

        // Tasks (repeatable section)
        foreach ($dailyLog->tasks as $index => $task) {
            $taskEntry = $fieldMapping['tasks'] ?? 'entry.';
            $data["{$taskEntry}_{$index}_title"] = $task->title;
            $data["{$taskEntry}_{$index}_progress"] = $task->progress_percent . '%';
            $data["{$taskEntry}_{$index}_time"] = $task->estimated_time . ' phút';
        }

        // Submit button
        $data['submit'] = 'Submit';

        return $data;
    }
}
```

### 4. Tạo Config cho Google Form Mapping
**File**: `config/logwork.php`

```php
<?php

return [
    'google_form_mapping' => [
        // Lấy entry IDs từ Google Form source HTML
        // Ví dụ: <input type="text" name="entry.123456789">
        'summary' => env('GOOGLE_FORM_ENTRY_SUMMARY', 'entry.xxxxx'),
        'tomorrow_plan' => env('GOOGLE_FORM_ENTRY_TOMORROW', 'entry.yyyyy'),
        'date' => env('GOOGLE_FORM_ENTRY_DATE', 'entry.wwwww'),
        // Tasks: dùng entry.aaaaa_0_title, entry.aaaaa_0_progress, ...
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

### 5. Tạo Failed Jobs Table Migration (nếu chưa có)
```bash
php artisan queue:failed-table
php artisan migrate
```

### 6. Tạo Command để process queue
```bash
php artisan make:command ProcessQueueCommand
```

**File**: `app/Console/Commands/ProcessQueueCommand.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessQueueCommand extends Command
{
    protected $signature = 'queue:process';
    protected $description = 'Process the queue jobs';

    public function handle(): int
    {
        $this->info('Starting queue worker...');
        $this->call('queue:work', [
            '--queue' => 'default',
            '--timeout' => 60,
            '--tries' => 3,
            '--sleep' => 3,
        ]);

        return self::SUCCESS;
    }
}
```

### 7. Supervisor Config (Production)
**File**: `supervisor/conf.d/laravel-queue.conf` (example)

```
[program:laravel-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work database --sleep=3 --tries=3 --timeout=60
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/laravel/queue.log
```

## Files cần tạo
- `app/Jobs/SubmitGoogleFormJob.php`
- `app/Infrastructure/Services/GoogleFormSubmitter.php`
- `config/logwork.php`
- `app/Console/Commands/ProcessQueueCommand.php`
- `supervisor/conf.d/laravel-queue.conf` (production)

## Kiểm tra
```bash
# Test queue connection
php artisan queue:work --once

# Test job dispatch
php artisan tinker
>>> App\Jobs\SubmitGoogleFormJob::dispatch(1, 1);
>>> php artisan queue:work --once

# Check failed jobs table
php artisan queue:failed
```

## Notes
- **Retry policy**: 3 lần, exponential backoff (5s, 25s, 125s)
- **Timeout**: 60s cho mỗi job
- **Failed job handling**: Ghi log, mark submission as failed, notify admin
- **Queue driver**: Production dùng `redis`, dev dùng `database`
- **Horizon**: Optional, để monitor queues在生产环境

---

**Status**: ⏳ Pending  
**Priority**: High  
**Dependencies**: Task 3, Task 2 (migrations)  
**Estimated time**: 20 phút
