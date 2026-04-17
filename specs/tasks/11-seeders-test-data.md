# Task 11: Seeders & Test Data

## Mục tiêu
Tạo database seeders để populate test data: admin user, global settings, sample user logs, tasks.

## Công việc cần làm

### 1. GlobalSettingsSeeder
**File**: `database/seeders/GlobalSettingsSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\GlobalSetting;
use Illuminate\Database\Seeder;

class GlobalSettingsSeeder extends Seeder
{
    public function run(): void
    {
        GlobalSetting::updateOrCreate(
            ['key' => 'google_form_url'],
            [
                'value' => env('GOOGLE_FORM_URL', 'https://docs.google.com/forms/d/e/FORM_ID/viewform'),
                'description' => 'Google Form endpoint for daily logwork submission',
            ]
        );

        GlobalSetting::updateOrCreate(
            ['key' => 'default_submit_time'],
            [
                'value' => env('DEFAULT_SUBMIT_TIME', '17:00:00'),
                'description' => 'Default time for auto-submit if user not set',
            ]
        );

        GlobalSetting::updateOrCreate(
            ['key' => 'ai_api_key'],
            [
                'value' => env('AI_API_KEY', ''),
                'description' => 'OpenAI/Anthropic API key for AI task parsing',
            ]
        );

        GlobalSetting::updateOrCreate(
            ['key' => 'allow_manual_edit'],
            [
                'value' => true,
                'description' => 'Allow users to edit logwork after submission',
            ]
        );

        GlobalSetting::updateOrCreate(
            ['key' => 'submit_retry_attempts'],
            [
                'value' => 3,
                'description' => 'Max retry attempts for Google Form submission',
            ]
        );
    }
}
```

### 2. UserSeeder (Admin + Test Users)
**File**: `database/seeders/UserSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@logwork.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $this->command->info('Admin user created: admin@logwork.test / password');

        // Test regular users (5 users)
        $users = [
            ['name' => 'Nguyễn Văn A', 'email' => 'nguyenvana@test.com'],
            ['name' => 'Trần Thị B', 'email' => 'tranthib@test.com'],
            ['name' => 'Lê Văn C', 'email' => 'levanc@test.com'],
            ['name' => 'Phạm Thị D', 'email' => 'phamthid@test.com'],
            ['name' => 'Hoàng Văn E', 'email' => 'hoangvane@test.com'],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'preferred_submit_time' => '17:00:00',
                    'auto_submit_enabled' => rand(0, 1),
                ]
            );

            // Create user settings
            UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'auto_submit_enabled' => (bool)rand(0, 1),
                    'preferred_submit_time' => rand(9, 18) . ':00:00',
                    'notify_before_submit' => (bool)rand(0, 1),
                ]
            );
        }

        $this->command->info(count($users) . ' test users created.');
    }
}
```

### 3. DailyLogFactory (nếu dùng Factory)
**File**: `database/factories/DailyLogFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'summary' => $this->faker->paragraph(2),
            'tomorrow_plan' => $this->faker->paragraph(1),
            'raw_input' => [
                'text' => $this->faker->paragraph(3),
            ],
            'is_submit_chat' => (bool)rand(0, 1),
            'submitted_at' => $this->faker->optional()->dateTimeThisMonth(),
        ];
    }
}
```

### 4. TaskFactory
**File**: `database/factories/TaskFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\DailyLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        $tasks = [
            ['title' => 'Phát triển API login', 'progress' => 100, 'time' => 120],
            ['title' => 'Fix bug đăng nhập', 'progress' => 100, 'time' => 90],
            ['title' => 'Viết unit test', 'progress' => 80, 'time' => 60],
            ['title' => 'Code review', 'progress' => 50, 'time' => 45],
            ['title' => 'Document API', 'progress' => 30, 'time' => 30],
        ];

        $task = $this->faker->randomElement($tasks);

        return [
            'daily_log_id' => DailyLog::inRandomOrder()->first()?->id ?? DailyLog::factory(),
            'title' => $task['title'],
            'progress_percent' => $task['progress'],
            'estimated_time' => $task['time'],
            'order_index' => 0, // sẽ set sau
        ];
    }
}
```

### 5. SubmissionSeeder
**File**: `database/seeders/DailyLogSeeder.php` (kết hợp tạo logs + tasks + submissions)

