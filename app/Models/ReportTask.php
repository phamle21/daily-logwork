<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'task_type',
        'description',
        'progress',
        'expected_date',
        'order',
    ];

    protected $casts = [
        'progress' => 'integer',
        'order' => 'integer',
        'expected_date' => 'date',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
