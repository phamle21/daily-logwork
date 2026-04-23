<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-4 px-3">
    <div class="max-w-3xl mx-auto mb-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Daily Report</h1>
                <p class="text-xs text-slate-600 mt-1">
                    {{ \Carbon\Carbon::now()->locale('vi')->isoFormat('dddd, D MMM YYYY') }}
                </p>
            </div>
            <a href="{{ route('logwork-history') }}" class="text-slate-600 hover:text-slate-900 p-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </a>
        </div>
    </div>

    <form wire:submit="save" class="max-w-3xl mx-auto space-y-3">
        @csrf

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-3 py-3 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Chọn dự án</h3>
            </div>
            <div class="px-3 py-3">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">
                            {{ substr($selectedProject, 0, 3) }}
                        </span>
                    </div>
                    <select wire:model="selectedProject" class="flex-1 h-8 px-2 py-1 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach($projects as $project)
                            <option value="{{ $project }}">{{ $project }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-3 py-3 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Task hôm nay</h3>
            </div>
            <div class="px-3 py-3">
                <div class="space-y-1.5">
                    @foreach($todayTasks as $index => $task)
                        <div wire:key="today-task-{{ $task['id'] }}" class="flex flex-col sm:flex-row gap-1.5 sm:gap-2 items-start sm:items-center p-1.5 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
                            <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors mt-0.5 sm:mt-0">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 3h2v2H9V3zm0 4h2v2H9V7zm0 4h2v2H9v-2zm4-8h2v2h-2V3zm0 4h2v2h-2V7zm0 4h2v2h-2v-2z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                wire:model="todayTasks.{{ $index }}.description"
                                placeholder="Mô tả..."
                                class="flex-1 h-8 px-2 py-1 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <select
                                wire:model="todayTasks.{{ $index }}.progress"
                                class="w-16 h-8 px-1 py-0.5 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                            >
                                @foreach([0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100] as $percent)
                                    <option value="{{ $percent }}">{{ $percent }}%</option>
                                @endforeach
                            </select>
                            @if($task['progress'] < 100)
                                <input
                                    type="date"
                                    wire:model="todayTasks.{{ $index }}.expectedDate"
                                    class="w-24 h-8 px-2 py-1 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            @endif
                            @if(count($todayTasks) > 1)
                                <button
                                    type="button"
                                    wire:click="removeTodayTask('{{ $task['id'] }}')"
                                    class="h-8 w-8 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 rounded-md flex items-center justify-center"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3L4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button
                    type="button"
                    wire:click="addTodayTask"
                    class="w-full mt-1.5 px-3 py-1.5 text-xs font-medium bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition-colors flex items-center justify-center gap-1"
                >
                    <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-3 py-3 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Task ngày mai</h3>
            </div>
            <div class="px-3 py-3">
                <div class="space-y-1.5">
                    @foreach($tomorrowTasks as $index => $task)
                        <div wire:key="tomorrow-task-{{ $task['id'] }}" class="flex flex-col sm:flex-row gap-1.5 sm:gap-2 items-start sm:items-center p-1.5 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
                            <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors mt-0.5 sm:mt-0">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 3h2v2H9V3zm0 4h2v2H9V7zm0 4h2v2H9v-2zm4-8h2v2h-2V3zm0 4h2v2h-2V7zm0 4h2v2h-2v-2z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                wire:model="tomorrowTasks.{{ $index }}.description"
                                placeholder="Mô tả..."
                                class="flex-1 h-8 px-2 py-1 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            @if(count($tomorrowTasks) > 1)
                                <button
                                    type="button"
                                    wire:click="removeTomorrowTask('{{ $task['id'] }}')"
                                    class="h-8 w-8 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 rounded-md flex items-center justify-center"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3L4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button
                    type="button"
                    wire:click="addTomorrowTask"
                    class="w-full mt-1.5 px-3 py-1.5 text-xs font-medium bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition-colors flex items-center justify-center gap-1"
                >
                    <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-3 py-3 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Tự đánh giá</h3>
            </div>
            <div class="px-3 py-3 space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-slate-900">Chất lượng</label>
                        <span class="text-xs font-medium text-slate-600">
                            @switch($qualityRating)
                                @case(1) Rất kém @break
                                @case(2) Kém @break
                                @case(3) Bình thường @break
                                @case(4) Tốt @break
                                @case(5) Rất tốt @break
                            @endswitch
                        </span>
                    </div>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            @php
                                $colors = ['', 'bg-red-500 hover:bg-red-600', 'bg-orange-500 hover:bg-orange-600', 'bg-yellow-500 hover:bg-yellow-600', 'bg-green-500 hover:bg-green-600', 'bg-emerald-500 hover:bg-emerald-600'];
                            @endphp
                            <button
                                type="button"
                                wire:click="$set('qualityRating', {{ $i }})"
                                class="flex-1 h-8 rounded-md text-xs font-medium transition-colors {{ $qualityRating === $i ? $colors[$i] . ' text-white border-0' : 'bg-white text-slate-700 border border-slate-200 hover:border-slate-300' }}"
                            >
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-slate-900">Tinh thần</label>
                        <span class="text-xs font-medium text-slate-600">
                            @switch($spiritRating)
                                @case(1) Rất kém @break
                                @case(2) Kém @break
                                @case(3) Bình thường @break
                                @case(4) Tốt @break
                                @case(5) Rất tốt @break
                            @endswitch
                        </span>
                    </div>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            @php
                                $colors = ['', 'bg-red-500 hover:bg-red-600', 'bg-orange-500 hover:bg-orange-600', 'bg-yellow-500 hover:bg-yellow-600', 'bg-green-500 hover:bg-green-600', 'bg-emerald-500 hover:bg-emerald-600'];
                            @endphp
                            <button
                                type="button"
                                wire:click="$set('spiritRating', {{ $i }})"
                                class="flex-1 h-8 rounded-md text-xs font-medium transition-colors {{ $spiritRating === $i ? $colors[$i] . ' text-white border-0' : 'bg-white text-slate-700 border border-slate-200 hover:border-slate-300' }}"
                            >
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-3 py-3 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Ghi chú</h3>
            </div>
            <div class="px-3 py-3">
                <textarea
                    wire:model.debounce-500ms="notes"
                    placeholder="Nhập ghi chú (tùy chọn)..."
                    class="w-full min-h-16 px-2 py-1 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="2"
                ></textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-3">
            <div class="flex flex-row justify-between items-center gap-2">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-slate-700">Gửi GForm</label>
                    <button
                        type="button"
                        wire:click="$toggle('submitToGForm')"
                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $submitToGForm ? 'bg-green-500' : 'bg-slate-300' }}"
                    >
                        <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform {{ $submitToGForm ? 'translate-x-5' : 'translate-x-1' }}"></span>
                    </button>
                </div>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-xs font-medium hover:bg-blue-700 transition-colors"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Gửi</span>
                    <span wire:loading>Đang gửi...</span>
                </button>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                @foreach($errors->all() as $error)
                    <p class="text-xs text-red-600">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(session()->has('message'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                <p class="text-xs text-green-600">{{ session('message') }}</p>
            </div>
        @endif
    </form>
</div>
