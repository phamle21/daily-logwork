<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Livewire\WithPagination;

class ProjectManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingProjectId = null;

    public $formName = '';
    public $formWebhookUrl = '';
    public $formAvatarUrl = '';
    public $formIsActive = true;
    public $formSortOrder = 0;

    protected $rules = [
        'formName' => 'required|string|max:255|unique:projects,name',
        'formWebhookUrl' => 'nullable|url|max:1000',
        'formAvatarUrl' => 'nullable|url|max:1000',
        'formIsActive' => 'boolean',
        'formSortOrder' => 'integer|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(Project $project)
    {
        $this->editingProjectId = $project->id;
        $this->formName = $project->name;
        $this->formWebhookUrl = $project->webhook_url ?? '';
        $this->formAvatarUrl = $project->avatar_url ?? '';
        $this->formIsActive = $project->is_active;
        $this->formSortOrder = $project->sort_order;
        $this->showEditModal = true;
    }

    public function createProject()
    {
        $this->validate();

        Project::create([
            'name' => $this->formName,
            'webhook_url' => $this->formWebhookUrl ?: null,
            'avatar_url' => $this->formAvatarUrl ?: null,
            'is_active' => $this->formIsActive,
            'sort_order' => $this->formSortOrder,
        ]);

        $this->showCreateModal = false;
        session()->flash('success', 'Đã tạo project thành công');
    }

    public function updateProject()
    {
        $rules = [
            'formName' => 'required|string|max:255|unique:projects,name,' . $this->editingProjectId,
            'formWebhookUrl' => 'nullable|url|max:1000',
            'formAvatarUrl' => 'nullable|url|max:1000',
            'formIsActive' => 'boolean',
            'formSortOrder' => 'integer|min:0',
        ];

        $this->validate($rules);

        $project = Project::findOrFail($this->editingProjectId);
        $project->name = $this->formName;
        $project->webhook_url = $this->formWebhookUrl ?: null;
        $project->avatar_url = $this->formAvatarUrl ?: null;
        $project->is_active = $this->formIsActive;
        $project->sort_order = $this->formSortOrder;
        $project->save();

        $this->showEditModal = false;
        session()->flash('success', 'Đã cập nhật project thành công');
    }

    public function toggleActive(Project $project)
    {
        $project->update(['is_active' => !$project->is_active]);
        session()->flash('success', 'Đã cập nhật trạng thái project');
    }

    public function deleteProject(Project $project)
    {
        $project->delete();
        session()->flash('success', 'Đã xóa project thành công');
    }

    public function testWebhook(Project $project)
    {
        if (!$project->webhook_url) {
            session()->flash('error', 'Project chưa có webhook URL');
            return;
        }

        try {
            $response = Http::timeout(10)->post($project->webhook_url, [
                'text' => "🔌 Test webhook từ Project: {$project->name}",
            ]);

            if ($response->successful()) {
                session()->flash('success', 'Test webhook thành công!');
            } else {
                session()->flash('error', 'Test webhook thất bại: ' . $response->status());
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi kết nối: ' . $e->getMessage());
        }
    }

    protected function resetForm()
    {
        $this->formName = '';
        $this->formWebhookUrl = '';
        $this->formAvatarUrl = '';
        $this->formIsActive = true;
        $this->formSortOrder = 0;
    }

    public function render()
    {
        $query = Project::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $projects = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.admin.project-management', [
            'projects' => $projects,
        ])
            ->layout('layouts.app');
    }
}
