<div>
    @if (session()->has('success'))
        <script>
            document.addEventListener('livewire:init', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: '{{ session("success") }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            });
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            document.addEventListener('livewire:init', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: '{{ session("error") }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            });
        </script>
    @endif

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-xl font-semibold text-slate-900 mb-4">Lịch sử báo cáo</h2>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dự án</label>
                    <select wire:model.live="filterProject"
                        class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                        <option value="">Tất cả</option>
                        @foreach ($projects as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Từ ngày</label>
                    <input type="date" wire:model.live="filterDateFrom"
                        class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Đến ngày</label>
                    <input type="date" wire:model.live="filterDateTo"
                        class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Ngày</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Dự án</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Chất lượng</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tinh thần</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($reports as $report)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm text-slate-900">
                                    {{ $report->report_date->format('d/m/Y') }}
                                    @if ($report->report_date->isToday())
                                        <span class="ml-2 inline-flex items-center rounded-full bg-green-100 px-2 text-xs font-medium text-green-700">Hôm nay</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-900">{{ $report->project->name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @foreach (range(1, 5) as $i)
                                        <span class="{{ $i <= $report->quality_rating ? 'text-yellow-400' : 'text-slate-300' }}">★</span>
                                    @endforeach
                                    <span class="ml-1 text-slate-500">({{ $report->quality_rating }}/5)</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ ['', '😵‍💫', '🤒', '😊', '😃', '🔥'][$report->spirit_rating] }}
                                    <span class="ml-1 text-slate-500">({{ $report->spirit_rating }}/5)</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        @if ($report->submitted_to_chat)
                                            <span title="Đã gửi Chat" class="text-blue-500">💬</span>
                                        @endif
                                        @if ($report->submitted_to_form)
                                            <span title="Đã gửi Form" class="text-green-500">📝</span>
                                        @endif
                                        @if ($report->report_date->isToday())
                                            <form method="POST" action="#" class="inline" onsubmit="return confirm('Xác nhận xóa?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" wire:click="delete({{ $report->id }})"
                                                    class="text-red-600 hover:text-red-700">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Chưa có báo cáo nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</div>
