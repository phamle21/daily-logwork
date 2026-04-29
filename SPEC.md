# Spec: Daily Logwork — Multi-User Daily Report System

## 1. Overview

Chuyển đổi Daily Report từ static PHP files (không auth, lưu history vào .log) thành web application đa người dùng:
- **Đăng nhập/đăng ký** với Laravel Fortify
- **Mỗi user** có lịch sử, config riêng
- **Projects** lưu webhook URL trong DB (không hardcode)
- **Google Form** config được per user
- **Admin** quản lý users, projects, xem tất báo cáo

---

## 2. Tech Stack (Vibe Code)

| Thành phần | Công nghệ | Trạng thái |
|------------|-----------|-----------|
| Framework | Laravel 13 + Livewire 4 | ✅ Có sẵn |
| CSS | Tailwind CSS 4 | ✅ Có sẵn |
| Auth | Laravel Fortify | ✅ Có sẵn (chưa config) |
| Alerts | SweetAlert2 (swal) | ❌ Cần install |
| Date picker | flatpickr | ❌ Cần install |
| Icons | Heroicons (via Laravel) | ✅ Có sẵn |
| DB | MySQL 8 | ✅ Docker |
| Cache | Redis 7 | ✅ Docker |

**Tại sao KHÔNG chọn:**
- ~~Filament~~ — Overkill cho app này
- ~~Inertia.js + Vue/React~~ — Learn curve thêm, Livewire đã đủ
- ~~Custom auth~~ — Fortify giải quyết login/register/forgot-password/password-reset trong vài lệnh

---

## 3. Users & Roles

| Role | Permissions |
|------|-------------|
| **User** | Báo cáo, xem lịch sử riêng, config cá nhân (default project, toggle Google Form) |
| **Admin** | Tất cả của User + quản lý users, projects, xem/tất báo cáo |

---

## 4. Database Schema

### users (Fortify tự tạo)
```
id, name, email, password, email_verified_at, two_factor_secret,
two_factor_recovery_codes, two_factor_confirmed_at,
remember_token, current_team_id, profile_photo_path, created_at, updated_at
```
**Thêm column:**
```php
$table->enum('role', ['user', 'admin'])->default('user');
```

### projects (MỚI)
```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();        // JRR, Primas, Project A...
    $table->string('webhook_url')->nullable(); // Google Chat webhook URL
    $table->string('avatar_url')->nullable();  // Logo URL
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### daily_reports (CẬP NHẬT)
```php
Schema::create('daily_reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('project_id')->constrained()->onDelete('restrict');
    $table->date('report_date');
    $table->integer('quality_rating')->default(3);    // 1-5
    $table->integer('spirit_rating')->default(3);     // 1-5
    $table->text('notes')->nullable();
    $table->boolean('submitted_to_chat')->default(false);
    $table->boolean('submitted_to_form')->default(false);
    $table->timestamp('submitted_at')->nullable();
    $table->timestamps();
    
    // Unique: mỗi user chỉ 1 report/ngày/mỗi project
    $table->unique(['user_id', 'project_id', 'report_date']);
});
```

### report_tasks (CẬP NHẬT)
```php
Schema::create('report_tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('daily_report_id')->constrained()->onDelete('cascade');
    $table->enum('task_type', ['today', 'tomorrow']);
    $table->text('description');
    $table->integer('progress')->default(0);          // 0-100
    $table->date('expected_date')->nullable();
    $table->integer('order')->default(0);
    $table->timestamps();
});
```

### user_project_preferences (MỚI)
```php
Schema::create('user_project_preferences', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('project_id')->constrained()->onDelete('restrict');
    $table->boolean('google_form_enabled')->default(true);
    $table->json('google_form_fields')->nullable();   // map fields
    $table->timestamps();
    
    $table->unique(['user_id', 'project_id']);
});
```

### team & team_user (Fortify tự tạo)
```
Team support cho multi-tenant (nếu cần group users theo team)
```

---

## 5. Features Detail

### 5.1 Authentication (Fortify)
```bash
php artisan fortify:install
php artisan make:auth    # tạo views login/register
```
- Login / Register
- Password Reset
- Email Verification (optional)
- Two-Factor Authentication (optional)
- Profile: name, email, password, photo

### 5.2 Report Form (Livewire)

**Sections:**
1. **Project Selector** — Dropdown từ projects table
2. **Today Tasks** — Repeater (add/remove/drag reorder)
   - Description, Progress (0-100%), Expected Date (conditional)
3. **Tomorrow Tasks** — Repeater (add/remove)
   - Description only
4. **Quality Rating** — 5 buttons (1-5)
   - 1: ❌ Kém, 2: ⚠️ Trung bình, 3: ✅ Khá, 4: 🌟 Tốt, 5: 🏆 Xuất sắc
5. **Spirit Rating** — 5 emoji buttons
   - 1: 😵‍💫, 2: 🤒, 3: 😊, 4: 😃, 5: 🔥
6. **Notes** — Textarea optional
7. **Google Form Toggle** — Switch ON/OFF
8. **Submit Button** — Loading state + SweetAlert2 notification

**Submit Flow:**
```
Validate → Save to DB → Send Webhook → Send Google Form (if enabled) → SweetAlert success → Redirect to history
```

### 5.3 History

**Personal History (User):**
- Paginated table: date, project, quality, spirit, actions
- Filter: date range, project
- Actions: view detail, edit (same day), delete
- View detail page with full report

**Admin History:**
- All users' reports
- Filter: user, project, date range
- Export CSV (optional)

### 5.4 User Profile
- Edit: name, email, password
- Default project preference
- Google Form toggle per project
- Activity stats (reports this month, etc.)

### 5.5 Admin Panel

**Manage Users:**
- List all users
- Assign role (user/admin)
- Create/edit/delete users
- Activity log

**Manage Projects:**
- CRUD projects
- Set webhook URL per project
- Set avatar URL per project
- Toggle active/inactive
- Seed default projects (JRR, Primas, A-H)

**System Settings:**
- Global config (if any)
- Google Form global defaults

### 5.6 Webhook Integration

**Từ DB config (projects table):**
```php
$project = Project::find($projectId);
$webhookUrl = $project->webhook_url;

