# Task 12: Testing Strategy & Implementation

## Mục tiêu
Setup testing framework, viết unit tests, feature tests, và integration tests cho hệ thống.

## Công việc cần làm

### 1. Install Testing Packages
```bash
# Pest (testing framework - optional, có thể dùng PHPUnit)
composer require pestphp/pest --dev
composer require pestphp/pest-plugin-laravel --dev

# OR dùng PHPUnit mặc định (đã có trong Laravel)
# composer require --dev phpunit/phpunit ^10.0

# Laravel Pint (code formatting)
composer require laravel/pint --dev

# Faker (đã có sẵn)

# Mockery (nếu cần)
composer require --dev mockery/mockery
```

**Setup Pest**:
```bash
# Install Pest
./vendor/bin/pest --init

# Tạo Pest bootstrap
cp .env.example .env.testing
```

### 2. Configure Testing Environment
**File**: `phpunit.xml` (update)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Application Test">
            <directory suffix="Test.php">./tests/Feature</directory>
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

### 3. Unit Tests

#### Test TaskParserService (Mock AI)
**File**: `tests/Unit/Services/TaskParserServiceTest.php`

```php
<?php

use App\Infrastructure\AI\TaskParserInterface;
use App\Infrastructure\AI\MockTaskParser;
use Tests\TestCase;

uses(MockTaskParser::class, TaskParserInterface::class)->beforeEach(function () {
    $this->parser = new MockTaskParser();
});

describe('TaskParserService', function () {
    it('can parse simple text into tasks', function () {
        $text = "Task 1\nTask 2\nTask 3";
        $result = $this->parser->parse($text);

        expect($result)->toHaveCount(3);
        expect($result[0]['title'])->toBe('Task 1');
        expect($result[0]['progress_percent'])->toBe(100);
        expect($result[0]['estimated_time'])->toBe(60);
    });

    it('handles empty input', function () {
        $result = $this->parser->parse('');
        expect($result)->toBeArray();
    });

    it('filters empty lines', function () {
        $text = "Task 1\n\n\nTask 2";
        $result = $this->parser->parse($text);
        expect($result)->toHaveCount(2);
    });
});
```

#### Test LogworkService
**File**: `tests/Unit/Services/LogworkServiceTest.php`

```php
<?php

use App\Domain\Logwork\Services\LogworkService;
use App\Domain\Logwork\Repositories\DailyLogRepositoryInterface;
use App\Domain\Logwork\Repositories\TaskRepositoryInterface;
use App\Models\User;
use Tests\TestCase;

uses(LogworkService::class)->beforeEach(function () {
    $this->mockDailyLogRepo = Mockery::mock(DailyLogRepositoryInterface::class);
    $this->mockTaskRepo = Mockery::mock(TaskRepositoryInterface::class);
    
    $this->service = new LogworkService(
        $this->mockDailyLogRepo,
        $this->mockTaskRepo
    );
});

describe('createOrUpdateDailyLog', function () {
    it('creates new daily log with tasks', function () {
        $user = User::factory()->make();
        $data = [
            'date' => '2026-04-17',
            'summary' => 'Test summary',
            'tomorrow_plan' => 'Test plan',
            'tasks' => [
                ['title' => 'Task 1', 'progress_percent' => 100, 'estimated_time' => 60],
            ],
        ];

        $this->mockDailyLogRepo->shouldReceive('findByUserAndDate')
            ->once()
            ->andReturnNull();

        $this->mockDailyLogRepo->shouldReceive('create')
            ->once()
            ->andReturn(new \App\Domain\Logwork\Models\DailyLog(['id' => 1]));

        $this->mockTaskRepo->shouldReceive('bulkCreate')
            ->once();

        $result = $this->service->createOrUpdateDailyLog($data, $user);
        
        expect($result)->not->toBeNull();
    });

    it('updates existing daily log', function () {
        $user = User::factory()->make();
        $date = new \DateTime('2026-04-17');
        
        $existingLog = new \App\Domain\Logwork\Models\DailyLog([
            'id' => 1,
            'user_id' => $user->id,
            'date' => $date,
        ]);

        $this->mockDailyLogRepo->shouldReceive('findByUserAndDate')
            ->once()
            ->andReturn($existingLog);

        $this->mockDailyLogRepo->shouldReceive('update')
            ->once()
            ->andReturn($existingLog);

        $this->mockTaskRepo->shouldReceive('bulkCreate')
            ->once();

        $result = $this->service->createOrUpdateDailyLog([
            'date' => '2026-04-17',
            'summary' => 'Updated',
            'tasks' => [],
        ], $user);

        expect($result)->not->toBeNull();
    });
});
```

