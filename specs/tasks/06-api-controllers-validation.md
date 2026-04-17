# Task 6: API Controllers & Request Validation

## Mục tiêu
Xây dựng RESTful API Controllers với validation rules đầy đủ cho tất cả operations.

## Công việc cần làm

### 1. Tạo DailyLogController
**File**: `app/Http/Controllers/Api/LogworkController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Logwork\Services\LogworkService;
use App\Domain\Logwork\Models\DailyLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Logwork\StoreDailyLogRequest;
use App\Http\Requests\Logwork\UpdateDailyLogRequest;

class LogworkController extends Controller
{
    public function __construct(
        protected LogworkService $logworkService
    ) {}

    /**
     * Lấy danh sách logworks của user hiện tại
     * GET /api/logworks
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        $logs = $this->logworkService->getUserLogs($user, $perPage);

        return response()->json([
            'success' => true,
            'data' => $logs,
            'pagination' => [
                'total' => $logs->total(),
                'per_page' => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
            ],
        ]);
    }

    /**
     * Lấy logwork hôm nay
     * GET /api/logworks/today
     */
    public function today(Request $request): JsonResponse
    {
        $user = $request->user();
        $log = $this->logworkService->getTodayLog($user);

        return response()->json([
            'success' => true,
            'data' => $log,
        ]);
    }

    /**
     * Tạo logwork mới
     * POST /api/logworks
     */
    public function store(StoreDailyLogRequest $request, LogworkService $logworkService): JsonResponse
    {
        $validated = $request->validated();
        $log = $this->logworkService->createOrUpdateDailyLog($validated, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logwork created successfully',
            'data' => $log->load(['tasks']),
        ], 201);
    }

    /**
     * Lấy chi tiết logwork
     * GET /api/logworks/{id}
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $user = $request->user();
        $log = DailyLog::with(['tasks', 'submissions'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $log,
        ]);
    }

    /**
     * Cập nhật logwork
     * PUT /api/logworks/{id}
     */
    public function update(UpdateDailyLogRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // Authorization check
        $log = DailyLog::where('user_id', $user->id)->findOrFail($id);

        $updatedLog = $this->logworkService->createOrUpdateDailyLog($validated, $user);

        return response()->json([
            'success' => true,
            'message' => 'Logwork updated successfully',
            'data' => $updatedLog->load(['tasks']),
        ]);
    }

    /**
     * Xóa logwork
     * DELETE /api/logworks/{id}
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $user = $request->user();
        $log = DailyLog::where('user_id', $user->id)->findOrFail($id);

        $this->logworkService->deleteDailyLog($log, $user);

        return response()->json([
            'success' => true,
            'message' => 'Logwork deleted successfully',
        ]);
    }

    /**
     * Trigger submit Google Form
     * POST /api/logworks/{id}/submit
     */
    public function submit(int $id, Request $request): JsonResponse
    {
        $user = $request->user();
        $log = DailyLog::where('user_id', $user->id)->findOrFail($id);

        // Validate đã có tasks
        if ($log->tasks->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot submit: logwork has no tasks',
            ], 422);
        }

        // Create submission record
        $submission = $log->submissions()->create([
            'scheduled_at' => now(),
            'status' => 'pending',
        ]);

        // Dispatch job
        \App\Jobs\SubmitGoogleFormJob::dispatch($log->id, $submission->id);

        return response()->json([
            'success' => true,
            'message' => 'Submission queued successfully',
            'data' => ['submission_id' => $submission->id],
        ]);
    }

    /**
     * Parse AI từ raw text (tạm thời mock)
     * POST /api/logworks/parse
     */
    public function parse(Request $request): JsonResponse
    {
        $request->validate([
            'raw_text' => 'required|string',
        ]);

        $parser = app(\App\Infrastructure\AI\TaskParserInterface::class);
        $tasks = $parser->parse($request->input('raw_text'));

        return response()->json([
            'success' => true,
            'data' => [
                'tasks' => $tasks,
                'count' => count($tasks),
            ],
        ]);
    }
}
```

### 2. Tạo Settings Controller
**File**: `app/Http/Controllers/Api/SettingsController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Logwork\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Settings\UpdateUserSettingsRequest;
use App\Http\Requests\Settings\UpdateGlobalSettingsRequest;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    /**
     * Lấy global settings (admin only)
     * GET /api/settings/global
     */
    public function global(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isAdmin) {
            abort(403, 'Admin only');
        }

        $settings = \App\Models\GlobalSetting::all()->keyBy('key');

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Update global settings
     * PUT /api/settings/global
     */
    public function updateGlobal(UpdateGlobalSettingsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        foreach ($validated as $key => $value) {
            $this->settingService::setGlobal($key, $value);
        }

        return response()->json([
            'success' => true,
            'message' => 'Global settings updated',
        ]);
    }

    /**
     * Lấy user settings
     * GET /api/settings/user
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $settings = $user->userSetting;

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Update user settings
     * PUT /api/settings/user
     */
    public function updateUser(UpdateUserSettingsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $this->settingService::setUserSetting($user, 'auto_submit_enabled', $validated['auto_submit_enabled']);
        $this->settingService::setUserSetting($user, 'preferred_submit_time', $validated['preferred_submit_time']);
        $this->settingService::setUserSetting($user, 'notify_before_submit', $validated['notify_before_submit']);

        return response()->json([
            'success' => true,
            'message' => 'User settings updated',
            'data' => $user->userSetting,
        ]);
    }
}
```

