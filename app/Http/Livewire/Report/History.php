<?php

namespace App\Http\Livewire\Report;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DailyReport;

class History extends Component
{
    use WithPagination;

    public $filterProject = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $perPage = 15;

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $report = DailyReport::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($report && $report->report_date->isToday()) {
            $report->delete();
            session()->flash('success', 'Đã xóa báo cáo');
        } else {
            session()->flash('error', 'Chỉ được xóa báo cáo cùng ngày');
        }
    }

    public function render()
    {
        $query = DailyReport::where('user_id', auth()->id())
            ->with(['project', 'tasks']);

        if ($this->filterProject) {
            $query->where('project_id', $this->filterProject);
        }

        if ($this->filterDateFrom) {
            $query->where('report_date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('report_date', '<=', $this->filterDateTo);
        }

        $reports = $query->orderBy('report_date', 'desc')
            ->paginate($this->perPage);

        $projects = \App\Models\Project::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return view('livewire.report.history', [
            'reports' => $reports,
            'projects' => $projects,
        ])
            ->layout('layouts.app');
    }
}
