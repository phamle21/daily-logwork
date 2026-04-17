# Task 4: Repository & Service Layer

## Mục tiêu
Implement Repository Pattern cho data access và Service Layer cho business logic, theo DDD architecture đã định.

## Công việc cần làm

### 1. Tạo Repository Interface
**File**: `app/Domain/Logwork/Repositories/DailyLogRepositoryInterface.php`

```php
<?php

namespace App\Domain\Logwork\Repositories;

use App\Domain\Logwork\Models\DailyLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DailyLogRepositoryInterface
{
    public function findById(int $id): ?DailyLog;
    public function findByUserAndDate(User $user, \DateTimeInterface $date): ?DailyLog;
    public function findByUser(User $user, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): DailyLog;
    public function update(DailyLog $dailyLog, array $data): DailyLog;
    public function delete(DailyLog $dailyLog): bool;
    public function existsForDate(User $user, \DateTimeInterface $date): bool;
    public function getLatestByUser(User $user, int $limit = 7): Collection;
}
```

### 2. Tạo Repository Implementation
**File**: `app/Infrastructure/Persistence/Eloquent/DailyLogRepository.php`

```php
<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Logwork\Models\DailyLog;
use App\Domain\Logwork\Repositories\DailyLogRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DailyLogRepository implements DailyLogRepositoryInterface
{
    public function findById(int $id): ?DailyLog
    {
        return DailyLog::with(['tasks', 'user'])->find($id);
    }

    public function findByUserAndDate(User $user, \DateTimeInterface $date): ?DailyLog
    {
        return DailyLog::with(['tasks'])
            ->where('user_id', $user->id)
            ->where('date', $date->format('Y-m-d'))
            ->first();
    }

    public function findByUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return DailyLog::with(['tasks', 'submissions'])
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): DailyLog
    {
        return DailyLog::create($data);
    }

    public function update(DailyLog $dailyLog, array $data): DailyLog
    {
        $dailyLog->update($data);
        return $dailyLog->fresh(['tasks']);
    }

    public function delete(DailyLog $dailyLog): bool
    {
        return $dailyLog->delete();
    }

    public function existsForDate(User $user, \DateTimeInterface $date): bool
    {
        return DailyLog::where('user_id', $user->id)
            ->where('date', $date->format('Y-m-d'))
            ->exists();
    }

    public function getLatestByUser(User $user, int $limit = 7): Collection
    {
        return DailyLog::with(['tasks'])
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }
}
```

### 3. Tạo Task Repository (nếu cần)
**File**: `app/Domain/Logwork/Repositories/TaskRepositoryInterface.php`

```php
<?php

namespace App\Domain\Logwork\Repositories;

use App\Domain\Logwork\Models\Task;

interface TaskRepositoryInterface
{
    public function findById(int $id): ?Task;
    public function create(array $data): Task;
    public function update(Task $task, array $data): Task;
    public function delete(Task $task): bool;
    public function bulkCreate(array $tasks, int $dailyLogId): void;
    public function reorder(Task $task, int $newOrder): void;
}
```

**File**: `app/Infrastructure/Persistence/Eloquent/TaskRepository.php`

```php
<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Logwork\Models\Task;
use App\Domain\Logwork\Repositories\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{
    public function findById(int $id): ?Task
    {
        return Task::find($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh();
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function bulkCreate(array $tasks, int $dailyLogId): void
    {
        foreach ($tasks as $index => $taskData) {
            $tasks[$index]['daily_log_id'] = $dailyLogId;
            $tasks[$index]['order_index'] = $index;
        }
        Task::insert($tasks);
    }

    public function reorder(Task $task, int $newOrder): void
    {
        $task->update(['order_index' => $newOrder]);
    }
}
```

### 4. Tạo Logwork Service
**File**: `app/Domain/Logwork/Services/LogworkService.php`

