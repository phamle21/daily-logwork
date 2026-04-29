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
        <!-- Project Selector -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-xl font-semibold text-slate-900 mb-4">Chọn dự án</h2>
            <div class="w-64">
                <select wire:model.live="projectId"
                    class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    @foreach ($projects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('projectId')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Today Tasks -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-slate-900">Công việc hôm nay</h2>
                <button type="button" wire:click="addTodayTask"
                    class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    + Thêm
                </button>
            </div>

            <div class="space-y-2">
                @forelse ($todayTasks as $index => $task)
                    <div class="flex items-start gap-2" wire:key="today-{{ $task['id'] }}">
                        <input type="text" wire:model.live="todayTasks.{{ $index }}.description" placeholder="Mô tả công việc..."
                            class="flex-1 rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                        <input type="number" wire:model.live="todayTasks.{{ $index }}.progress" min="0" max="100" placeholder="%"
                            class="w-20 rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm text-center">
                        <input type="date" wire:model.live="todayTasks.{{ $index }}.expected_date"
                            class="rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                        <button type="button" wire:click="removeTodayTask({{ $index }})"
                            class="text-red-600 hover:text-red-700 p-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 italic">Chưa có công việc nào</p>
                @endforelse
            </div>
        </div>

        <!-- Tomorrow Tasks -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-slate-900">Kế hoạch ngày mai</h2>
                <button type="button" wire:click="addTomorrowTask"
                    class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    + Thêm
                </button>
            </div>

            <div class="space-y-2">
                @forelse ($tomorrowTasks as $index => $task)
                    <div class="flex items-start gap-2" wire:key="tomorrow-{{ $task['id'] }}">
                        <input type="text" wire:model.live="tomorrowTasks.{{ $index }}.description" placeholder="Mô tả công việc..."
                            class="flex-1 rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                        <button type="button" wire:click="removeTomorrowTask({{ $index }})"
                            class="text-red-600 hover:text-red-700 p-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 italic">Chưa có kế hoạch nào</p>
                @endforelse
            </div>
        </div>

        <!-- Ratings & Notes -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-xl font-semibold text-slate-900 mb-4">Đánh giá</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Chất lượng</label>
                    <div class="flex gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('quality', {{ $i }})"
                                class="flex-1 rounded-md py-2 text-sm font-semibold transition
                                    {{ $quality === $i ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ ['', '❌', '⚠️', '✅', '🌟', '🏆'][$i] }}
                            </button>
                        @endfor
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tinh thần</label>
                    <div class="flex gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('spirit', {{ $i }})"
                                class="flex-1 rounded-md py-2 text-sm font-semibold transition
                                    {{ $spirit === $i ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ ['', '😵‍💫', '🤒', '😊', '😃', '🔥'][$i] }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Ghi chú (tùy chọn)</label>
                <textarea wire:model.debounce-500ms="notes" rows="3"
                    class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm"></textarea>
            </div>

            <!-- Submit Options -->
            <div class="flex items-center gap-6 mb-6">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="submitToChat" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                    <span class="ml-2 text-sm text-slate-700">Gửi vào Google Chat</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="submitToForm" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                    <span class="ml-2 text-sm text-slate-700">Gửi vào Google Form</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button wire:click="save"
                    class="inline-flex items-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <span wire:loading.remove wire:target="save">Lưu báo cáo</span>
                    <span wire:loading wire:target="save">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Đang lưu...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