// Build cardV2 payload (giống code cũ)
// Send via cURL
```

**Payload format giữ nguyên** như send-webhook.php hiện tại.

### 5.7 Google Form Integration

**Config per user per project:**
```php
$pref = UserProjectPreference::firstOrCreate([
    'user_id' => $userId,
    'project_id' => $projectId,
]);

$googleFormEnabled = $pref->google_form_enabled;
$googleFormFields = $pref->google_form_fields;
```

**Fields mapping (JSON):**
```json
{
  "email": "entry.1568765630",
  "project": "entry.603488309",
  "date_year": "entry.2095511431_year",
  "date_month": "entry.2095511431_month",
  "date_day": "entry.2095511431_day",
  "task_today": "entry.xxx",
  "task_tomorrow": "entry.xxx",
  "quality": "entry.xxx",
  "spirit": "entry.xxx"
}
```

**Form URL:** Mỗi project có thể có form URL riêng (lưu trong project table hoặc user_project_preferences).

---

## 6. Routes

| Route | Method | Component/Controller | Middleware |
|-------|--------|---------------------|------------|
| `/login` | GET/POST | Fortify | guest |
| `/register` | GET/POST | Fortify | guest |
| `/forgot-password` | GET/POST | Fortify | guest |
| `/reset-password` | GET/POST | Fortify | guest |
| `/` | GET | Livewire\Report\Form | auth |
| `/report` | GET/POST | Livewire\Report\Form | auth |
| `/report/history` | GET | Livewire\Report\History | auth |
| `/report/{id}` | GET | Livewire\Report\View | auth |
| `/report/{id}/edit` | GET/POST | Livewire\Report\Edit | auth |
| `/profile` | GET/POST | Livewire\Profile\Edit | auth |
| `/admin` | GET | Livewire\Admin\Dashboard | auth,can:admin |
| `/admin/users` | GET/POST | Livewire\Admin\UserManagement | auth,can:admin |
| `/admin/projects` | GET/POST | Livewire\Admin\ProjectManagement | auth,can:admin |
| `/admin/reports` | GET | Livewire\Admin\ReportOverview | auth,can:admin |

---

## 7. Directory Structure

```
src/
├── app/
│   ├── Http/Livewire/
│   │   ├── Report/
│   │   │   ├── Form.php          # Main report form
│   │   │   ├── History.php       # Personal history
│   │   │   ├── View.php          # Report detail
│   │   │   └── Edit.php          # Edit report
│   │   ├── Profile/
│   │   │   └── Edit.php          # User profile
│   │   └── Admin/
│   │       ├── Dashboard.php
│   │       ├── UserManagement.php
│   │       ├── ProjectManagement.php
│   │       └── ReportOverview.php
│   ├── Models/
│   │   ├── User.php              # Add role, relationships
│   │   ├── Project.php           # NEW
│   │   ├── DailyReport.php       # Update relationships
│   │   ├── ReportTask.php        # Keep
│   │   └── UserProjectPreference.php  # NEW
│   └── Policies/
│       ├── DailyReportPolicy.php
│       └── AdminPolicy.php
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   ├── report/
│   │   │   │   ├── form.blade.php
│   │   │   │   ├── history.blade.php
│   │   │   │   ├── view.blade.php
│   │   │   │   └── edit.blade.php
│   │   │   ├── profile/
│   │   │   │   └── edit.blade.php
│   │   │   └── admin/
│   │   │       ├── dashboard.blade.php
│   │   │       ├── user-management.blade.php
│   │   │       ├── project-management.blade.php
│   │   │       └── report-overview.blade.php
│   │   └── components/
│   │       ├── card.blade.php
│   │       ├── task-row-today.blade.php
│   │       ├── task-row-tomorrow.blade.php
│   │       ├── rating-buttons.blade.php
│   │       └── project-selector.blade.php
│   └── css/app.css
├── database/
│   ├── migrations/
│   │   └── 0000_00_00_add_fields_to_tables.php
│   └── seeders/
│       └── ProjectSeeder.php     # 10 default projects
└── routes/
    └── web.php