```php
<?php

namespace Database\Seeders;

use App\Models\DailyLog;
use App\Models\Task;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DailyLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            // Tạo 7 logs gần nhất
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::today()->subDays($i);
                
                // Check nếu đã có log cho date này
                if (DailyLog::where('user_id', $user->id)->where('date', $date)->exists()) {
                    continue;
                }

                $log = DailyLog::create([
                    'user_id' => $user->id,
                    'date' => $date,
                    'summary' => "Ngày $date: Hoàn thành các task được giao, thực hiện code review và viết unit test.",
                    'tomorrow_plan' => "Ngày mai: Triển khai tính năng mới, fix bug tồn đọng.",
                    'raw_input' => [
                        'text' => "Hôm nay làm xong API login, fix bug user logout, và refactor code base."
                    ],
                    'is_submit_chat' => (bool)rand(0, 1),
                    'submitted_at' => rand(0, 1) ? Carbon::now() : null,
                ]);

                // Tạo 3-5 tasks cho mỗi log
                $taskCount = rand(3, 5);
                for ($j = 0; $j < $taskCount; $j++) {
                    Task::create([
                        'daily_log_id' => $log->id,
                        'title' => "Task " . ($j + 1) . ": " . $this->getRandomTaskTitle(),
                        'progress_percent' => rand(70, 100),
                        'estimated_time' => rand(30, 180),
                        'order_index' => $j,
                    ]);
                }

                // Tạo submission nếu log được submit
                if ($log->is_submit_chat) {
                    Submission::create([
                        'daily_log_id' => $log->id,
                        'scheduled_at' => $log->created_at,
                        'submitted_at' => $log->submitted_at ?? Carbon::now(),
                        'status' => $log->submitted_at ? 'success' : 'pending',
                        'response_data' => ['status' => 'ok'],
                        'retry_count' => 0,
                    ]);
                }
            }
        }

        $this->command->info('Daily logs, tasks, and submissions created for all users.');
    }

    protected function getRandomTaskTitle(): string
    {
        $titles = [
            'Phát triển tính năng login/register',
            'Fix bug security issues',
            'Viết unit test cho module payment',
            'Tối ưu query database',
            'Code review PR #123',
            'Update documentation',
            'Triển khai caching Redis',
            'Setup monitoring với Grafana',
            'Refactor legacy code',
            'Implement CI/CD pipeline',
        ];

        return $this->faker->randomElement($titles);
    }
}
```

### 6. DatabaseSeeder
**File**: `database/seeders/DatabaseSeeder.php` (update)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            GlobalSettingsSeeder::class,
            DailyLogSeeder::class, // Thêm seed logs
        ]);
    }
}
```

### 7. Migration Factory (tùy chọn)
Nếu dùng model factory cũ (Laravel < 8):

**File**: `database/factories/UserFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'user',
            'preferred_submit_time' => null,
            'auto_submit_enabled' => false,
            'remember_token' => Str::random(10),
        ];
    }
}
```

### 8. Refresh Database Command (tùy chọn)
**File**: `database/seeders/RefreshTestDataSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RefreshTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ (cẩn thận!)
        \App\Models\Task::query()->delete();
        \App\Models\Submission::query()->delete();
        \App\Models\DailyLog::query()->delete();
        \App\Models\UserSetting::query()->delete();
        \App\Models\User::where('role', 'user')->delete();

        // Seed lại
        $this->call([
            UserSeeder::class,
            DailyLogSeeder::class,
        ]);

        $this->command->info('Test data refreshed successfully.');
    }
}
```

## Files cần tạo
- `database/seeders/GlobalSettingsSeeder.php`
- `database/seeders/UserSeeder.php`
- `database/factories/DailyLogFactory.php` (optional)
- `database/factories/TaskFactory.php` (optional)
- `database/seeders/DailyLogSeeder.php`
- Edit `database/seeders/DatabaseSeeder.php`
- `database/seeders/RefreshTestDataSeeder.php` (optional)

## Kiểm tra
```bash
# Fresh database + seed
php artisan migrate:fresh --seed

# Check data
php artisan tinker
>>> \App\Models\User::count()
>>> \App\Models\DailyLog::count()
>>> \App\Models\Task::count()

# Refresh only test data
php artisan db:seed --class=RefreshTestDataSeeder
```

## Notes
- **First user**: Auto admin@logwork.test password: password
- **Test passwords**: Tất cả test users dùng password: "password"
- **Factory vs Seeder**: Seeder dùng để tạo data fix, Factory dùng để bulk generate random
- **Unique constraint**: DailyLog unique (user_id, date) nên seeder cần check exists
- **Dates**: Tạo 7 logs gần nhất cho mỗi user (7 ngày gần nhất)
- **Submission**: Random 50% logs được submit

---

**Status**: ⏳ Pending  
**Priority**: Medium  
**Dependencies**: Task 2 (Migrations)  
**Estimated time**: 15 phút
