<?php

use App\Http\Livewire\Report\Form;
use App\Http\Livewire\Report\History;
use App\Http\Livewire\Profile\Edit as ProfileEdit;
use App\Http\Livewire\Admin\Dashboard;
use App\Http\Livewire\Admin\UserManagement;
use App\Http\Livewire\Admin\ProjectManagement;
use Illuminate\Support\Facades\Route;

// Auth routes (Fortify handles these)

Route::middleware('auth')->group(function () {
    // Redirect to report form on home
    Route::get('/', Form::class)->name('report.form');

    // Report
    Route::get('/report/history', History::class)->name('report.history');

    // Profile
    Route::get('/profile', ProfileEdit::class)->name('profile.edit');

    // Admin
    Route::middleware(function ($request, $next) {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('report.form');
        }
        return $next($request);
    })->group(function () {
        Route::get('/admin', Dashboard::class)->name('admin.dashboard');
        Route::get('/admin/users', UserManagement::class)->name('admin.users');
        Route::get('/admin/projects', ProjectManagement::class)->name('admin.projects');
    });
});