```php
<?php

namespace App\Domain\Logwork\Services;

use App\Domain\Logwork\Repositories\DailyLogRepositoryInterface;
use App\Domain\Logwork\Repositories\TaskRepositoryInterface;
use App\Domain\Logwork\Models\DailyLog;
use App\Models\User;
use Illuminate\Support\Carbon;

class LogworkService
{
    public function __construct(
        protected DailyLogRepositoryInterface $dailyLogRepository,
        protected TaskRepositoryInterface $taskRepository
    ) {}

    public function createOrUpdateDailyLog(array $data, User $user): DailyLog
    {
        $date = $data['date'] ?: today()->format('Y-m-d');

        // Check if exists
        $existing = $this->dailyLogRepository->findByUserAndDate($user, new \DateTime($date));

        if ($existing) {
            // Update
            $dailyLog = $this->dailyLogRepository->update($existing, [
                'summary' => $data['summary'],
                'tomorrow_plan' => $data['tomorrow_plan'],
                'raw_input' => $data['raw_input'] ?? null,
                'is_submit_chat' => $data['is_submit_chat'] ?? false,
            ]);

            // Update tasks
            $this->syncTasks($existing, $data['tasks'] ?? []);
        } else {
            // Create new
            $dailyLog = $this->dailyLogRepository->create([
                'user_id' => $user->id,
                'date' => $date,
                'summary' => $data['summary'],
                'tomorrow_plan' => $data['tomorrow_plan'],
                'raw_input' => $data['raw_input'] ?? null,
                'is_submit_chat' => $data['is_submit_chat'] ?? false,
            ]);

            // Create tasks
            if (!empty($data['tasks'])) {
                $this->taskRepository->bulkCreate($data['tasks'], $dailyLog->id);
            }
        }

        return $dailyLog->fresh(['tasks']);
    }

    public function getTodayLog(User $user): ?DailyLog
    {
        return $this->dailyLogRepository
            ->findByUserAndDate($user, today());
    }

    public function getUserLogs(User $user, int $perPage = 15)
    {
        return $this->dailyLogRepository->findByUser($user, $perPage);
    }

    public function deleteDailyLog(DailyLog $dailyLog, User $user): bool
    {
        // Authorization: user can only delete own log
        if ($dailyLog->user_id !== $user->id && !$user->isAdmin) {
            abort(403, 'Unauthorized');
        }

        return $this->dailyLogRepository->delete($dailyLog);
    }

    protected function syncTasks(DailyLog $dailyLog, array $tasks): void
    {
        // Xóa tasks cũ
        $dailyLog->tasks()->delete();

        // Tạo tasks mới
        foreach ($tasks as $index => $taskData) {
            $dailyLog->tasks()->create([
                'title' => $taskData['title'],
                'progress_percent' => $taskData['progress_percent'],
                'estimated_time' => $taskData['estimated_time'],
                'order_index' => $index,
            ]);
        }
    }

    public function calculateTotalTime(DailyLog $dailyLog): int
    {
        return $dailyLog->tasks->sum('estimated_time');
    }

    public function calculateAverageProgress(DailyLog $dailyLog): float
    {
        if ($dailyLog->tasks->isEmpty()) {
            return 0;
        }
        return round($dailyLog->tasks->avg('progress_percent'), 2);
    }
}
```

### 5. Tạo Setting Service
**File**: `app/Domain/Logwork/Services/SettingService.php`

```php
<?php

namespace App\Domain\Logwork\Services;

use App\Domain\Logwork\Models\GlobalSetting;
use App\Models\User;
use App\Models\UserSetting;

class SettingService
{
    // Global Settings
    public static function getGlobal(string $key, $default = null)
    {
        return GlobalSetting::get($key, $default);
    }

    public static function setGlobal(string $key, $value, string $description = null): void
    {
        GlobalSetting::set($key, $value, $description);
    }

    public static function getGoogleFormUrl(): ?string
    {
        return self::getGlobal(GlobalSetting::KEY_GOOGLE_FORM_URL);
    }

    public static function getDefaultSubmitTime(): string
    {
        return self::getGlobal(GlobalSetting::KEY_DEFAULT_SUBMIT_TIME, '17:00:00');
    }

    public static function getRetryAttempts(): int
    {
        return self::getGlobal(GlobalSetting::KEY_SUBMIT_RETRY_ATTEMPTS, 3);
    }

    // User Settings
    public static function getUserSetting(User $user, string $key, $default = null)
    {
        $settings = UserSetting::forUser($user);
        return $settings->$key ?? $default;
    }

    public static function setUserSetting(User $user, string $key, $value): void
    {
        $settings = UserSetting::forUser($user);
        $settings->update([$key => $value]);
    }

    public static function isAutoSubmitEnabled(User $user): bool
    {
        return self::getUserSetting($user, 'auto_submit_enabled', false);
    }

    public static function getPreferredSubmitTime(User $user): ?string
    {
        return self::getUserSetting($user, 'preferred_submit_time');
    }
}
```

