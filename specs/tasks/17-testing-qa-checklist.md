# Task 16: Testing & QA Checklist

## Mục tiêu
Đảm bảo quality trước release: testing, bug fix, security audit, performance optimization.

## Công việc cần làm

### 1. Manual Testing Checklist

#### User Authentication
- [ ] Register (if enabled)
- [ ] Login với email/password
- [ ] Logout
- [ ] Password reset (nếu có)
- [ ] Session timeout
- [ ] Remember me

#### User Dashboard
- [ ] Xem dashboard stats
- [ ] Navigation links work
- [ ] Profile dropdown
- [ ] Responsive trên mobile/tablet

#### Create Logwork
- [ ] Form hiển thị đúng
- [ ] Validation: required fields
- [ ] Parse AI button (mock): tạo tasks từ raw input
- [ ] Add/remove task dynamically
- [ ] Update task progress/estimated time
- [ ] Toggle submit switch
- [ ] Submit thành công → redirect
- [ ] Submit lỗi → show error message
- [ ] Không thể tạo 2 logs cùng ngày
- [ ] Date picker đúng format

#### Edit Logwork
- [ ] Load existing data
- [ ] Update thành công
- [ ] Delete logwork
- [ ] Authorization: user chỉ edit log của mình

#### History/Listing
- [ ] Pagination hoạt động
- [ ] Filter by date range
- [ ] Search by content (nếu có)
- [ ] Sort by date/tasks
- [ ] View details

#### Settings
- [ ] Update user settings
- [ ] Toggle auto-submit
- [ ] Set preferred time
- [ ] Save persistence

#### Submission
- [ ] Check submission status in history
- [ ] Queue job được dispatch
- [ ] Job retry nếu fail
- [ ] Submission record update đúng status

#### Admin Panel (Filament)
- [ ] Login admin
- [ ] CRUD DailyLog resource
- [ ] CRUD GlobalSettings
- [ ] View Submissions
- [ ] Filters work
- [ ] Search work
- [ ] Bulk actions (delete)
- [ ] Role-based access (admin vs user)

### 2. Cross-browser Testing
Test trên:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

### 3. Performance Testing

**Lighthouse Audit**:
```bash
# Using Chrome DevTools
# F12 → Lighthouse → Generate report

# Expected scores:
# Performance: > 80
# Accessibility: > 90
# Best Practices: > 90
# SEO: > 90
```

**Database query log** (tạm thời):
`app/Providers/AppServiceProvider.php`:
```php
public function boot(): void
{
    if (app()->environment('local')) {
        \DB::listen(fn($query) => 
            logger($query->sql, $query->bindings)
        );
    }
}
```

Check slow queries:
```bash
tail -f storage/logs/laravel.log | grep sql
```

**Optimization tasks**:
- [ ] Add indexes nếu queries chậm
- [ ] Eager load relationships (with(['tasks', 'user']))
- [ ] Cache global settings: `Cache::remember('settings', 3600, fn() => ...)`
- [ ] Paginate listings (15-25 items/page)
- [ ] Compress images (nếu có)

### 4. Security Audit

#### Common issues check:
- [ ] SQL Injection: Eloquent đã sanitize
- [ ] XSS: Blade `{{ }}` auto-escape, use `{!! !!}` only when necessary
- [ ] CSRF: Laravel token có sẵn, check forms có `@csrf`
- [ ] Authentication: tất cả routes protected by `auth` middleware
- [ ] Authorization: Policies check user owns resource
- [ ] Rate limiting: Apply cho API endpoints
- [ ] Validation: All inputs validated
- [ ] File uploads: Nếu có, validate mime/size
- [ ] Environment: `.env` không commit lên Git
- [ ] HTTPS: Force SSL production

**Tools**:
```bash
# PHPStan (static analysis)
./vendor/bin/phpstan analyse

# Larastan (type safety)
composer require nunomaduro/larastan --dev
./vendor/bin/phpstan analyse

# Security checker
composer require enlightn/security-checker
./vendor/bin/security-checker check
```

### 5. Accessibility Testing
- [ ] Color contrast (WCAG AA)
- [ ] Keyboard navigation
- [ ] Screen reader (NVDA, VoiceOver)
- [ ] Alt texts for images
- [ ] ARIA labels nếu cần

### 6. Mobile Responsive Testing
- [ ] Layout trên < 768px (mobile)
- [ ] Layout trên 768-1024px (tablet)
- [ ] Layout > 1024px (desktop)
- [ ] Touch targets đủ lớn (min 44×44px)
- [ ] Text readable không zoom

### 7. API Testing với Postman/Insomnia
Import collection và test:

**Collection**:
```
POST /api/login
POST /api/logworks
GET /api/logworks
PUT /api/logworks/{id}
DELETE /api/logworks/{id}
POST /api/logworks/{id}/submit
GET /api/settings/user
PUT /api/settings/user
GET /api/submissions
```

Test cases:
- [ ] Valid requests → 200/201
- [ ] Invalid data → 422
- [ ] Unauthorized → 401
- [ ] Forbidden → 403
- [ ] Not found → 404
- [ ] Server error → 500

