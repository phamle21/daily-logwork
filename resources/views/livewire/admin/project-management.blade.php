<div>
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-slate-900">Quản lý dự án</h2>
            <button wire:click="openCreateModal"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                + Tạo dự án
            </button>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm kiếm theo tên..."
                class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tên</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Webhook URL</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Thứ tự</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($projects as $project)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $project->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 max-w-xs truncate">
                                {{ $project->webhook_url ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                    {{ $project->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $project->is_active ? 'Hoạt động' : 'Tắt' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $project->sort_order }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <button wire:click="openEditModal({{ $project->id }})"
                                        class="text-blue-600 hover:text-blue-700">Sửa</button>
                                    <button wire:click="toggleActive({{ $project->id }})"
                                        class="text-yellow-600 hover:text-yellow-700">
                                        {{ $project->is_active ? 'Tắt' : 'Bật' }}
                                    </button>
                                    @if ($project->webhook_url)
                                        <button wire:click="testWebhook({{ $project->id }})"
                                            class="text-green-600 hover:text-green-700">Test</button>
                                    @endif
                                    <button wire:click="deleteProject({{ $project->id }})"
                                        wire:confirm="Xác nhận xóa project {{ $project->name }}?"
                                        class="text-red-600 hover:text-red-700">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                Chưa có dự án nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $projects->links() }}
    </div>

    <!-- Create Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Tạo dự án mới</h3>

                <form wire:submit="createProject" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tên dự án</label>
                        <input type="text" wire:model="formName" required
                            class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Webhook URL (tùy chọn)</label>
                        <input type="url" wire:model="formWebhookUrl"
                            class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Avatar URL (tùy chọn)</label>
                        <input type="url" wire:model="formAvatarUrl"
                            class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Thứ tự</label>
                            <input type="number" wire:model="formSortOrder" min="0"
                                class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="formIsActive" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                <span class="ml-2 text-sm text-slate-700">Đang hoạt động</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" wire:click="$set('showCreateModal', false)"
                            class="rounded-md px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Hủy</button>
                        <button type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                            Tạo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit Modal -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Chỉnh sửa dự án</h3>

                <form wire:submit="updateProject" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tên dự án</label>
                        <input type="text" wire:model="formName" required
                            class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Webhook URL (tùy chọn)</label>
                        <input type="url" wire:model="formWebhookUrl"
                            class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Avatar URL (tùy chọn)</label>
                        <input type="url" wire:model="formAvatarUrl"
                            class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Thứ tự</label>
                            <input type="number" wire:model="formSortOrder" min="0"
                                class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="formIsActive" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                <span class="ml-2 text-sm text-slate-700">Đang hoạt động</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" wire:click="$set('showEditModal', false)"
                            class="rounded-md px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Hủy</button>
                        <button type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                            Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
