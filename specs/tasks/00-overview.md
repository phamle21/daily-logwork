# Tasks Overview - Hệ thống Logwork

## 📋 Danh sách 17 tasks đã phân rã

Sau đây là toàn bộ danh sách tasks cần thực hiện để xây dựng hệ thống Logwork từ A-Z.

---

## 🚀 **PHASE 1: FOUNDATION (Tasks 1-3)**

### Task 1: Project Setup & Dependencies
**File**: `01-project-setup.md`
- Tạo Laravel 10+ project
- Cài Filament panel (v3.3)
- Cài Tailwind CSS + Alpine.js
- Publish & setup Filament
- Cài Queue tables migration
- Cài Sanctum (API auth)

**Output**: Project khởi tạo xong, Filament chạy được tại `/admin`

---

### Task 2: Database Migrations
**File**: `02-database-migrations.md`
- Extend `users` table (role, settings)
- Create `daily_logs` table
- Create `tasks` table
- Create `submissions` table
- Create `global_settings` table
- Create `user_settings` table
- Run migrations

**Output**: 6 bảng CSDL, relationships đã define

---

### Task 3: Models & Relationships
**File**: `03-models-relationships.md`
- DailyLog model (hasMany tasks, belongsTo user)
- Task model (belongsTo dailyLog)
- Submission model (belongsTo dailyLog)
- GlobalSetting model
- UserSetting model
- Update User model với relationships

**Output**: Models hoàn chỉnh, casts, scopes, accessors

---

## ⚙️ **PHASE 2: CORE LOGIC (Tasks 4-5)**

### Task 4: Repository & Service Layer
**File**: `04-repository-service-layer.md`
- DailyLogRepositoryInterface + EloquentDailyLogRepository
- TaskRepositoryInterface + EloquentTaskRepository
- LogworkService (business logic create/update/delete)
- SettingService (global & user settings)
- MockTaskParser (AI interface)
- Bindings trong AppServiceProvider

**Output**: Service layer với DDD pattern, dễ test

---

### Task 5: Queue System & Jobs
**File**: `05-queue-system.md`
- Configure QUEUE_CONNECTION=database
- Create SubmitGoogleFormJob (tries=3, timeout=60s)
- Create GoogleFormSubmitter service
- Config logwork.php
- Failed jobs table
- Retry với exponential backoff
- Failed handling + notification

**Output**: Async submission system với retry

---

## 🌐 **PHASE 3: API LAYER (Tasks 6-7)**

### Task 6: API Controllers & Validation
**File**: `06-api-controllers-validation.md`
- LogworkController (CRUD + submit + parse)
- SettingsController (global + user)
- SubmissionController (history)
- Request classes: StoreDailyLogRequest, UpdateDailyLogRequest
- Request classes: UpdateUserSettingsRequest, UpdateGlobalSettingsRequest
- Custom validation messages tiếng Việt

**Output**: RESTful API đầy đủ, validated

---

### Task 7: Routes & Middleware
**File**: `07-routes-middleware.md`
- API routes (`routes/api.php`) -全部 endpoints
- Web routes (`routes/web.php`) - Frontend pages
- Create EnsureRole middleware
- Register middleware trong Kernel
- Rate limiting (throttle)
- CORS config (nếu cần)
- ApiResponse trait (optional)

**Output**: Routes hoàn chỉnh, middleware đã apply

---

## 🎨 **PHASE 4: FRONTEND (Tasks 8-9)**

### Task 8: Filament Admin Panel
**File**: `08-filament-admin-panel.md`
- DailyLogResource (CRUD, filters, custom query)
- SubmissionResource (view-only)
- GlobalSettingResource (CRUD)
- Custom Page: Admin\GlobalSettingsPage (form config)
- Widget: TodaySubmissionStatus (stats)
- (Optional) Filament Shield setup

**Output**: Admin panel đầy đủ tại `/admin`

---

### Task 9: Frontend Pages (Tailwind + Alpine)
**File**: `09-frontend-pages.md`
- Tailwind config + PostCSS
- Main layout `layouts/app.blade.php`
- Dashboard page với stats cards
- Create/Edit logwork page với:
  - Raw input textarea
  - AI parse button (mock)
  - Task repeater (Alpine dynamic)
  - Summary & tomorrow plan
  - Submit toggle switch
- History list page với filter
- Settings page
- Alpine components cho task management

**Output**: User interface hoàn chỉnh, responsive

---

## 🔗 **PHASE 5: INTEGRATION (Task 10)**

### Task 10: Google Form Integration
**File**: `10-google-form-integration.md`
- GoogleFormSubmitter service (buildFormData)
- Config mapping trong `config/logwork.php`
- Get entry IDs từ Google Form source
- Test với form mẫu
- TestSubmission command
- SubmissionFailed notification
- Retry logic trong Job

**Output**: Google Form submission hoạt động

---

## 🌱 **PHASE 6: DATA & TESTING (Tasks 11-12)**

### Task 11: Seeders & Test Data
**File**: `11-seeders-test-data.md`
- GlobalSettingsSeeder
- UserSeeder (admin + 5 test users)
- DailyLogSeeder (7 logs mỗi user, tasks + submissions)
- DailyLogFactory + TaskFactory
- DatabaseSeeder update
- RefreshTestDataSeeder (optional)

