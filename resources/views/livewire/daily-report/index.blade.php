<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8 px-4">
    <div class="max-w-4xl mx-auto mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Daily Report</h1>
                <p class="text-slate-600 mt-2">
                    {{ \Carbon\Carbon::now()->locale('vi')->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>
            <a href="{{ route('logwork-history') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 px-4 py-2 border border-slate-200 rounded-md bg-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Lịch sử
            </a>
        </div>
    </div>

    <form wire:submit="save" class="max-w-4xl mx-auto space-y-6">
        @csrf

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Chọn dự án</h3>
            </div>
            <div class="px-6 py-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-lg bg-blue-100 flex items-center justify-center">
                        <span class="text-lg font-bold text-blue-600">
                            {{ substr($selectedProject, 0, 2) }}
                        </span>
                    </div>
                    <select wire:model="selectedProject" class="flex-1 h-10 px-3 py-2 text-sm border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach($projects as $project)
                            <option value="{{ $project }}">{{ $project }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Task ngày hôm nay</h3>
                <p class="text-sm text-slate-500 mt-1">Nhập các công việc đã làm hôm nay với tiến độ</p>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-2">
                    @foreach($todayTasks as $index => $task)
                        <div wire:key="today-task-{{ $task['id'] }}" class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
                            <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors mt-1 sm:mt-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 3h2v2H9V3zm0 4h2v2H9V7zm0 4h2v2H9v-2zm4-8h2v2h-2V3zm0 4h2v2h-2V7zm0 4h2v2h-2v-2z"/>
                                </svg>
                            </div>
                            <input 
                                type="text"
                                wire:model="todayTasks.{{ $index }}.description"
                                placeholder="Mô tả..."
                                class="flex-1 h-9 px-3 py-2 text-sm border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <select 
                                wire:model="todayTasks.{{ $index }}.progress"
                                class="w-20 h-9 px-2 py-1 text-sm border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                            >
                                @foreach([0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100] as $percent)
                                    <option value="{{ $percent }}">{{ $percent }}%</option>
                                @endforeach
                            </select>
                            @if($task['progress'] < 100)
                                <input 
                                    type="date"
                                    wire:model="todayTasks.{{ $index }}.expectedDate"
                                    class="w-32 h-9 px-3 py-2 text-sm border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            @endif
                            @if(count($todayTasks) > 1)
                                <button 
                                    type="button"
                                    wire:click="removeTodayTask('{{ $task['id'] }}')"
                                    class="h-9 w-9 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 rounded-md flex items-center justify-center"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    class="w-full mt-2 px-4 py-2 text-sm font-medium bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Task dự kiến ngày mai</h3>
                <p class="text-sm text-slate-500 mt-1">Lên kế hoạch cho ngày mai</p>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-2">
                    @foreach($tomorrowTasks as $index => $task)
                        <div wire:key="tomorrow-task-{{ $task['id'] }}" class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
                            <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors mt-1 sm:mt-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 3h2v2H9V3zm0 4h2v2H9V7zm0 4h2v2H9v-2zm4-8h2v2h-2V3zm0 4h2v2h-2V7zm0 4h2v2h-2v-2z"/>
                                </svg>
                            </div>
                            <input 
                                type="text"
                                wire:model="tomorrowTasks.{{ $index }}.description"
                                placeholder="Mô tả..."
                                class="flex-1 h-9 px-3 py-2 text-sm border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            @if(count($tomorrowTasks) > 1)
                                <button 
                                    type="button"
                                    wire:click="removeTomorrowTask('{{ $task['id'] }}')"
                                    class="h-9 w-9 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 rounded-md flex items-center justify-center"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    class="w-full mt-2 px-4 py-2 text-sm font-medium bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Thêm
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Tự đánh giá</h3>
                <p class="text-sm text-slate-500 mt-1">Đánh giá chất lượng công việc và tinh thần làm việc</p>
            </div>
            <div class="px-6 py-4 space-y-8">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="text-sm font-semibold text-slate-900">Chất lượng công việc</label>
                        <span class="text-sm font-medium text-slate-600">
                            @switch($qualityRating)
                                @case(1) Rất kém @break
                                @case(2) Kém @break
                                @case(3) Bình thường @break
                                @case(4) Tốt @break
                                @case(5) Rất tốt @break
                            @endswitch
                        </span>
                    </div>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            @php
                                $colors = ['', 'bg-red-500 hover:bg-red-600', 'bg-orange-500 hover:bg-orange-600', 'bg-yellow-500 hover:bg-yellow-600', 'bg-green-500 hover:bg-green-600', 'bg-emerald-500 hover:bg-emerald-600'];
                            @endphp
                            <button 
                                type="button"
                                wire:click="$set('qualityRating', {{ $i }})"
                                class="flex-1 h-10 rounded-md font-medium transition-colors {{ $qualityRating === $i ? $colors[$i] . ' text-white border-0' : 'bg-white text-slate-700 border border-slate-200 hover:border-slate-300' }}"
                            >
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="text-sm font-semibold text-slate-900">Tinh thần làm việc</label>
                        <span class="text-sm font-medium text-slate-600">
                            @switch($spiritRating)
                                @case(1) Rất kém @break
                                @case(2) Kém @break
                                @case(3) Bình thường @break
                                @case(4) Tốt @break
                                @case(5) Rất tốt @break
                            @endswitch
                        </span>
                    </div>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            @php
                                $colors = ['', 'bg-red-500 hover:bg-red-600', 'bg-orange-500 hover:bg-orange-600', 'bg-yellow-500 hover:bg-yellow-600', 'bg-green-500 hover:bg-green-600', 'bg-emerald-500 hover:bg-emerald-600'];
                            @endphp
                            <button 
                                type="button"
                                wire:click="$set('spiritRating', {{ $i }})"
                                class="flex-1 h-10 rounded-md font-medium transition-colors {{ $spiritRating === $i ? $colors[$i] . ' text-white border-0' : 'bg-white text-slate-700 border border-slate-200 hover:border-slate-300' }}"
                            >
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Ghi chú</h3>
                <p class="text-sm text-slate-500 mt-1">Tùy chọn</p>
            </div>
            <div class="px-6 py-4">
                <textarea 
                    wire:model.debounce-500ms="notes"
                    placeholder="Thêm ghi chú hoặc nhận xét thêm..."
                    class="w-full min-h-24 px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    rows="3"
                ></textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">Google Form Integration</h3>
                        <p class="text-sm text-slate-500 mt-1">Gửi report tới Google Form</p>
                    </div>
                    <button 
                        type="button"
                        wire:click="$toggle('submitToGForm')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $submitToGForm ? 'bg-green-500' : 'bg-slate-300' }}"
                    >
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $submitToGForm ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        <button 
            type="submit"
            class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors text-lg"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Gửi Daily Report</span>
            <span wire:loading class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Đang gửi...
            </span>
        </button>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(session()->has('message'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-600">{{ session('message') }}</p>
            </div>
        @endif
    </form>
</div>