```

---

## 8. Implementation Phases

### Phase 1: Foundation (BE)
- [ ] Config Fortify (`php artisan fortify:install`)
- [ ] Create users migration (add role column)
- [ ] Create projects table + seeder (10 projects)
- [ ] Update daily_reports migration (add user_id, project_id FK)
- [ ] Create report_tasks migration (same as current)
- [ ] Create user_project_preferences migration
- [ ] Update models with relationships
- [ ] Create policies

### Phase 2: Report Form (BE+FE)
- [ ] Livewire\Report\Form component
- [ ] Blade view: form.blade.php
- [ ] Task repeater (add/remove/update)
- [ ] Quality/Spirit rating buttons
- [ ] Google Form toggle
- [ ] Submit handler: save to DB
- [ ] Webhook send (from DB config)
- [ ] Google Form send (from config)
- [ ] SweetAlert2 notifications

### Phase 3: History & Profile
- [ ] Livewire\Report\History — personal history table
- [ ] Livewire\Report\View — report detail
- [ ] Livewire\Report\Edit — edit report (same day)
- [ ] Livewire\Profile\Edit — user profile
- [ ] Project selector in profile

### Phase 4: Admin Panel
- [ ] Livewire\Admin\Dashboard
- [ ] User management (CRUD + role)
- [ ] Project management (CRUD + webhook URL)
- [ ] Report overview (all users)

### Phase 5: Polish
- [ ] Drag-and-drop task reorder (SortableJS)
- [ ] Export CSV (history)
- [ ] Dashboard stats (reports this month, etc.)
- [ ] Date range filter on history
- [ ] Responsive improvements

---

## 9. Key Integration Points

### Webhook (Google Chat)
```php
// Lấy từ Project model
$project = Project::find($projectId);
$url = $project->webhook_url;

// Payload giữ nguyên từ send-webhook.php
$cardV2 = [...];

// Send cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cardV2));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
```

### Google Form
```php
// Lấy từ UserProjectPreference
$pref = UserProjectPreference::firstOrCreate([
    'user_id' => auth()->id(),
    'project_id' => $projectId,
]);

if ($pref->google_form_enabled) {
    $fields = $pref->google_form_fields; // JSON decode
    // Map report data to form entry IDs
    // Send via cURL POST
}
```

### Report Save
```php
$report = DailyReport::updateOrCreate(
    [
        'user_id' => auth()->id(),
        'project_id' => $projectId,
        'report_date' => $date,
    ],
    [
        'quality_rating' => $quality,
        'spirit_rating' => $spirit,
        'notes' => $notes,
    ]
);

// Save tasks
$report->tasks()->delete();
foreach ($tasks as $i => $task) {
    $report->tasks()->create([
        'task_type' => $type,
        'description' => $task['description'],
        'progress' => $task['progress'],
        'expected_date' => $task['expected_date'],
        'order' => $i,
    ]);
}
```

---

## 10. Security

- CSRF protection (Laravel built-in)
- Password hashing (bcrypt via Fortify)
- Authorization policies (UserPolicy, DailyReportPolicy)
- SQL injection prevention (Eloquent ORM)
- XSS prevention (Blade auto-escaping {{ }})
- Rate limiting on login (Fortify built-in)
- Mass assignment protection ($fillable in models)
- Webhook URLs validated (URL validation before store)

---

## 11. Config Defaults

### Seed projects (ProjectSeeder)
```php
$projects = [
    ['name' => 'JRR', 'sort_order' => 1, 'is_active' => true],
    ['name' => 'Primas', 'sort_order' => 2, 'is_active' => true],
    ['name' => 'Project A', 'sort_order' => 10, 'is_active' => true],
    // ... đến H
];
```

### Default values
- Quality rating: 3 (Khá)
- Spirit rating: 3 (😊)
- Google Form: enabled (true)
- Default project: first active project

---

## 12. Success Criteria

- ✅ Users can login and submit reports
- ✅ Each user sees only their own history
- ✅ Webhook configurable per project (admin)
- ✅ Google Form configurable per user/project
- ✅ Admin can manage users and projects
- ✅ All reports persisted in database
- ✅ Responsive on mobile/tablet/desktop
- ✅ No hardcode webhooks or form fields