#### Test SettingService
**File**: `tests/Unit/Services/SettingServiceTest.php`

```php
<?php

use App\Domain\Logwork\Services\SettingService;
use App\Models\GlobalSetting;
use App\Models\User;
use App\Models\UserSetting;
use Tests\TestCase;

uses(SettingService::class)->describe('Global Settings', function () {
    beforeEach(function () {
        GlobalSetting::truncate();
    });

    it('can set and get global setting', function () {
        SettingService::setGlobal('test_key', 'test_value', 'Test description');
        
        expect(SettingService::getGlobal('test_key'))->toBe('test_value');
    });

    it('returns default if setting not exists', function () {
        expect(SettingService::getGlobal('non_existent', 'default'))->toBe('default');
    });

    it('can get google form url', function () {
        SettingService::setGlobal('google_form_url', 'https://forms.google.com/...');
        
        expect(SettingService::getGoogleFormUrl())
            ->toBe('https://forms.google.com/...');
    });
});

uses(SettingService::class)->describe('User Settings', function () {
    it('can set user setting', function () {
        $user = User::factory()->create();
        
        SettingService::setUserSetting($user, 'auto_submit_enabled', true);
        
        expect(SettingService::getUserSetting($user, 'auto_submit_enabled'))
            ->toBeTrue();
    });

    it('creates user setting if not exists', function () {
        $user = User::factory()->create();
        
        SettingService::setUserSetting($user, 'preferred_submit_time', '09:00:00');
        
        expect($user->userSetting)->not->toBeNull();
    });
});
```

### 4. Feature Tests

#### UserCanCreateLogworkTest
**File**: `tests/Feature/Logwork/UserCanCreateLogworkTest.php`

```php
<?php

use App\Models\User;
use App\Models\DailyLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(User::class)->beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['*']);
});

describe('Daily Logwork API', function () {
    it('user can create daily logwork', function () {
        $data = [
            'date' => '2026-04-17',
            'summary' => 'Hôm nay làm nhiều việc',
            'tomorrow_plan' => 'Ngày mai sẽ tiếp tục',
            'is_submit_chat' => false,
            'tasks' => [
                [
                    'title' => 'Task 1',
                    'progress_percent' => 100,
                    'estimated_time' => 60,
                ],
                [
                    'title' => 'Task 2',
                    'progress_percent' => 80,
                    'estimated_time' => 90,
                ],
            ],
        ];

        $response = $this->postJson('/api/logworks', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Logwork created successfully',
            ]);

        $this->assertDatabaseHas('daily_logs', [
            'user_id' => $this->user->id,
            'date' => '2026-04-17',
            'summary' => 'Hôm nay làm nhiều việc',
        ]);

        $this->assertDatabaseCount('tasks', 2);
    });

    it('cannot create two logs on same day', function () {
        DailyLog::create([
            'user_id' => $this->user->id,
            'date' => '2026-04-17',
            'summary' => 'First log',
        ]);

        $data = [
            'date' => '2026-04-17',
            'summary' => 'Second log',
            'tasks' => [
                ['title' => 'Task', 'progress_percent' => 100, 'estimated_time' => 60],
            ],
        ];

        $response = $this->postJson('/api/logworks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('date');
    });

    it('requires at least 1 task', function () {
        $data = [
            'date' => '2026-04-17',
            'summary' => 'No tasks',
            'tasks' => [],
        ];

        $response = $this->postJson('/api/logworks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('tasks');
    });

    it('validates task progress between 0-100', function () {
        $data = [
            'date' => '2026-04-17',
            'summary' => 'Test',
            'tasks' => [
                ['title' => 'Task', 'progress_percent' => 150, 'estimated_time' => 60],
            ],
        ];

        $response = $this->postJson('/api/logworks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('tasks.0.progress_percent');
    });

    it('user can view own logs only', function () {
        $otherUser = User::factory()->create();
        $log = DailyLog::create([
            'user_id' => $otherUser->id,
            'date' => '2026-04-17',
            'summary' => 'Other user log',
        ]);

        $response = $this->getJson("/api/logworks/{$log->id}");

        $response->assertStatus(404); // Not found (policy denies)
    });

    it('user can update own log', function () {
        $log = DailyLog::create([
            'user_id' => $this->user->id,
            'date' => '2026-04-17',
            'summary' => 'Original summary',
        ]);

        $data = [
            'date' => '2026-04-17',
            'summary' => 'Updated summary',
            'tasks' => [
                ['title' => 'Updated task', 'progress_percent' => 100, 'estimated_time' => 60],
            ],
        ];

        $response = $this->putJson("/api/logworks/{$log->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logwork updated successfully',
            ]);

        $this->assertDatabaseHas('daily_logs', [
            'id' => $log->id,
            'summary' => 'Updated summary',
        ]);
    });

    it('user can delete own log', function () {
        $log = DailyLog::create([
            'user_id' => $this->user->id,
            'date' => '2026-04-17',
            'summary' => 'To be deleted',
        ]);

        $response = $this->deleteJson("/api/logworks/{$log->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('daily_logs', ['id' => $log->id]);
    });
});
```

