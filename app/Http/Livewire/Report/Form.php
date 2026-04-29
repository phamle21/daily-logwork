<?php

namespace App\Http\Livewire\Report;

use Livewire\Component;
use App\Models\Project;
use App\Models\DailyReport;
use App\Models\ReportTask;

class Form extends Component
{
    public $projects = [];
    public $projectId = '';
    public $reportDate;
    public $quality = 3;
    public $spirit = 3;
    public $notes = '';
    public $submitToChat = true;
    public $submitToForm = true;

    public $todayTasks = [];
    public $tomorrowTasks = [];

    public $editingReportId = null;

    protected $rules = [
        'projectId' => 'required|exists:projects,id',
        'quality' => 'required|integer|min:1|max:5',
        'spirit' => 'required|integer|min:1|max:5',
        'todayTasks' => 'nullable|array',
        'todayTasks.*.description' => 'nullable|string|max:500',
        'todayTasks.*.progress' => 'nullable|integer|min:0|max:100',
        'todayTasks.*.expected_date' => 'nullable|date',
        'tomorrowTasks' => 'nullable|array',
        'tomorrowTasks.*.description' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'todayTasks.*.description.unique_from_empty' => 'Mô tả không được để trống',
    ];

    public function mount()
    {
        $this->projects = Project::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->reportDate = now()->format('Y-m-d');

        if (empty($this->projectId) && !empty($this->projects)) {
            $this->projectId = array_key_first($this->projects);
        }

        // Load existing report for today if exists
        $existing = DailyReport::where('user_id', auth()->id())
            ->where('project_id', $this->projectId)
            ->where('report_date', today())
            ->with('tasks')
            ->first();

        if ($existing) {
            $this->editingReportId = $existing->id;
            $this->quality = $existing->quality_rating;
            $this->spirit = $existing->spirit_rating;
            $this->notes = $existing->notes;

            foreach ($existing->tasks as $task) {
                if ($task->task_type === 'today') {
                    $this->todayTasks[] = [
                        'id' => uniqid(),
                        'description' => $task->description,
                        'progress' => $task->progress,
                        'expected_date' => $task->expected_date?->format('Y-m-d'),
                    ];
                } else {
                    $this->tomorrowTasks[] = [
                        'id' => uniqid(),
                        'description' => $task->description,
                    ];
                }
            }
        }
    }

    public function updatedProjectId()
    {
        // Check for existing report for today when switching projects
        $existing = DailyReport::where('user_id', auth()->id())
            ->where('project_id', $this->projectId)
            ->where('report_date', today())
            ->with('tasks')
            ->first();

        if ($existing) {
            $this->editingReportId = $existing->id;
            $this->quality = $existing->quality_rating;
            $this->spirit = $existing->spirit_rating;
            $this->notes = $existing->notes;
            $this->todayTasks = [];
            $this->tomorrowTasks = [];

            foreach ($existing->tasks as $task) {
                if ($task->task_type === 'today') {
                    $this->todayTasks[] = [
                        'id' => uniqid(),
                        'description' => $task->description,
                        'progress' => $task->progress,
                        'expected_date' => $task->expected_date?->format('Y-m-d'),
                    ];
                } else {
                    $this->tomorrowTasks[] = [
                        'id' => uniqid(),
                        'description' => $task->description,
                    ];
                }
            }
        } else {
            $this->editingReportId = null;
            $this->quality = 3;
            $this->spirit = 3;
            $this->notes = '';
            $this->todayTasks = [];
            $this->tomorrowTasks = [];
        }
    }

    public function addTodayTask()
    {
        $this->todayTasks[] = [
            'id' => uniqid(),
            'description' => '',
            'progress' => 0,
            'expected_date' => '',
        ];
    }

    public function addTomorrowTask()
    {
        $this->tomorrowTasks[] = [
            'id' => uniqid(),
            'description' => '',
        ];
    }

    public function removeTodayTask($index)
    {
        unset($this->todayTasks[$index]);
        $this->todayTasks = array_values($this->todayTasks);
    }

    public function removeTomorrowTask($index)
    {
        unset($this->tomorrowTasks[$index]);
        $this->tomorrowTasks = array_values($this->tomorrowTasks);
    }

    public function save()
    {
        $this->validate([
            'projectId' => 'required|exists:projects,id',
            'quality' => 'required|integer|min:1|max:5',
            'spirit' => 'required|integer|min:1|max:5',
            'todayTasks' => 'nullable|array',
            'todayTasks.*.description' => 'nullable|string|max:500',
            'todayTasks.*.progress' => 'nullable|integer|min:0|max:100',
            'todayTasks.*.expected_date' => 'nullable|date',
            'tomorrowTasks' => 'nullable|array',
            'tomorrowTasks.*.description' => 'nullable|string|max:500',
        ]);

        $todayTasks = array_values(array_filter($this->todayTasks, fn($t) => !empty(trim($t['description']))));
        $tomorrowTasks = array_values(array_filter($this->tomorrowTasks, fn($t) => !empty(trim($t['description']))));

        if (empty($todayTasks) && empty($tomorrowTasks)) {
            session()->flash('error', 'Vui lòng thêm ít nhất một task');
            return;
        }

        try {
            $report = DailyReport::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'project_id' => $this->projectId,
                    'report_date' => $this->reportDate,
                ],
                [
                    'quality_rating' => $this->quality,
                    'spirit_rating' => $this->spirit,
                    'notes' => $this->notes,
                ]
            );

