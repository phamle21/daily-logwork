# Task 1: Project Setup & Dependencies

## Mục tiêu
Tạo Laravel project mới và cài đặt tất cả packages cần thiết cho hệ thống Logwork.

## Công việc cần làm

### 1. Tạo Laravel Project
```bash
composer create-project laravel/laravel daily-logwork "10.*"
cd daily-logwork
```

### 2. Cài đặt Filament Admin Panel
```bash
# Cài Filament core packages
composer require filament/filament:"^3.3" -W
composer require filament/forms:"^3.3" -W
composer require filament/tables:"^3.3" -W

# Cài Filament Schemas (hỗ trợ form schema)
composer require filament/filament-schemas -W
```

### 3. Cài đặt Frontend Packages (Tailwind CSS)
```bash
# Cài Tailwind CSS, PostCSS, Autoprefixer
npm install -D tailwindcss postcss autoprefixer

# Cài Alpine.js cho UI động
npm install alpinejs

# Không cần Livewire vì Filament đã dùng Livewire, nhưng frontend user có thể dùng Alpine
```

### 4. Publish & Setup Filament
```bash
# Install Filament panel
php artisan filament:install --panels

# Tạo admin user mặc định
php artisan make:filament-user
# Nhập: admin@example.com, password, name
```

### 5. Cài đặt Queue Support
```bash
# Migration cho queue table (database driver)
php artisan queue:table
php artisan queue:failed-table

# Chạy migration queue tables
php artisan migrate
```

### 6. Cài đặt Additional Packages
```bash
# Laravel Sanctum (API authentication) - Nếu cần API riêng
composer require laravel/sanctum

# Spatie Permission (nếu cần role/permission chi tiết hơn Filament Shield)
# composer require spatie/laravel-permission

# Laravel Debugbar (development)
composer require barryvdh/laravel-debugbar --dev
```

## Files cần tạo/touch
- ✅ `composer.json` (tự động)
- ✅ `package.json` (tự động)
- ✅ `.env` (copy từ `.env.example`)

## Kiểm tra
```bash
# Kiểm tra Laravel version
php artisan --version

# Kiểm tra Filament đã install
php artisan filament:list

# Kiểm tra Tailwind config
ls tailwind.config.js

# Test server
php artisan serve
# Truy cập http://localhost:8000/admin
```

## Notes
- **Laravel version**: 10.x hoặc 11.x (nếu đã ra stable)
- **PHP version**: >= 8.1
- **Queue driver**: dùng `database` trước, production chuyển `redis`
- **AI parser**: tạm thời disable, sẽ implement sau với interface

---

**Status**: ⏳ Pending  
**Priority**: Highest  
**Estimated time**: 15 phút
