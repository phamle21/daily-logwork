# Task 3: Models & Relationships

## Mục tiêu
Tạo tất cả Models với relationships đầy đủ giữa các bảng.

## Công việc cần làm

### 1. DailyLog Model
**File**: `app/Models/DailyLog.php`

Relationships:
```php
class DailyLog extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'summary',
        'tomorrow_plan',
        'raw_input',
        'is_submit_chat',
        'submitted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'raw_input' => 'array', // JSON cast
        'is_submit_chat' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('order_index');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}
```

### 2. Task Model
**File**: `app/Models/Task.php`

```php
class Task extends Model
{
    protected $fillable = [
        'daily_log_id',
        'title',
        'progress_percent',
        'estimated_time',
        'order_index',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'estimated_time' => 'integer',
        'order_index' => 'integer',
    ];

    // Relationships
    public function dailyLog(): BelongsTo
    {
        return $this->belongsTo(DailyLog::class);
    }

    // Accessors
    public function getEstimatedHoursAttribute(): float
    {
        return round($this->estimated_time / 60, 2);
    }

    public function getProgressBadgeAttribute(): string
    {
        if ($this->progress_percent === 100) return 'success';
        if ($this->progress_percent >= 70) return 'warning';
        if ($this->progress_percent >= 30) return 'info';
        return 'danger';
    }
}
```

### 3. Submission Model
**File**: `app/Models/Submission.php`

```php
class Submission extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'daily_log_id',
        'scheduled_at',
        'submitted_at',
        'status',
        'response_data',
        'error_message',
        'retry_count',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'submitted_at' => 'datetime',
        'response_data' => 'array',
    ];

    // Relationships
    public function dailyLog(): BelongsTo
    {
        return $this->belongsTo(DailyLog::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function markAsSuccess(array $response = null): bool
    {
        return $this->update([
            'status' => self::STATUS_SUCCESS,
            'submitted_at' => now(),
            'response_data' => $response,
        ]);
    }

    public function markAsFailed(string $error, int $retryCount): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $error,
            'retry_count' => $retryCount,
        ]);
    }

    public function retry(): void
    {
        $this->increment('retry_count');
        $this->update([
            'status' => self::STATUS_PENDING,
            'submitted_at' => null,
        ]);
    }
}
```

### 4. GlobalSetting Model
**File**: `app/Models/GlobalSetting.php`

```php
class GlobalSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    protected $casts = [
        'value' => 'array', // JSON cast
    ];

    // Constants cho các key setting
    const KEY_GOOGLE_FORM_URL = 'google_form_url';
    const KEY_DEFAULT_SUBMIT_TIME = 'default_submit_time';
    const KEY_AI_API_KEY = 'ai_api_key';
    const KEY_ALLOW_MANUAL_EDIT = 'allow_manual_edit';
    const KEY_SUBMIT_RETRY_ATTEMPTS = 'submit_retry_attempts';

    // Methods
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, string $description = null): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
            ]
        );
    }

    public static function getGoogleFormUrl(): ?string
    {
        return self::get(self::KEY_GOOGLE_FORM_URL);
    }

    public static function getDefaultSubmitTime(): string
    {
        return self::get(self::KEY_DEFAULT_SUBMIT_TIME, '17:00:00');
    }
}
```

### 5. UserSetting Model
**File**: `app/Models/UserSetting.php`

```php
class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'auto_submit_enabled',
        'preferred_submit_time',
        'notify_before_submit',
    ];

    protected $casts = [
        'auto_submit_enabled' => 'boolean',
        'notify_before_submit' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public static function forUser(User $user): self
    {
        return $user->userSetting ?: $user->userSetting()->create([
            'auto_submit_enabled' => false,
            'notify_before_submit' => false,
        ]);
    }
}
```

### 6. Update User Model
**File**: `app/Models/User.php`

Add relationships:
```php
class User extends Authenticatable
{
    // ... existing code ...

    // Relationships
    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class)->orderBy('date', 'desc');
    }

    public function userSetting(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class)->through('daily_logs');
    }

    // Accessors
    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    // Scopes
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeUsers($query)
    {
        return $query->where('role', 'user');
    }
}
```

## Files cần tạo
- ✅ `app/Models/DailyLog.php` (edit)
- ✅ `app/Models/Task.php` (create)
- ✅ `app/Models/Submission.php` (create)
- ✅ `app/Models/GlobalSetting.php` (create)
- ✅ `app/Models/UserSetting.php` (create)
- ✅ `app/Models/User.php` (edit - add relationships)

## Kiểm tra
```bash
# Tinker test relationships
php artisan tinker
>>> App\Models\User::first()->dailyLogs
>>> App\Models\DailyLog::first()->tasks
>>> App\Models\User::find(1)->userSetting
```

## Notes
- **Casts**: JSON cho `raw_input`, `response_data`; boolean cho flags; datetime cho timestamps
- **Unique constraint**: `daily_logs(user_id, date)` đảm bảo 1 logwork/ngày
- **Cascade delete**: Tất cả child records sẽ xóa theo parent
- **Soft deletes**: Có thể thêm `SoftDeletes` nếu cần recover data

---

**Status**: ⏳ Pending  
**Priority**: Highest  
**Dependencies**: Task 2  
**Estimated time**: 15 phút