### 8. Database Integrity Testing
```bash
# Check foreign keys
SELECT 
    tc.table_name, 
    kcu.column_name, 
    ccu.table_name AS foreign_table_name,
    ccu.column_name AS foreign_column_name 
FROM information_schema.table_constraints AS tc 
JOIN information_schema.key_column_usage AS kcu
    ON tc.constraint_name = kcu.constraint_name
JOIN information_schema.constraint_column_usage AS ccu
    ON ccu.constraint_name = tc.constraint_name
WHERE tc.constraint_type = 'FOREIGN KEY';

# Check indexes
SHOW INDEX FROM daily_logs;
SHOW INDEX FROM tasks;

# Check data consistency
SELECT * FROM daily_logs dl 
LEFT JOIN users u ON dl.user_id = u.id 
WHERE u.id IS NULL;  -- Orphans

SELECT * FROM tasks t 
LEFT JOIN daily_logs dl ON t.daily_log_id = dl.id 
WHERE dl.id IS NULL;
```

### 9. Load Testing (optional)
**Package**: `jmeter` hoặc `k6`

```bash
# Install k6
npm install -g k6

# Test script: loadtest.js
import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '30s', target: 10 },
    { duration: '1m', target: 50 },
    { duration: '30s', target: 0 },
  ],
};

export default function () {
  let res = http.get('http://localhost:8000/api/logworks');
  check(res, { 'status was 200': (r) => r.status == 200 });
  sleep(1);
}

# Run
k6 run loadtest.js
```

### 10. User Acceptance Testing (UAT)
- [ ] Create real log với test data
- [ ] Parse AI (mock) tạo tasks chính xác?
- [ ] Submit → Google Form nhận data đúng format?
- [ ] Submission status updates
- [ ] Settings thay đổi behavior
- [ ] Admin panel shows all data

### 11. Regression Testing
Test lại tất cả critical paths:
1. Create log → submit → view history
2. Edit log → update → verify
3. Delete log → confirm không còn
4. Change settings → reload giữ nguyên
5. Submit failure → retry success

### 12. Bug Fixing
.Create issues trên GitHub hoặc file `BUGS.md`:

```markdown
## Known Issues

- [ ] Google Form sometimes returns 404 if form is unpublished
- [ ] Task repeater loses focus when adding new item (Alpine bug)
- [ ] Date picker không đủ UX-friendly
- [ ] No loading indicator on parse button
- [ ] Submission status not real-time
```

Fix优先级:
1. Critical: Data loss, security
2. High: Core features fails
3. Medium: UX issues
4. Low: Nice-to-have

### 13. Code Review
Self-review checklist:
- [ ] PSR-12 coding standards
- [ ] No debug code (`dd()`, `dump()`, `var_dump()`)
- [ ] No hardcoded values
- [ ] Proper error handling (try-catch)
- [ ] Validation rules complete
- [ ] Authorization checked
- [ ] No N+1 queries (use `with()`)
- [ ] No secrets in code (API keys in .env)
- [ ] Commit messages follow convention

### 14. Documentation Updates
Update README.md:
```markdown
# Daily Logwork

## Installation
1. `composer install`
2. `npm install`
3. `.env` copy và configure
4. `php artisan migrate --seed`
5. `npm run build`

## Usage
- Đăng nhập admin/admin
- Tạo logwork mỗi ngày
- Submit to Google Form

## API
- `POST /api/logworks` - Create log
- `GET /api/logworks` - List logs

## Testing
`php artisan test`

## Deployment
See `docs/deployment.md`
```

### 15. Final Deployment Dry-run
Trên staging server:
```bash
git clone <repo> /var/www/logwork-staging
cd /var/www/logwork-staging
cp .env.example .env
php artisan key:generate
# Configure .env staging
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl reread && sudo supervisorctl update
sudo systemctl restart nginx
```

Test:
- [ ] Create logwork
- [ ] Submit works
- [ ] Admin access
- [ ] Error handling

### 16. Production Deployment
```bash
# SSH vào production
ssh user@server

# Pull code
cd /var/www/logwork
git pull origin main

# Install deps
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Migrate
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo supervisorctl restart laravel-queue:*
sudo supervisorctl restart laravel-horizon:*
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx

# Check health
curl https://logwork.yourcompany.com/health
```

## Checklist Files
Tạo file checklists:

**File**: `TESTING_CHECKLIST.md`
```markdown
- [ ] Unit tests pass
- [ ] Feature tests pass
- [ ] Manual testing complete
- [ ] Security audit passed
- [ ] Performance acceptable
- [ ] Accessibility check
- [ ] Mobile responsive
- [ ] Documentation updated
- [ ] Staging deployment OK
```

## Estimated Times
- Manual testing: 2-3h
- Security audit: 1h
- Performance tuning: 2h
- Bug fixing: 1-2h
- Documentation: 1h
- **Total**: ~7-9h

## Pre-Launch Sign-off

Before going live:
- [ ] All tests pass
- [ ] No critical bugs
- [ ] Production env configured
- [ ] SSL certificate installed
- [ ] Backup script working
- [ ] Monitoring setup (Horizon, logs)
- [ ] Admin credentials secure
- [ ] Google Form URL correct
- [ ] Queue worker running
- [ ] Scheduler running
- [ ] Health check returns OK
- [ ] Team notified

---

**Status**: ⏳ Pending  
**Priority**: High (pre-release)  
**Dependencies**: All features implemented  
**Estimated time**: 1 ngày
