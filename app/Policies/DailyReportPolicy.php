<?php

namespace App\Policies;

use App\Models\DailyReport;
use App\Models\User;

class DailyReportPolicy
{
    public function view(User $user, DailyReport $report): bool
    {
        return $user->id === $report->user_id || $user->isAdmin();
    }

    public function update(User $user, DailyReport $report): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $report->user_id
            && $report->report_date->isToday();
    }

    public function delete(User $user, DailyReport $report): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $report->user_id
            && $report->report_date->isToday();
    }
}
