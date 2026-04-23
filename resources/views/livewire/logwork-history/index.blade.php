<div class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-4xl mx-auto mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-slate-900">Lịch sử Daily Report</h1>
            <a href="{{ route('daily-report') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 px-4 py-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m5 5l5-5"/>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-4">
        @forelse($reports as $report)
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                <span class="text-xs font-medium text-slate-500">
                                    {{ substr($report->project, 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $report->project }}</h3>
                                <p class="text-sm text-slate-500">{{ $report->report_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-center">
                                <p class="text-xs text-slate-500">Chất lượng</p>
                                <p class="text-lg font-semibold text-slate-900">{{ $report->quality_rating }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-slate-500">Tinh thần</p>
                                <p class="text-lg font-semibold text-slate-900">{{ $report->spirit_rating }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-3">
                        @if($report->tasks->where('task_type', 'today')->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-slate-700 mb-2">Task hôm nay</h4>
                                <div class="space-y-2">
                                    @foreach($report->tasks->where('task_type', 'today') as $task)
                                        <div class="flex items-center gap-3 text-sm">
                                            <span class="text-slate-400">{{ $task->progress }}%</span>
                                            <span class="text-slate-900">{{ $task->description }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if($report->tasks->where('task_type', 'tomorrow')->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-slate-700 mb-2">Task ngày mai</h4>
                                <div class="space-y-2">
                                    @foreach($report->tasks->where('task_type', 'tomorrow') as $task)
                                        <div class="flex items-center gap-3 text-sm">
                                            <span class="text-slate-900">{{ $task->description }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if($report->notes)
                            <div>
                                <h4 class="text-sm font-medium text-slate-700 mb-1">Ghi chú</h4>
                                <p class="text-sm text-slate-600">{{ $report->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-8 text-center">
                <p class="text-slate-500">Chưa có Daily Report nào.</p>
            </div>
        @endforelse

        @if($reports->hasPages())
            <div class="flex justify-center gap-2">
                @if($reports->onFirstPage())
                    <span class="px-3 py-2 text-slate-400">...</span>
                @else
                    <a href="{{ $reports->previousPageUrl() }}" class="px-3 py-2 text-slate-600 hover:text-slate-900">Trước</a>
                @endif
                
                @foreach($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                    @if($page == $reports->currentPage())
                        <span class="px-3 py-2 bg-blue-600 text-white rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 text-slate-600 hover:text-slate-900">{{ $page }}</a>
                    @endif
                @endforeach

                @if($reports->hasMorePages())
                    <a href="{{ $reports->nextPageUrl() }}" class="px-3 py-2 text-slate-600 hover:text-slate-900">Sau</a>
                @else
                    <span class="px-3 py-2 text-slate-400">...</span>
                @endif
            </div>
        @endif
    </div>
</div>