# HỆ THỐNG LOGWORK - ĐẶC TẢ KỸ THUẬT

## 1. Tổng quan

Hệ thống quản lý và báo cáo công việc hàng ngày với:

- **Backend**: Laravel 10+
- **Admin Panel**: Filament PHP
- **Frontend**: Tailwind CSS + Alpine.js (hoặc Livewire)
- **Database**: MySQL/PostgreSQL
- **Queue**: Laravel Queue (database/redis)
- **Authentication**: Laravel Breeze/Jetstream/Filament Shield

---

## 2. Mục tiêu

1. User đăng nhập, cấu hình preference cá nhân
2. Mỗi ngày tạo logwork report (text → AI parse → tasks)
3. Lưu report, có option submit Google Form
4. Theo dõi lịch sử submission
5. Admin quản lý global settings

---

## 3. Cấu trúc thư mục

```
app/
├── Domain/
│   ├── Logwork/
│   │   ├── Models/
│   │   │   ├── DailyLog.php
│   │   │   ├── Task.php
│   │   │   └── Submission.php
│   │   ├── Repositories/
│   │   │   ├── DailyLogRepository.php
│   │   │   └── TaskRepository.php
│   │   └── Services/
│   │       ├── LogworkService.php
│   │       ├── TaskParserService.php
│   │       └── SubmissionService.php
├── Application/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── LogworkController.php
│   │   │   ├── SettingsController.php
│   │   │   └── Api/
│   │   └── Middleware/
│   │       └── EnsureRole.php
│   └── Http/
│       └── Requests/
│           ├── Logwork/
│           │   ├── StoreRequest.php
│           │   └── UpdateRequest.php
│           └── Settings/
│               ├── GlobalSettingsRequest.php
│               └── UserSettingsRequest.php
├── Infrastructure/
│   ├── Persistence/
│   │   └── Eloquent/
│   │       ├── DailyLog.php
│   │       ├── Task.php
│   │       └── Submission.php
│   ├── Google/
│   │   └── GoogleFormSubmitter.php
│   └── AI/
│       └── TaskParserInterface.php
├── Filament/
│   ├── Pages/
│   │   ├── Admin/
│   │   │   ├── GlobalSettings.php
│   │   │   └── SubmissionLogs.php
│   │   └── Dashboard.php
│   ├── Resources/
│   │   ├── DailyLogResource.php
│   │   ├── TaskResource.php
│   │   └── SubmissionResource.php
│   └── Widgets/
│       └── TodayLogStatus.php
└── Providers/
    └── AppServiceProvider.php

database/
├── migrations/
│   ├── 2024_01_01_000000_create_users_table.php (extend)
│   ├── 2024_01_01_000001_create_daily_logs_table.php
│   ├── 2024_01_01_000002_create_tasks_table.php
│   ├── 2024_01_01_000003_create_submissions_table.php
│   ├── 2024_01_01_000004_create_global_settings_table.php
│   └── 2024_01_01_000005_create_user_settings_table.php
├── seeders/
│   ├── DatabaseSeeder.php
│   └── GlobalSettingsSeeder.php
└── factories/ (nếu cần)

resources/
├── views/
│   ├── logwork/
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── index.blade.php
│   └── filament/
│       └── pages/
│           └── admin/
│               └── global-settings.blade.php
├── css/
│   └── app.css (Tailwind)
├── js/
│   └── app.js (Alpine)
└── vite.config.js
```

---

## 4. Database Schema

### 4.1 Bảng `users` (mở rộng từ Laravel)

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | |
| name | string | |
| email | string | unique |
| password | string | |
| role | enum('user','admin') | default: 'user' |
| preferred_submit_time | time | null |
| auto_submit_enabled | boolean | default: false |
| created_at | timestamp | |
| updated_at | timestamp | |

