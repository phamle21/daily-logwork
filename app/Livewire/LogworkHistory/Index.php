<?php

namespace App\Livewire\LogworkHistory;

use App\Models\DailyReport;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $reports = DailyReport::with('tasks')
            ->orderBy('report_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.logwork-history.index', [
            'reports' => $reports,
        ]);
    }
}
