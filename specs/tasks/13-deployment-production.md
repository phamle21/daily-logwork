# Task 13: Deployment & Production Configuration

## Mục tiêu
Setup production environment, configure production services, optimize performance, monitors.

## Công việc cần làm

### 1. Environment Configuration
**File**: `.env.production` (example)

```env
APP_NAME="Daily Logwork"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://logwork.yourcompany.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=logwork_prod
DB_USERNAME=logwork_user
DB_PASSWORD=strong_password_here

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis
QUEUE_REDIS_PREFIX=logwork_queue

# Google Form
GOOGLE_FORM_URL=https://docs.google.com/forms/d/e/FORM_ID/formResponse
GOOGLE_FORM_ENTRY_SUMMARY=entry.xxxxx
GOOGLE_FORM_ENTRY_TOMORROW=entry.yyyyy
GOOGLE_FORM_ENTRY_DATE=entry.wwwww
GOOGLE_FORM_ENTRY_TASKS_PREFIX=entry.ttttt

# AI (future)
AI_API_KEY=

# Queue
QUEUE_RETRY_ATTEMPTS=3
DEFAULT_SUBMIT_TIME=17:00:00
AUTO_SUBMIT_ENABLED=false

# Files
FILESYSTEM_DISK=public
```

### 2. Generate App Key
```bash
php artisan key:generate
```

### 3. Config Cache & Optimize
```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Clear caches
php artisan optimize:clear

# View cache (if using Blade)
php artisan view:cache
```

### 4. Database Optimization
```sql
-- MySQL Optimization
SET GLOBAL innodb_buffer_pool_size = 256M;
SET GLOBAL query_cache_size = 64M;
```

**Add database config**:
`config/database.php` update connection settings:

```php
'mysql' => [
    'strict' => true,
    'engine' => null,
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ],
],
```

### 5. Redis Setup (Production Queue)
```bash
# Install Redis
sudo apt-get install redis-server -y

# Start Redis
sudo systemctl start redis
sudo systemctl enable redis

# Install PHP Redis extension
sudo apt-get install php-redis -y
sudo service php8.2-fpm restart  # hoặc apache

# Test Redis
redis-cli ping
# Output: PONG
```

**.env**:
```env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### 6. Laravel Horizon (Queue Monitoring)
```bash
composer require laravel/horizon --with-all-dependencies
```

**Publish Horizon**:
```bash
php artisan horizon:install
```

**Config**: `config/horizon.php`
```php
'environments' => [
    'production' => [
        'use' => 'redis',
        'connection' => 'redis',
        'queue' => ['default'],
        'balance' => 'auto',
        'maxProcesses' => 10,
        'memory' => 128,
    ],
],
```

**Setup Supervisor**:
`supervisor/conf.d/laravel-horizon.conf`:
```
[program:laravel-horizon]
process_name=%(program_name)s
command=php /var/www/html/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/laravel/horizon.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-horizon:*
```

### 7. SSL/HTTPS (Let's Encrypt)
```bash
# Install Certbot (Ubuntu)
sudo apt-get install certbot python3-certbot-nginx -y

# Get certificate
sudo certbot --nginx -d logwork.yourcompany.com

# Auto-renew
sudo certbot renew --dry-run
```

**Force HTTPS** in `.htaccess` (Apache) hoặc Nginx config:
```nginx
server {
    listen 80;
    server_name logwork.yourcompany.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name logwork.yourcompany.com;
    
    ssl_certificate /etc/letsencrypt/live/logwork.yourcompany.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/logwork.yourcompany.com/privkey.pem;
    
    # ... rest config
}
```

### 8. Nginx Configuration
**File**: `/etc/nginx/sites-available/logwork`

```nginx
upstream php-upstream {
    server unix:/var/run/php/php8.2-fpm.sock;
}