### 4.2 Bảng `daily_logs`

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | |
| user_id | foreignId → users.id | onDelete:cascade |
| date | date | unique:user_id (1 report/ngày) |
| summary | text | nullable |
| tomorrow_plan | text | nullable |
| raw_input | json | Input gốc từ textarea |
| is_submit_chat | boolean | Có submit Google Form không |
| submitted_at | timestamp | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes**: `unique(user_id, date)`

### 4.3 Bảng `tasks`

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | |
| daily_log_id | foreignId → daily_logs.id | onDelete:cascade |
| title | string | |
| progress_percent | tinyInteger | 0-100 |
| estimated_time | integer | (phút) |
| order_index | integer | Sắp xếp |
| created_at | timestamp | |
| updated_at | timestamp | |

### 4.4 Bảng `submissions` (lịch sử submit)

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | |
| daily_log_id | foreignId → daily_logs.id | onDelete:cascade |
| scheduled_at | timestamp | Thời gian dự kiến submit |
| submitted_at | timestamp | Thực tế submit |
| status | enum('pending','success','failed') | |
| response_data | json | Response từ Google Form |
| error_message | text | nullable |
| retry_count | integer | default: 0 |
| created_at | timestamp | |

### 4.5 Bảng `global_settings`

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | |
| key | string | unique |
| value | json | Có thể là string/array/object |
| description | string | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Dữ liệu mẫu**:
- `google_form_url`: string
- `default_submit_time`: "17:00:00"
- `ai_api_key`: string (nullable)
- `allow_manual_edit`: boolean (true)

### 4.6 Bảng `user_settings` (có thể merge vào users)

| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | |
| user_id | foreignId → users.id | unique, onDelete:cascade |
| auto_submit_enabled | boolean | default: false |
| preferred_submit_time | time | nullable |
| notify_before_submit | boolean | default: false |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 5. API Endpoints (RESTful)

### 5.1 Auth
- `POST /login` - Đăng nhập
- `POST /logout` - Đăng xuất
- `POST /register` - Đăng ký (nếu mở)

### 5.2 Daily Logs
- `GET /api/logworks` - Danh sách logworks (filter by date)
- `POST /api/logworks` - Tạo logwork mới
- `GET /api/logworks/{id}` - Chi tiết logwork
- `PUT /api/logworks/{id}` - Cập nhật
- `DELETE /api/logworks/{id}` - Xóa
- `POST /api/logworks/{id}/submit` - Trigger submit Google Form

### 5.3 Tasks (concurrent với logwork)
- `GET /api/logworks/{logwork}/tasks` - Danh sách tasks
- `POST /api/tasks` - Thêm task
- `PUT /api/tasks/{id}` - Cập nhật task
- `DELETE /api/tasks/{id}` - Xóa task

### 5.4 Settings
- `GET /api/settings/global` - Global settings (admin only)
- `PUT /api/settings/global` - Update global settings
- `GET /api/settings/user` - User settings
- `PUT /api/settings/user` - Update user settings

### 5.5 Submissions
- `GET /api/submissions` - Lịch sử submissions
- `GET /api/submissions/{id}` - Chi tiết submission

---

## 6. Luồng nghiệp vụ (Flow)

```
User đăng nhập
    ↓
Xem dashboard (logwork hôm nay đã tạo chưa)
    ↓
[Chưa có] → Tạo logwork mới
    ↓
Nhập raw text vào textarea (VD: "Hôm nay làm API login, fix bug đăng nhập")
    ↓
(Optional) AI parse → Tạo tasks tự động
    ↓
User chỉnh sửa tasks (CRUD tasks trong repeater):
    - Title
    - Progress % (0-100)
    - Estimated time (phút)
    ↓
Nhập Summary (tổng kết ngày)
    ↓
Nhập Tomorrow Plan (kế hoạch ngày mai)
    ↓
Toggle "Submit to Google Form" (is_submit_chat)
    ↓
Submit → Lưu DailyLog + Tasks
    ↓
Nếu is_submit_chat = true:
    Tạo Submission record (status: pending)
    Dispatch Job → SubmitGoogleFormJob
        ↓
    Job chạy (scheduled_at hoặc immediate)
        ↓
    Gọi Google Form API (curl/http)
        ↓
    Update Submission (success/failed)
        ↓
    Nếu failed, retry (max 3 lần)
```

