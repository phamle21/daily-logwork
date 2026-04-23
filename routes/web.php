<?php

use App\Livewire\DailyReport\Compact as DailyReportCompact;
use App\Livewire\DailyReport\Index as DailyReportIndex;
use App\Livewire\LogworkHistory\Index as LogworkHistoryIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/', DailyReportIndex::class)->name('daily-report');
    Route::get('/compact', DailyReportCompact::class)->name('daily-report.compact');
    Route::get('/logwork-history', LogworkHistoryIndex::class)->name('logwork-history');
});