**Output**: Test data ready, `php artisan migrate:fresh --seed` work

---

### Task 12: Testing Strategy
**File**: `12-testing.md`
- Install Pest/PHPUnit
- Unit tests: Services, Models
- Feature tests: CRUD operations
- Integration tests: Submit flow
- Factory tests
- API auth tests
- Pest config + coverage
- PHPStan/Larastan

**Output**: Test suite with >80% coverage

---

## 🚀 **PHASE 7: DEPLOYMENT (.Tasks 13-14)**

### Task 13: Deployment & Production
**File`: `13-deployment-production.md`
- `.env.production` config
- Redis setup (queue + cache)
- Laravel Horizon (monitoring)
- Nginx config (SSL + optimize)
- Supervisor config (queue worker + scheduler)
- Laravel Scheduler (auto-submit command)
- Backup script (database + files)
- Monitoring + logging
- Health check endpoint
- CI/CD pipeline (GitHub Actions)
- Security hardening

**Output**: Production-ready, automated deployment

---

### Task 14: Additional Features & Polish
**File**: `14-additional-features-polish.md`
- Real-time notifications (Reverb)
- Email notifications
- Browser push notifications
- Export Excel (Maatwebsite)
- Dashboard analytics (Charts)
- Dark mode toggle
- Mobile enhancements
- PWA offline mode
- Keyboard shortcuts
- Activity log (Spatie Activitylog)
- File attachments (optional)
- i18n (Vietnamese/English)
- Swagger API docs
- Bulk actions
- Custom error pages

**Output**: UX polished, enterprise-ready

---

## ✅ **PHASE 8: QA & RELEASE (Tasks 15-17)**

### Task 15: Performance Optimization
- Database indexing
- Eager loading tất cả relationships
- Cache global settings
- Optimize assets (Vite)
- Image compression
- CDN integration (future)

---

### Task 16: Testing & QA Checklist
**File**: `16-testing-qa-checklist.md`
- Manual testing (all flows)
- Cross-browser testing
- Lighthouse audit
- Security audit (PHPStan, security-checker)
- Accessibility testing (WCAG)
- Mobile responsive
- API testing với Postman
- Database integrity
- Load testing (k6)
- UAT với real users
- Regression testing
- Bug fixing
- Code review
- Documentation
- Staging dry-run

---

### Task 17: Go-Live Checklist
**File**: `17-go-live-checklist.md` (cần tạo)
- [ ] All tests pass
- [ ] Staging deployment successful
- [ ] Production env configured
- [ ] SSL certificate active
- [ ] Backup tested
- [ ] Monitoring active (Horizon, logs)
- [ ] Queue workers running
- [ ] Scheduler running
- [ ] Health check OK
- [ ] Admin credentials secure
- [ ] Team trained
- [ ] Rollback plan ready

---

## 📊 **Dependencies Map**

```
Task 1 → Task 2 → Task 3 → Task 4 → Task 6
         ↓          ↓          ↓          ↓
Task 5 ←───┘          ↓          ↓
         ↓     Task 7 ←───┘          ↓
Task 8 ←─────────────┘     Task 9 ←───┘
         ↓                          ↓
Task 10 ←──── Task 11 ←─── Task 12 ←───┐
         ↓                          ↓    ↓
Task 13 ←────────────────────── Task 14 ←─┘
         ↓
Task 15 → Task 16 → Task 17
```

---

## 🎯 **Execution Order Recommended**

1. **Week 1**: Tasks 1-4 (Setup + Core logic)
2. **Week 2**: Tasks 5-7 (Queue + API)
3. **Week 3**: Tasks 8-9 (Admin + Frontend)
4. **Week 4**: Tasks 10-11 (Integration + Seeder)
5. **Week 5**: Task 12 (Testing)
6. **Week 6**: Task 13 (Deployment)
7. **Week 7**: Task 14-16 (Polish + QA)
8. **Week 8**: Task 17 (Go-live)

---

## 📦 **Packages Summary**

| Package | Purpose | Task |
|---------|---------|------|
| `laravel/framework` | Core | 1 |
| `filament/filament` | Admin panel | 1, 8 |
| `tailwindcss` | CSS | 1, 9 |
| `alpinejs` | JS interactions | 1, 9 |
| `laravel/sanctum` | API auth | 1, 6 |
| `laravel/horizon` | Queue monitor | 5, 13 |
| `laravel/reverb` | Realtime | 14 |
| `maatwebsite/excel` | Export | 14 |
| `spatie/laravel-activitylog` | Audit | 14 |
| `pestphp/pest` | Testing | 12 |

---

## 💡 **Quick Start Commands**

```bash
# Clone + install
git clone <repo> && cd daily-logwork
composer install
npm install

# Setup
cp .env.example .env
php artisan key:generate

# Migrate + seed
php artisan migrate:fresh --seed

# Dev server
php artisan serve
npm run dev  # Vite

# Login admin
admin@logwork.test / password

# Run tests
php artisan test

# Queue worker
php artisan queue:work

# Horizon
php artisan horizon
```

---

**Total Tasks**: 17 files  
**Total Estimated Time**: 6-8 tuần (full-time)  
**Created by**: Kilo Software Engineer  
**Date**: 17/04/2026
