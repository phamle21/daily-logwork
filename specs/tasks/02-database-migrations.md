# Task 2: Database Migrations

## Mục tiêu
Tạo toàn bộ migrations cho hệ thống Logwork: extend users table, daily_logs, tasks, submissions, global_settings, user_settings.

## Công việc cần làm

### 1. Extend Users Table
```bash
php artisan make:migration extend_users_table_for_logwork --table=users
```

**File**: `database/migrations/xxxx_xx_xx_xxxxxx_extend_users_table_for_logwork.php`

Columns thêm vào:
```php
$table->enum('role', ['user', 'admin'])->default('user')->after('email');
$table->time('preferred_submit_time')->nullable()->after('role');
$table->boolean('auto_submit_enabled')->default(false)->after('preferred_submit_time');
```

### 2. Create Daily Logs Table
```bash
php artisan make:migration create_daily_logs_table
```

Schema:
```php
Schema::create('daily_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->date('date');
    $table->text('summary')->nullable();
    $table->text('tomorrow_plan')->nullable();
    $table->json('raw_input')->nullable(); // Input gốc từ textarea
    $table->boolean('is_submit_chat')->default(false);
    $table->timestamp('submitted_at')->nullable();
    $table->timestamps();

    $table->unique(['user_id', 'date']); // 1 logwork/ngày/user
});

$table->index(['user_id', 'date']);
```

### 3. Create Tasks Table
```bash
php artisan make:migration create_tasks_table
```

Schema:
```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('daily_log_id')->constrained()->onDelete('cascade');
    $table->string('title', 255);
    $table->tinyInteger('progress_percent')->default(0); // 0-100
    $table->integer('estimated_time')->default(60); // phút
    $table->integer('order_index')->default(0);
    $table->timestamps();
});

$table->index(['daily_log_id', 'order_index']);
```

### 4. Create Submissions Table
```bash
php artisan make:migration create_submissions_table
```

Schema:
```php
Schema::create('submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('daily_log_id')->constrained()->onDelete('cascade');
    $table->timestamp('scheduled_at')->nullable();
    $table->timestamp('submitted_at')->nullable();
    $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
    $table->json('response_data')->nullable();
    $table->text('error_message')->nullable();
    $table->integer('retry_count')->default(0);
    $table->timestamps();
});

$table->index(['daily_log_id', 'status']);
$table->index('scheduled_at');
```

### 5. Create Global Settings Table
```bash
php artisan make:migration create_global_settings_table
```

Schema:
```php
Schema::create('global_settings', function (Blueprint $table) {
    $table->id();
    $table->string('key', 100)->unique();
    $table->json('value')->nullable();
    $table->string('description')->nullable();
    $table->timestamps();
});

$table->unique('key');
```

### 6. Create User Settings Table (nếu không merge vào users)
```bash
php artisan make:migration create_user_settings_table
```

Schema:
```php
Schema::create('user_settings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
    $table->boolean('auto_submit_enabled')->default(false);
    $table->time('preferred_submit_time')->nullable();
    $table->boolean('notify_before_submit')->default(false);
    $table->timestamps();
});
```

### 7. Run All Migrations
```bash
php artisan migrate
```

## Files cần tạo

- `database/migrations/xxxx_xx_xx_xxxxxx_extend_users_table_for_logwork.php`
- `database/migrations/xxxx_xx_xx_xxxxxx_create_daily_logs_table.php`
- `database/migrations/xxxx_xx_xx_xxxxxx_create_tasks_table.php`
- `database/migrations/xxxx_xx_xx_xxxxxx_create_submissions_table.php`
- `database/migrations/xxxx_xx_xx_xxxxxx_create_global_settings_table.php`
- `database/migrations/xxxx_xx_xx_xxxxxx_create_user_settings_table.php`

## Kiểm tra
```bash
# Check migrations đã tạo
php artisan migrate:status

# Rollback nếu lỗi
php artisan migrate:rollback

# Fresh migrate (xóa hết + tạo lại)
php artisan migrate:fresh
```

## Notes
- **Timestamps**: Tất cả bảng dùng `$table->timestamps()`
- **Foreign keys**: Tất cả `onDelete('cascade')`
- **Indexes**: Index đầy đủ cho query performance
- **Ordering**: Tasks có `order_index` để sắp xếp

---

**Status**: ⏳ Pending  
**Priority**: Highest  
**Dependencies**: Task 1  
**Estimated time**: 20 phút
