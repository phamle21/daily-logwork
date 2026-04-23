<?php

namespace App\Livewire\DailyReport;

use App\Models\DailyReport;
use Livewire\Component;

class Compact extends Component
{
    public string $selectedProject = 'JRR';

    public array $todayTasks = [];

    public array $tomorrowTasks = [];

    public int $qualityRating = 3;

    public int $spiritRating = 3;

    public string $notes = '';

    public bool $submitToGForm = true;

    public array $projects = [];

    public function mount(): void
    {
        $this->projects = DailyReport::getProjectOptions();
        $this->initializeTasks();
    }

    public function render()
    {
        return view('livewire.daily-report.compact');
    }

    public function addTodayTask(): void
    {
        $this->todayTasks[] = $this->newTask();
    }

    public function removeTodayTask(string $taskId): void
    {
        if (count($this->todayTasks) > 1) {
            $this->todayTasks = array_filter($this->todayTasks, fn ($task) => $task['id'] !== $taskId);
            $this->todayTasks = array_values($this->todayTasks);
        }
    }

    public function addTomorrowTask(): void
    {
        $this->tomorrowTasks[] = $this->newTask();
    }

    public function removeTomorrowTask(string $taskId): void
    {
        if (count($this->tomorrowTasks) > 1) {
            $this->tomorrowTasks = array_filter($this->tomorrowTasks, fn ($task) => $task['id'] !== $taskId);
            $this->tomorrowTasks = array_values($this->tomorrowTasks);
        }
    }

    public function save(): void
    {
        $this->validate([
            'todayTasks.*.description' => 'required|string|max:255',
            'tomorrowTasks.*.description' => 'required|string|max:255',
        ]);

        $report = DailyReport::create([
            'project' => $this->selectedProject,
            'quality_rating' => $this->qualityRating,
            'spirit_rating' => $this->spiritRating,
            'notes' => $this->notes ?: null,
            'submit_to_gform' => $this->submitToGForm,
        ]);

        foreach ($this->todayTasks as $index => $task) {
            if (! empty($task['description'])) {
                $report->tasks()->create([
                    'description' => $task['description'],
                    'progress' => $task['progress'],
                    'expected_date' => $task['expectedDate'] ?: null,
                    'task_type' => 'today',
                    'order' => $index,
                ]);
            }
        }

        foreach ($this->tomorrowTasks as $index => $task) {
            if (! empty($task['description'])) {
                $report->tasks()->create([
                    'description' => $task['description'],
                    'progress' => 0,
                    'expected_date' => null,
                    'task_type' => 'tomorrow',
                    'order' => $index,
                ]);
            }
        }

        session()->flash('message', 'Daily report submitted!');

        $this->reset(['notes', 'submitToGForm']);
        $this->initializeTasks();
    }

    private function initializeTasks(): void
    {
        $this->todayTasks = [$this->newTask()];
        $this->tomorrowTasks = [$this->newTask()];
    }

    private function newTask(): array
    {
        return [
            'id' => uniqid(),
            'description' => '',
            'progress' => 0,
            'expectedDate' => null,
        ];
    }
}