---

## 7. Google Form Integration

### 7.1 Form structure
- Form ID lưu trong `global_settings`
- Cần map fields:
  - Summary → text area
  - Tasks → repeatable section (title, progress, estimated_time)
  - Tomorrow plan → text area
  - Date → hidden field

### 7.2 Submit method
- POST request đến Google Forms endpoint
- Form data payload (application/x-www-form-urlencoded)
- Dùng `Http::asForm()->post($formUrl, $data)`

### 7.3 Retry policy
- Max 3 retries
- Exponential backoff: 1s, 5s, 30s
- Sau 3 lần → status = 'failed', notify admin (optional)

---

## 8. Filament Admin Panel

### 8.1 Resources
- **DailyLogResource** - Quản lý logworks (view-only cho admin, full cho user)
- **TaskResource** - Quản lý tasks (có thể ẩn, task quản lý trong DailyLog)
- **SubmissionResource** - Xem lịch sử submit
- **GlobalSettingResource** - Settings toàn hệ thống

### 8.2 Pages (Custom)
- **Admin/GlobalSettings** - Form config Google Form URL, thời gian default
- **Admin/Dashboard** - Widgets: số logworks hôm nay, submission status
- **User/MyLogwork** - Page để user tạo/sửa logwork hôm nay

### 8.3 Roles & Permissions (Filament Shield)
- `admin`: full access
- `user`: access to own logworks only

---

## 9. Queue System

### 9.1 Jobs
```php
class SubmitGoogleFormJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $submissionId,
        public int $dailyLogId
    ) {}

    public function handle(): void
    {
        // Gọi GoogleFormService
        // Update submission record
    }

    public function failed(\Throwable $exception): void
    {
        // Log lỗi, notify admin
    }
}
```

### 9.2 Configure queue
- `.env`: `QUEUE_CONNECTION=database` (đơn giản)
- Horizon nếu cần monitor

---

## 10. Frontend (Tailwind CSS)

### 10.1 Layouts
- `resources/views/layouts/app.blade.php` - Layout chính với navbar
- `resources/views/filament/layouts/app.blade.php` - Override Filament layout (nếu custom)

### 10.2 Components
- `resources/views/components/logwork-form.blade.php` - Form tạo logwork
- `resources/views/components/task-repeater.blade.php` - Repeater inputs tasks
- `resources/views/components/textarea-parser.blade.php` - Textarea + AI button

### 10.3 Pages
- `resources/views/logwork/create.blade.php` - Tạo logwork
- `resources/views/logwork/edit.blade.php` - Sửa logwork
- `resources/views/logwork/index.blade.php` - Danh sách

### 10.4 JavaScript
- Alpine.js cho dynamic task repeater (thêm/xóa task inline)
- Optional: Livewire nếu muốn realtime mà không viết JS

---

## 11. Services

### 11.1 LogworkService
```php
class LogworkService
{
    public function createOrUpdate(array $data, User $user): DailyLog
    {
        // Validate: chỉ 1 logwork/ngày/user
        // Create/update DailyLog + Tasks
    }

    public function getTodayLog(User $user): ?DailyLog
    {
        // Return logwork của hôm nay
    }
}
```

### 11.2 TaskParserService (AI - tạm thời)
```php
class TaskParserService implements TaskParserInterface
{
    public function parse(string $rawText): array
    {
        // Tạm thời: return mock data
        // Sau: gọi OpenAI API để extract tasks
        return [
            ['title' => 'Task 1', 'progress_percent' => 100, 'estimated_time' => 60],
        ];
    }
}
```