            $report->tasks()->delete();

            $order = 0;
            foreach ($todayTasks as $task) {
                $report->tasks()->create([
                    'task_type' => 'today',
                    'description' => trim($task['description']),
                    'progress' => $task['progress'] ?? 0,
                    'expected_date' => $task['expected_date'] ?: null,
                    'order' => $order++,
                ]);
            }

            foreach ($tomorrowTasks as $task) {
                $report->tasks()->create([
                    'task_type' => 'tomorrow',
                    'description' => trim($task['description']),
                    'progress' => 0,
                    'expected_date' => null,
                    'order' => $order++,
                ]);
            }

            // Send webhook if enabled
            if ($this->submitToChat) {
                $this->sendWebhook($report);
            }

            // Send Google Form if enabled
            if ($this->submitToForm) {
                $this->sendGoogleForm($report);
            }

            session()->flash('success', 'Báo cáo đã được lưu thành công!');

            return redirect()->route('report.history');
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    protected function sendWebhook(DailyReport $report): void
    {
        $project = $report->project;
        if (!$project || !$project->webhook_url) {
            return;
        }

        $todayTasks = $report->todayTasks->filter(fn($t) => !empty(trim($t->description)));
        $tomorrowTasks = $report->tomorrowTasks->filter(fn($t) => !empty(trim($t->description)));

        $qualityEmojis = ['', '❌', '⚠️', '✅', '🌟', '🏆'];
        $spiritEmojis = ['', '😵‍💫', '🤒', '😊', '😃', '🔥'];

        $card = [
            'header' => [
                'title' => "📋 Báo cáo ngày {$report->report_date->format('d/m/Y')}",
                'subtitle' => $report->project->name,
                'imageUrl' => $project->avatar_url ?? '',
            ],
            'sections' => [[
                'widgets' => [
                    ['keyValue' => [
                        'topLabel' => 'Chất lượng',
                        'content' => ['text' => ($qualityEmojis[$report->quality_rating] ?? '') . " {$report->quality_rating}/5"],
                    ]],
                    ['keyValue' => [
                        'topLabel' => 'Tinh thần',
                        'content' => ['text' => ($spiritEmojis[$report->spirit_rating] ?? '') . " {$report->spirit_rating}/5"],
                    ]],
                ],
            ]],
        ];

        if ($todayTasks->isNotEmpty()) {
            $section = ['header' => '📌 Công việc hôm nay'];
            foreach ($todayTasks as $task) {
                $progress = $task->progress > 0 ? " ({$task->progress}%)" : '';
                $section['widgets'][] = ['text' => ['text' => "• {$task->description}{$progress}"]];
            }
            $card['sections'][] = $section;
        }

        if ($tomorrowTasks->isNotEmpty()) {
            $section = ['header' => '📅 Kế hoạch ngày mai'];
            foreach ($tomorrowTasks as $task) {
                $section['widgets'][] = ['text' => ['text' => "• {$task->description}"]];
            }
            $card['sections'][] = $section;
        }

        $ch = curl_init($project->webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['cards' => [$card]]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);

        $report->update(['submitted_to_chat' => true, 'submitted_at' => now()]);
    }

    protected function sendGoogleForm(DailyReport $report): void
    {
        $pref = \App\Models\UserProjectPreference::firstOrCreate(
            ['user_id' => auth()->id(), 'project_id' => $report->project_id],
            ['google_form_enabled' => true]
        );

        if (!$pref->google_form_enabled || !$pref->google_form_fields) {
            return;
        }

        $fields = $pref->google_form_fields;
        $todayTasks = $report->todayTasks->pluck('description')->join("\n");
        $tomorrowTasks = $report->tomorrowTasks->pluck('description')->join("\n");

        $data = [
            $fields['email'] ?? '' => $report->user->email,
            $fields['project'] ?? '' => $report->project->name,
            $fields['date_year'] ?? '' => $report->report_date->format('Y'),
            $fields['date_month'] ?? '' => $report->report_date->format('m'),
            $fields['date_day'] ?? '' => $report->report_date->format('d'),
            $fields['task_today'] ?? '' => $todayTasks,
            $fields['task_tomorrow'] ?? '' => $tomorrowTasks,
            $fields['quality'] ?? '' => $report->quality_rating,
            $fields['spirit'] ?? '' => $report->spirit_rating,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://docs.google.com/forms/d/e/YOUR_FORM_ID/formResponse');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_exec($ch);
        curl_close($ch);

        $report->update(['submitted_to_form' => true, 'submitted_at' => now()]);
    }

    public function render()
    {
        return view('livewire.report.form')
            ->layout('layouts.app');
    }
}
