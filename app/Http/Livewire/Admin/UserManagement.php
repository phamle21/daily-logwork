<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingUserId = null;

    public $formName = '';
    public $formEmail = '';
    public $formPassword = '';
    public $formRole = 'user';

    protected $rules = [
        'formName' => 'required|string|max:255',
        'formEmail' => 'required|email|unique:users',
        'formPassword' => 'required|min:8',
        'formRole' => 'required|in:user,admin',
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

    public function openEditModal(User $user)
    {
        $this->editingUserId = $user->id;
        $this->formName = $user->name;
        $this->formEmail = $user->email;
        $this->formPassword = '';
        $this->formRole = $user->role;
        $this->showEditModal = true;
    }

    public function createUser()
    {
        $this->validate();

        User::create([
            'name' => $this->formName,
            'email' => $this->formEmail,
            'password' => Hash::make($this->formPassword),
            'role' => $this->formRole,
        ]);

        $this->showCreateModal = false;
        session()->flash('success', 'Đã tạo user thành công');
    }

    public function updateUser()
    {
        $rules = [
            'formName' => 'required|string|max:255',
            'formEmail' => 'required|email|unique:users,email,' . $this->editingUserId,
            'formRole' => 'required|in:user,admin',
        ];

        if ($this->formPassword) {
            $rules['formPassword'] = 'required|min:8';
        }

        $this->validate($rules);

        $user = User::findOrFail($this->editingUserId);
        $user->name = $this->formName;
        $user->email = $this->formEmail;
        $user->role = $this->formRole;

        if ($this->formPassword) {
            $user->password = Hash::make($this->formPassword);
        }

        $user->save();

        $this->showEditModal = false;
        session()->flash('success', 'Đã cập nhật user thành công');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Không được xóa chính mình');
            return;
        }

        $user->delete();
        session()->flash('success', 'Đã xóa user thành công');
    }

    protected function resetForm()
    {
        $this->formName = '';
        $this->formEmail = '';
        $this->formPassword = '';
        $this->formRole = 'user';
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ])
            ->layout('layouts.app');
    }
}
