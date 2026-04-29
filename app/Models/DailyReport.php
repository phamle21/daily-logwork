<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'report_date',
        'quality_rating',
        'spirit_rating',
        'notes',
        'submitted_to_chat',
        'submitted_to_form',
        'submitted_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'quality_rating' => 'integer',
        'spirit_rating' => 'integer',
        'submitted_to_chat' => 'boolean',
        'submitted_to_form' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(ReportTask::class)->orderBy('order');
    }

    public function todayTasks()
    {
        return $this->tasks()->where('task_type', 'today');
    }

    public function tomorrowTasks()
    {
        return $this->tasks()->where('task_type', 'tomorrow');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }
}
