<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\DailyReport;
use App\Models\Project;

class Dashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('report.form');
        }

        $this->stats = [
            'total_users' => User::count(),
            'total_reports' => DailyReport::count(),
            'reports_today' => DailyReport::where('report_date', today())->count(),
            'total_projects' => Project::count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.app');
    }
}
