# Task 7: Routes & Middleware Configuration

## Mục tiêu
Định nghĩa tất cả routes cho API và web, cấu hình middleware, groups, và rate limiting.

## Công việc cần làm

### 1. API Routes
**File**: `routes/api.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LogworkController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\SubmissionController;

// Public routes
Route::post('/login', [AuthController::class, 'login']); // Nếu dùng Sanctum
Route::post('/register', [AuthController::class, 'register']);

// Protected routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Logworks
    Route::get('/logworks', [LogworkController::class, 'index']);
    Route::get('/logworks/today', [LogworkController::class, 'today']);
    Route::post('/logworks', [LogworkController::class, 'store']);
    Route::get('/logworks/{id}', [LogworkController::class, 'show']);
    Route::put('/logworks/{id}', [LogworkController::class, 'update']);
    Route::delete('/logworks/{id}', [LogworkController::class, 'destroy']);
    Route::post('/logworks/{id}/submit', [LogworkController::class, 'submit']);

    // AI Parse (tạm thời)
    Route::post('/logworks/parse', [LogworkController::class, 'parse']);

    // Tasks (concurrent với logwork, có thể embed trong logwork)
    Route::get('/logworks/{logwork}/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::post('/tasks/reorder', [TaskController::class, 'reorder']);

    // Settings
    Route::get('/settings/user', [SettingsController::class, 'user']);
    Route::put('/settings/user', [SettingsController::class, 'updateUser']);

    // Settings (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings/global', [SettingsController::class, 'global']);
        Route::put('/settings/global', [SettingsController::class, 'updateGlobal']);
    });

    // Submissions
    Route::get('/submissions', [SubmissionController::class, 'index']);
    Route::get('/submissions/{id}', [SubmissionController::class, 'show']);

    // Dashboard stats
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
});
```

### 2. Web Routes (Frontend)
**File**: `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogworkController;
use App\Http\Controllers\SettingsController;

// Auth routes (Laravel Breeze/Jetstream/Filament)
// Đã có sẵn từ auth scaffolding

// Homepage redirect
Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');

// Logwork routes (user pages)
Route::middleware('auth')->group(function () {
    // Today's logwork
    Route::get('/logwork/today', [LogworkController::class, 'today'])->name('logwork.today');
    Route::get('/logwork/create', [LogworkController::class, 'create'])->name('logwork.create');
    Route::post('/logwork', [LogworkController::class, 'store'])->name('logwork.store');
    Route::get('/logwork/{id}/edit', [LogworkController::class, 'edit'])->name('logwork.edit');
    Route::put('/logwork/{id}', [LogworkController::class, 'update'])->name('logwork.update');
    Route::delete('/logwork/{id}', [LogworkController::class, 'destroy'])->name('logwork.destroy');

    // History
    Route::get('/logwork/history', [LogworkController::class, 'history'])->name('logwork.history');

    // Settings
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Submissions
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
});

// Filament Admin Panel (đã có sẵn khi install Filament)
// Routes được register tự động bởi FilamentServiceProvider
// Truy cập: /admin
```

### 3. Tạo Middleware Kiểm tra Role
**File**: `app/Http/Middleware/EnsureRole.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            abort(403, 'Unauthorized. Required role: ' . $role);
        }

        return $next($request);
    }
}
```

**Register middleware**: `app/Http/Kernel.php`

```php
protected $routeMiddleware = [
    // ... existing
    'role' => \App\Http\Middleware\EnsureRole::class,
];
```

### 4. Rate Limiting
**File**: `app/Http/Middleware/ThrottleRequests.php` (customize nếu cần)

Trong `RouteServiceProvider` hoặc ` Kernel.php`:

```php
protected function configureRateLimiting()
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    RateLimiter::for('parse-ai', function (Request $request) {
        return Limit::perMinute(10)->by($request->user()->id);
    });
}
```

**Áp dụng vào route**:
```php
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // API routes
});

Route::post('/logworks/parse')->middleware('throttle:parse-ai');
```

### 5. CORS Configuration
**File**: `config/cors.php` (nếu cần frontend separate domain)

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://localhost:5173'], // Vite dev server
'allowed_headers' => ['*'],
'expose_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

### 6. API Response Trait (optional)
**File**: `app/Traits/ApiResponse.php`

```php
<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
```

### 7. Tạo AuthController cho API (nếu dùng Sanctum)
**File**: `app/Http/Controllers/Api/AuthController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = $request->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }
}
```

## Files cần tạo
- Edit `routes/api.php`
- Edit `routes/web.php`
- `app/Http/Middleware/EnsureRole.php`
- Edit `app/Http/Kernel.php` (register middleware)
- `app/Traits/ApiResponse.php` (optional)
- `app/Http/Controllers/Api/AuthController.php` (nếu cần API auth)
- `config/cors.php` (nếu cần)

## Kiểm tra
```bash
# List all routes
php artisan route:list

# Test API endpoint (sau khi có auth)
curl -H "Accept: application/json" http://localhost:8000/api/logworks
```

## Notes
- **Route grouping**: Auth middleware cho tất cả API ngoài login/register
- **Rate limiting**: 60 req/phút cho API, 10 req/phút cho AI parse
- **CORS**: Cho phép frontend origin (http://localhost:5173)
- **Parameter binding**: Route model binding cho {id}
- **Restful**: Tuân theo REST conventions

---

**Status**: ⏳ Pending  
**Priority**: High  
**Dependencies**: Task 6 (Controllers)  
**Estimated time**: 20 phút