### 6. Tạo AI Parser Interface & Mock Implementation
**File**: `app/Infrastructure/AI/TaskParserInterface.php`

```php
<?php

namespace App\Infrastructure\AI;

interface TaskParserInterface
{
    /**
     * Parse raw text into array of tasks
     *
     * @param string $rawText
     * @return array Array of tasks: [{title, progress_percent, estimated_time}]
     */
    public function parse(string $rawText): array;
}
```

**File**: `app/Infrastructure/AI/MockTaskParser.php`

```php
<?php

namespace App\Infrastructure\AI;

class MockTaskParser implements TaskParserInterface
{
    public function parse(string $rawText): array
    {
        // Tạm thời parse đơn giản theo dòng
        $lines = array_filter(array_map('trim', explode("\n", $rawText)));

        $tasks = [];
        foreach ($lines as $index => $line) {
            if (!empty($line)) {
                $tasks[] = [
                    'title' => $line,
                    'progress_percent' => 100,
                    'estimated_time' => 60, // default 1h
                ];
            }
        }

        return $tasks;
    }
}
```

### 7. Bind Interfaces vào AppServiceProvider
**File**: `app/Providers/AppServiceProvider.php`

```php
public function register(): void
{
    // Repository bindings
    $this->app->bind(
        \App\Domain\Logwork\Repositories\DailyLogRepositoryInterface::class,
        \App\Infrastructure\Persistence\Eloquent\DailyLogRepository::class
    );

    $this->app->bind(
        \App\Domain\Logwork\Repositories\TaskRepositoryInterface::class,
        \App\Infrastructure\Persistence\Eloquent\TaskRepository::class
    );

    // AI Parser binding (mock)
    $this->app->bind(
        \App\Infrastructure\AI\TaskParserInterface::class,
        \App\Infrastructure\AI\MockTaskParser::class
    );
}
```

## Files cần tạo
- `app/Domain/Logwork/Repositories/DailyLogRepositoryInterface.php`
- `app/Infrastructure/Persistence/Eloquent/DailyLogRepository.php`
- `app/Domain/Logwork/Repositories/TaskRepositoryInterface.php`
- `app/Infrastructure/Persistence/Eloquent/TaskRepository.php`
- `app/Domain/Logwork/Services/LogworkService.php`
- `app/Domain/Logwork/Services/SettingService.php`
- `app/Infrastructure/AI/TaskParserInterface.php`
- `app/Infrastructure/AI/MockTaskParser.php`
- Edit `app/Providers/AppServiceProvider.php`

## Kiểm tra
```bash
# Tinker test service container
php artisan tinker
>>> app(\App\Domain\Logwork\Repositories\DailyLogRepositoryInterface::class)
>>> app(\App\Infrastructure\AI\TaskParserInterface::class)->parse("Task 1\nTask 2");
```

## Notes
- **Interface segregation**: Tách riêng interfaces cho dễ test và swap implementation
- **Dependency injection**: Laravel service container tự động resolve
- **Service layer**: Tất cả business logic nằm trong Services
- **Repository pattern**: Abstract data access, có thể swap Eloquent/Query Builder

---

**Status**: ⏳ Pending  
**Priority**: Highest  
**Dependencies**: Task 3  
**Estimated time**: 25 phút