#### AdminCanViewAllLogsTest
**File**: `tests/Feature/Admin/AdminCanViewAllLogsTest.php`

```php
<?php

use App\Models\User;
use App\Models\DailyLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(TestCase::class)->beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($this->admin, ['*']);
});

describe('Admin Permissions', function () {
    it('admin can view all users logs', function () {
        $otherUser = User::factory()->create();
        DailyLog::create([
            'user_id' => $otherUser->id,
            'date' => '2026-04-17',
            'summary' => 'Other user log',
        ]);

        $response = $this->getJson('/api/logworks');

        $response->assertStatus(200);
        // Should return at least 1 log
        expect($response->json('data.data'))->toHaveCount(1);
    });

    it('admin can access global settings', function () {
        $response = $this->getJson('/api/settings/global');

        $response->assertStatus(200);
    });

    it('non-admin cannot access global settings', function () {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/settings/global');

        $response->assertStatus(403);
    });
});
```

#### AutoSubmitQueueTest
**File**: `tests/Feature/Queue/AutoSubmitQueueTest.php`

```php
<?php

use App\Domain\Logwork\Models\DailyLog;
use App\Jobs\SubmitGoogleFormJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(TestCase::class)->beforeEach(function () {
    Queue::fake();
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['*']);
});

describe('Submission Queue', function () {
    it('dispatches job when submitting logwork', function () {
        $log = DailyLog::create([
            'user_id' => $this->user->id,
            'date' => '2026-04-17',
            'summary' => 'Test log',
            'is_submit_chat' => false,
        ]);

        // Add tasks
        $log->tasks()->create([
            'title' => 'Task 1',
            'progress_percent' => 100,
            'estimated_time' => 60,
            'order_index' => 0,
        ]);

        $response = $this->postJson("/api/logworks/{$log->id}/submit");

        $response->assertStatus(200);

        Queue::assertPushed(SubmitGoogleFormJob::class, function ($job) use ($log) {
            return $job->dailyLogId === $log->id;
        });
    });

    it('cannot submit logwork without tasks', function () {
        $log = DailyLog::create([
            'user_id' => $this->user->id,
            'date' => '2026-04-17',
            'summary' => 'No tasks',
        ]);

        $response = $this->postJson("/api/logworks/{$log->id}/submit");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot submit: logwork has no tasks',
            ]);

        Queue::assertNotPushed(SubmitGoogleFormJob::class);
    });
});
```

### 5. Database Testing (Refresh Trait)
**File**: `tests/Feature/Http/Controllers/LogworkControllerTest.php` (Full controller test)

```php
<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\DailyLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogworkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed initial data
        $this->seed([
            UserSeeder::class,
        ]);
    }

    /** @test */
    public function user_can_get_today_logwork(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        DailyLog::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'summary' => 'Today work',
        ]);

        $response = $this->getJson('/api/logworks/today');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.summary', 'Today work');
    }

    /** @test */
    public function user_can_list_their_logworks_with_pagination(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        DailyLog::factory()->count(20)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('/api/logworks?per_page=10');

        $response->assertOk()
            ->assertJsonPath('pagination.total', 20)
            ->assertJsonPath('pagination.per_page', 10);
    }
}
```