server {
    listen 80;
    server_name logwork.yourcompany.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name logwork.yourcompany.com;

    root /var/www/html/logwork/public;
    index index.php;

    # SSL
    ssl_certificate /etc/letsencrypt/live/logwork.yourcompany.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/logwork.yourcompany.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # Vite assets
    location /build/ {
        alias /var/www/html/logwork/public/build/;
        expires 1y;
        access_log off;
        add_header Cache-Control "public, immutable";
    }

    # Main application
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php-upstream;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Logging
    access_log /var/log/nginx/logwork-access.log;
    error_log /var/log/nginx/logwork-error.log;
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/logwork /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 9. Supervisor for Queue Worker
**File**: `supervisor/conf.d/laravel-queue.conf`

```
[program:laravel-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/logwork/artisan queue:work redis --sleep=3 --tries=3 --timeout=60
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/laravel/queue.log
stopwaitsecs=3600

[program:laravel-scheduler]
process_name=%(program_name)s
command=php /var/www/html/logwork/artisan schedule:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/laravel/scheduler.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

### 10. Laravel Scheduler (Auto-submit)
**File**: `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Daily auto-submit check
    $schedule->command('logwork:auto-submit')
        ->dailyAt(env('DEFAULT_SUBMIT_TIME', '17:00'))
        ->runInBackground();

    // Clean old failed jobs (weekly)
    $schedule->command('queue:flush-failed')
        ->weekly()
        ->sundays()
        ->at('02:00');

    // Clear old logs (30 days)
    $schedule->command('logwork:clean-old')
        ->daily()
        ->at('03:00');
}
```

**Create command** `php artisan make:command AutoSubmitLogwork`:
```php
public function handle(): void
{
    $users = User::where('auto_submit_enabled', true)->get();
    
    foreach ($users as $user) {
        $log = $this->logworkService->getTodayLog($user);
        
        if ($log && !$log->is_submit_chat) {
            $log->update(['is_submit_chat' => true]);
            
            $submission = $log->submissions()->create([
                'scheduled_at' => now(),
                'status' => 'pending',
            ]);
            
            SubmitGoogleFormJob::dispatch($log->id, $submission->id);
            
            $this->info("Queued auto-submit for user {$user->id}");
        }
    }
}
```

### 11. Backup Strategy
```bash
# Automated backup script
cat > /var/www/backup-logwork.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR=/backups/logwork
DB_NAME=logwork_prod
DB_USER=logwork_user

mkdir -p $BACKUP_DIR

# Database backup
mysqldump $DB_NAME -u $DB_USER -p > $BACKUP_DIR/db_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html/logwork/storage

# Keep only 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
EOF

chmod +x /var/www/backup-logwork.sh

# Add to crontab
crontab -e
# 0 2 * * * /var/www/backup-logwork.sh
```

### 12. Monitoring & Logging
**File**: `config/logging.php` (add Slack channel)

```php
'channels' => [
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => env('LOG_LEVEL', 'critical'),
    ],
],
```

**Log failed submissions**:
```php
Log::channel('slack')->critical('Submission failed', [
    'logwork_id' => $id,
    'error' => $e->getMessage(),
]);
```

### 13. Health Check Endpoint
**Route**: `routes/web.php` hoặc `routes/api.php`

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'environment' => app()->environment(),
        'services' => [
            'database' => DB::connection()->getPdo() ? 'connected' : 'error',
            'redis' => app('redis')->ping() ? 'connected' : 'error',
            'queue' => Queue::size() >= 0 ? 'working' : 'error',
        ],
    ]);
});
```

### 14. Deployment Script
**File**: `deploy.sh` (example)

```bash
#!/bin/bash

set -e

echo "🚀 Starting deployment..."

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo supervisorctl restart laravel-queue:*
sudo supervisorctl restart laravel-horizon:*
sudo supervisorctl restart laravel-scheduler:*

echo "✅ Deployment completed!"
```

### 15. CI/CD Pipeline (GitHub Actions)
**File**: `.github/workflows/deploy.yml`

```yaml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, intl, bcmath, gd, redis
          
      - name: Install Composer dependencies
        run: composer install --no-interaction --prefer-dist --optimize-autoloader
        
      - name: Run tests
        run: |
            cp .env.example .env
            php artisan key:generate
            php artisan test --coverage
        
      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /var/www/html/logwork
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
```

## Files cần configure
- `.env.production`
- `supervisor/conf.d/*.conf`
- `/etc/nginx/sites-available/logwork`
- `deploy.sh`
- `.github/workflows/deploy.yml`
- `config/horizon.php`

## Kiểm tra
```bash
# Check services
sudo supervisorctl status
sudo systemctl status nginx
sudo systemctl status redis

# Test app
curl https://logwork.yourcompany.com/health

# Check logs
tail -f /var/log/nginx/logwork-error.log
tail -f /var/log/laravel/laravel.log
tail -f /var/log/supervisor/laravel-queue.log
```

## Notes
- **Security**: Firewall (UFW), fail2ban, disable root login
- **Backup**: Database + files daily, offsite backup
- **Monitoring**: Use Laravel Telescope (dev) hoặc external (New Relic, Sentry)
- **Scaling**: Nếu nhiều users, cân nhắc load balancer, multiple queue workers
- **SSL**: Auto-renew với Certbot

---

**Status**: ⏳ Pending  
**Priority**: Medium  
**Dependencies**: Toàn bộ hệ thống đã build xong  
**Estimated time**: 1-2 giờ (setup server)