### 3. Tạo Submission Controller
**File**: `app/Http/Controllers/Api/SubmissionController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Logwork\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubmissionController extends Controller
{
    /**
     * Lấy lịch sử submissions của user
     * GET /api/submissions
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $submissions = Submission::with(['dailyLog'])
            ->whereHas('dailyLog', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $submissions,
            'pagination' => [
                'total' => $submissions->total(),
                'per_page' => $submissions->perPage(),
                'current_page' => $submissions->currentPage(),
            ],
        ]);
    }

    /**
     * Lấy chi tiết submission
     * GET /api/submissions/{id}
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $user = $request->user();

        $submission = Submission::with(['dailyLog.tasks'])
            ->whereHas('dailyLog', fn($q) => $q->where('user_id', $user->id))
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $submission,
        ]);
    }
}
```

### 4. Tạo Request Classes

#### StoreDailyLogRequest
**File**: `app/Http/Requests/Logwork/StoreDailyLogRequest.php`

```php
<?php

namespace App\Http\Requests\Logwork;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth middleware đã check ở route
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'date' => 'required|date|unique:daily_logs,date,NULL,id,user_id,' . $userId,
            'summary' => 'nullable|string|max:1000',
            'tomorrow_plan' => 'nullable|string|max:1000',
            'raw_input' => 'nullable|array',
            'raw_input.text' => 'nullable|string',
            'is_submit_chat' => 'boolean',
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.progress_percent' => 'required|integer|min:0|max:100',
            'tasks.*.estimated_time' => 'required|integer|min:1|max:1440', // max 24h
        ];
    }

    public function messages(): array
    {
        return [
            'date.unique' => 'Bạn đã có logwork cho ngày này rồi.',
            'tasks.required' => 'Phải có ít nhất 1 task.',
            'tasks.*.title.required' => 'Tiêu đề task không được để trống.',
            'tasks.*.progress_percent.min' => 'Tiến độ phải từ 0-100%.',
            'tasks.*.estimated_time.min' => 'Thời gian dự kiến tối thiểu 1 phút.',
        ];
    }
}
```

#### UpdateDailyLogRequest (giống Store)
**File**: `app/Http/Requests/Logwork/UpdateDailyLogRequest.php`

```php
<?php

namespace App\Http\Requests\Logwork;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Logwork\Models\DailyLog;

class UpdateDailyLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'summary' => 'nullable|string|max:1000',
            'tomorrow_plan' => 'nullable|string|max:1000',
            'raw_input' => 'nullable|array',
            'is_submit_chat' => 'boolean',
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.progress_percent' => 'required|integer|min:0|max:100',
            'tasks.*.estimated_time' => 'required|integer|min:1|max:1440',
        ];

        // Chỉ check unique date nếu đổi date
        if ($this->has('date')) {
            $dailyLog = DailyLog::find($this->route('id'));
            $userId = auth()->id();

            $rules['date'] = 'required|date|unique:daily_logs,date,' . $dailyLog->id . ',id,user_id,' . $userId;
        }

        return $rules;
    }
}
```

#### UpdateUserSettingsRequest
**File**: `app/Http/Requests/Settings/UpdateUserSettingsRequest.php`

```php
<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'auto_submit_enabled' => 'boolean',
            'preferred_submit_time' => 'nullable|date_format:H:i:s',
            'notify_before_submit' => 'boolean',
        ];
    }
}
```

#### UpdateGlobalSettingsRequest
**File**: `app/Http/Requests/Settings/UpdateGlobalSettingsRequest.php`

```php
<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGlobalSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin ?? false;
    }

    public function rules(): array
    {
        return [
            'google_form_url' => 'nullable|url',
            'default_submit_time' => 'nullable|date_format:H:i:s',
            'ai_api_key' => 'nullable|string|max:255',
            'allow_manual_edit' => 'boolean',
            'submit_retry_attempts' => 'nullable|integer|min:1|max:10',
        ];
    }
}
```

## Files cần tạo
- `app/Http/Controllers/Api/LogworkController.php`
- `app/Http/Controllers/Api/SettingsController.php`
- `app/Http/Controllers/Api/SubmissionController.php`
- `app/Http/Requests/Logwork/StoreDailyLogRequest.php`
- `app/Http/Requests/Logwork/UpdateDailyLogRequest.php`
- `app/Http/Requests/Settings/UpdateUserSettingsRequest.php`
- `app/Http/Requests/Settings/UpdateGlobalSettingsRequest.php`

## Kiểm tra
```bash
# Test validation
php artisan tinker
>>> \App\Http\Requests\Logwork\StoreDailyLogRequest::class

# Check routes
php artisan route:list --path=api
```

## Notes
- **Authorization**: Controller-level middleware `auth:sanctum` hoặc `auth`
- **Validation**: Custom messages bằng tiếng Việt
- **JSON response**: Format consistent `{success, message, data}`
- **Pagination**: Sử dụng Laravel paginator
- **Error handling**: Return 422 với validation errors

---

**Status**: ⏳ Pending  
**Priority**: High  
**Dependencies**: Task 4 (Services), Task 3 (Models)  
**Estimated time**: 30 phút