### 6. Run All Tests
**File**: `phpunit.xml` - configure test suite

```bash
# Run all tests
./vendor/bin/pest

# Run specific test file
./vendor/bin/pest tests/Feature/Logwork/UserCanCreateLogworkTest.php

# Run specific test
./vendor/bin/pest --filter="test_name"

# With coverage (Xdebug/PCOV)
./vendor/bin/pest --coverage

# Generate HTML coverage
./vendor/bin/pest --coverage-html coverage-report

# Watch mode (if using Pest)
./vendor/bin/pest --watch
```

### 7. Test Factories
**File**: `tests/Feature/Factories/DailyLogFactoryTest.php`

```php
<?php

use App\Models\DailyLog;
use App\Models\User;
use Tests\TestCase;

uses(DailyLog::factory())->describe('DailyLog factory', function () {
    it('can create a daily log', function () {
        $log = DailyLog::factory()->create();
        
        expect($log->id)->not->toBeNull();
        expect($log->date)->toBeInstanceOf(\Carbon\Carbon::class);
    });

    it('can create with user', function () {
        $user = User::factory()->create();
        $log = DailyLog::factory()->for($user)->create();
        
        expect($log->user_id)->toBe($user->id);
    });

    it('can create with tasks', function () {
        $log = DailyLog::factory()->hasTasks(3)->create();
        
        expect($log->tasks)->toHaveCount(3);
    });
});
```

### 8. API Testing với Laravel Sanctum
**File**: `tests/Feature/Api/ApiAuthTest.php`

```php
<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    public function test_user_can_login_and_get_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'token',
                ],
            ]);
    }

    public function test_user_can_access_protected_routes_with_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }
}
```

### 9. Browser/Feature Tests (Dusk - optional)
```bash
composer require laravel/dusk --dev
php artisan dusk:install
```

**File**: `tests/Browser/LogworkTest.php` (full browser automation)

### 10. Code Coverage & Quality
```bash
# Run Pint (code style)
./vendor/bin/pint

# Run PHPStan (static analysis)
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse

# Run Rector (refactoring)
composer require --dev rector/rector
./vendor/bin/rector process
```

## Testing Checklist

- [ ] Unit tests: Services, Repositories, Models
- [ ] Feature tests: API endpoints
- [ ] Integration tests: Full flow (create → submit)
- [ ] Database migrations test
- [ ] Validation rules test
- [ ] Authorization/Policies test
- [ ] Queue jobs test
- [ ] Google Form submission mock test
- [ ] Filament resources test (optional)

## Files cần tạo
- `tests/Unit/Services/TaskParserServiceTest.php`
- `tests/Unit/Services/LogworkServiceTest.php`
- `tests/Unit/Services/SettingServiceTest.php`
- `tests/Feature/Logwork/UserCanCreateLogworkTest.php`
- `tests/Feature/Admin/AdminCanViewAllLogsTest.php`
- `tests/Feature/Queue/AutoSubmitQueueTest.php`
- `tests/Feature/Http/Controllers/LogworkControllerTest.php`
- `tests/Feature/Factories/DailyLogFactoryTest.php`
- `tests/Feature/Api/ApiAuthTest.php`
- Edit `phpunit.xml` or create `pest.php`

## Kiểm tra
```bash
# Run tests
./vendor/bin/pest

# Check coverage
./vendor/bin/pest --coverage

# Run specific test suite
./vendor/bin/pest --testsuite=Unit
./vendor/bin/pest --testsuite=Feature
```

## Notes
- **Test database**: SQLite in-memory, `RefreshDatabase` trait
- **Mocking**: Mock jobs, Http clients, external APIs
- **Factories**: Use `User::factory()`, `DailyLog::factory()` for test data
- **Authentication**: Use `Sanctum::actingAs()` for API tests
- **Assertions**: `assertStatus()`, `assertJson()`, `assertDatabaseHas()`
- **Pest vs PHPUnit**: Pest cleaner syntax, PHPUnit built-in
- **CI/CD**: Add `php artisan test` vào GitHub Actions

---

**Status**: ⏳ Pending  
**Priority**: High  
**Dependencies**: Task 2 (Migrations), Task 6 (Controllers)  
**Estimated time**: 1-2 giờ (viết tests)
