<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProjectPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'google_form_enabled',
        'google_form_fields',
    ];

    protected $casts = [
        'google_form_enabled' => 'boolean',
        'google_form_fields' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public static function getForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('user_id', $userId)
            ->with('project')
            ->get();
    }
}