### 11.3 GoogleFormSubmi

tter
```php
class GoogleFormSubmitter
{
    public function submit(DailyLog $log): array
    {
        // Map data từ DailyLog → Google Form fields
        // POST request
        // Return response
    }
}
```

---

## 12. Repository Pattern

```php
interface DailyLogRepositoryInterface
{
    public function findById(int $id): ?DailyLog;
    public function findByUserAndDate(User $user, string $date): ?DailyLog;
    public function create(array $data): DailyLog;
    public function update(DailyLog $log, array $data): DailyLog;
    public function delete(DailyLog $log): bool;
}
```

---

## 13. Validation Rules

### StoreDailyLogRequest
```php
return [
    'date' => 'required|date|unique:daily_logs,date,NULL,id,user_id,' . auth()->id(),
    'summary' => 'nullable|string|max:1000',
    'tomorrow_plan' => 'nullable|string|max:1000',
    'is_submit_chat' => 'boolean',
    'tasks' => 'required|array|min:1',
    'tasks.*.title' => 'required|string|max:255',
    'tasks.*.progress_percent' => 'required|integer|min:0|max:100',
    'tasks.*.estimated_time' => 'required|integer|min:1',
];
```

---

## 14. Security

- Laravel Sanctum/auth cho API
- Policies: user chỉ edit logwork của mình
- Admin: xem tất cả
- Sanitize input (Purifier hoặc `strip_tags`)
- Rate limit: 5 requests/phút cho API (nếu cần)

---

## 15. Testing

### Unit Tests
- `TaskParserServiceTest` (mock AI response)
- `LogworkServiceTest` (create/update logic)
- `GoogleFormSubmitterTest` (mock HTTP)

### Feature Tests
- `UserCanCreateLogworkTest`
- `UserCanSubmitToGoogleFormTest`
- `AdminCanViewAllLogsTest`
- `AutoSubmitQueueTest`

### PHPUnit + Pest

---

## 16. Deployment & Env

`.env.example`:
```env
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=logwork
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
GOOGLE_FORM_URL= # Google Form URL (endpoint submit)
AI_API_KEY= # Optional

FILAMENT_SHIELD_ENABLED=true
```

---

## 17. Phát triển sau này (TODO)

- [ ] AI parsing với GPT-4o-mini (free tier) hoặc Anthropic
- [ ] Excel/PDF export
- [ ] Dashboard analytics (biểu đồ task completion)
- [ ] Reminder notification (email/browser) trước submit time
- [ ] Multiple report templates (Slack, email)
- [ ] Team/group reporting
- [ ] Mobile responsive (PWA)
- [ ] Calendar integration (Google Calendar sync)

---

## 18. Giới hạn hiện tại

- **AI tạm thời disabled**: chưa tìm được model free ổn định
- **Google Form**: submit qua form URL (có limit submissions/ngày)
- **Queue**: dùng database driver (production nên dùng Redis)
- **Authentication**: chưa có OAuth (Google/GitHub login)

---

## 19. Checklist triển khai

- [ ] Setup Laravel project + dependencies
- [ ] Cấu hình Filament + Shield
- [ ] Tạo migrations (6 bảng)
- [ ] Tạo Models + Relationships
- [ ] Tạo Repositories + Services
- [ ] Build Authentication (Breeze/Jetstream)
- [ ] Tạo LogworkController + validation
- [ ] Build Task repeater UI (Tailwind + Alpine)
- [ ] Implement GoogleFormSubmi

tter service
- [ ] Setup Queue + Job
- [ ] Filament Resources + Pages
- [ ] User dashboard page
- [ ] Global settings page (admin)
- [ ] Testing
- [ ] Deploy (shared hosting/VPS)

---

*Người tạo: Kilo - Software Engineer*  
*Ngày: 17/04/2026*  
*Phiên bản: 1.0*
