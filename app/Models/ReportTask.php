<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportTask extends Model
{
    protected $fillable = [
        'daily_report_id',
        'description',
        'progress',
        'expected_date',
        'task_type',
        'order',
    ];

    protected $casts = [
        'expected_date' => 'date',
        'progress' => 'integer',
        'task_type' => 'string',
        'order' => 'integer',
    ];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }
}
