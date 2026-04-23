<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    protected $fillable = [
        'project',
        'report_date',
        'quality_rating',
        'spirit_rating',
        'notes',
        'submit_to_gform',
        'gform_response_id',
    ];

    protected $casts = [
        'report_date' => 'date',
        'quality_rating' => 'integer',
        'spirit_rating' => 'integer',
        'submit_to_gform' => 'boolean',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(ReportTask::class);
    }

    public static function getProjectOptions(): array
    {
        return [
            'JRR',
            'Primas',
            'Project A',
            'Project B',
            'Project C',
            'Project D',
            'Project E',
            'Project F',
            'Project G',
            'Project H',
        ];
    }
}
