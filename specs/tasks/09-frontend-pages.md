# Task 9: Frontend Pages (Tailwind + Alpine)

## Mục tiêu
Xây dựng frontend pages cho user: create/edit logwork, history list, settings page với Tailwind CSS và Alpine.js.

## Công việc cần làm

### 1. Cấu hình Tailwind CSS
**File**: `tailwind.config.js`

```js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/filament/**/*.blade.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
          950: '#172554',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
```

**File**: `resources/css/app.css`

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom styles */
.toggle-checkbox:checked {
    @apply: right-0 border-green-400;
    right: 0;
    border-color: #68D391;
}
.toggle-checkbox:checked + .toggle-label {
    @apply: bg-green-400;
    background-color: #68D391;
}
```

### 2. Tạo Main Layout
**File**: `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Logwork') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-primary-600">
                            Daily Logwork
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('logwork.today') }}" class="text-gray-600 hover:text-gray-900">
                                Today
                            </a>
                            <a href="{{ route('logwork.history') }}" class="text-gray-600 hover:text-gray-900">
                                History
                            </a>
                            <a href="{{ route('settings.edit') }}" class="text-gray-600 hover:text-gray-900">
                                Settings
                            </a>
                            <a href="{{ route('submissions.index') }}" class="text-gray-600 hover:text-gray-900">
                                Submissions
                            </a>
                            
                            <!-- Profile Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900">
                                    <span>{{ auth()->user()->name }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Flash Messages -->
    <x-filament::notification />
</body>
</html>
```

### 3. Dashboard Page (Home)
**File**: `resources/views/dashboard.blade.php`

```blade
<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Logworks Today</dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ $todayLog ? '1 (Done)' : '0 (Pending)' }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Tasks</dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ $todayLog?->tasks->sum('progress_percent') ?? 0 }}%
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Time</dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ $todayLog?->tasks->sum('estimated_time') ?? 0 }} min
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Logs</dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ auth()->user()->dailyLogs()->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="flex space-x-4">
                    @if(!$todayLog)
                        <a href="{{ route('logwork.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Today's Logwork
                        </a>
                    @else
                        <a href="{{ route('logwork.edit', $todayLog) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Edit Today's Logwork
                        </a>
                        <a href="{{ route('logwork.history') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            View History
                        </a>
                    @endif
                </div>
            </div>

            <!-- Recent Submissions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Submissions</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tasks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentSubmissions as $submission)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $submission->dailyLog->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $submission->dailyLog->tasks->count() }} tasks
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $submission->status === 'success' ? 'bg-green-100 text-green-800' : 
                                           ($submission->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $submission->submitted_at?->format('d/m/Y H:i') ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No submissions yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
```

### 4. Create/Edit Logwork Page (Complex)
**File**: `resources/views/logwork/form.blade.php`

```blade
<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    {{ $logwork ? 'Edit Logwork for ' . $logwork->date->format('d/m/Y') : 'Create Today Logwork' }}
                </h1>

                <form id="logwork-form" action="{{ $logwork ? route('logwork.update', $logwork) : route('logwork.store') }}" 
                      method="POST">
                    @csrf
                    @if($logwork)
                        @method('PUT')
                    @endif

                    <div class="space-y-8">
                        <!-- Raw Input Section -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Raw Input (Nhập nội dung làm việc hôm nay)
                            </label>
                            <textarea 
                                id="raw-input"
                                name="raw_input[text]"
                                rows="4"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="VD: Hôm nay tôi đã làm API login, fix bug đăng nhập, và viết unit test..."
                            >{{ old('raw_input.text', $logwork->raw_input['text'] ?? '') }}</textarea>

                            <div class="mt-2 flex justify-between items-center">
                                <p class="text-sm text-gray-500">
                                    Nhập nội dung thô, hệ thống sẽ parse thành tasks (AI tạm thời)
                                </p>
                                <button 
                                    type="button"
                                    id="parse-btn"
                                    class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm font-medium"
                                >
                                    Parse Tasks (AI Mock)
                                </button>
                            </div>
                        </div>

                        <!-- Tasks Repeater Section -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Tasks List
                                    <span class="text-red-500">*</span>
                                </label>
                                <button 
                                    type="button"
                                    id="add-task-btn"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                                >
                                    + Add Task
                                </button>
                            </div>

                            <div id="tasks-container" class="space-y-4">
                                @if($logwork && $logwork->tasks->count())
                                    @foreach($logwork->tasks as $index => $task)
                                        <div class="task-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-index="{{ $index }}">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-6">
                                                    <label class="block text-xs font-medium text-gray-500 mb-1">Task Name</label>
                                                    <input type="text" 
                                                           name="tasks[{{ $index }}][title]"
                                                           value="{{ old('tasks.'.$index.'.title', $task->title) }}"
                                                           class="block w-full rounded-md border-gray-300 shadow-sm"
                                                           required>
                                                </div>
                                                <div class="col-span-2">
                                                    <label class="block text-xs font-medium text-gray-500 mb-1">Progress %</label>
                                                    <input type="number" 
                                                           name="tasks[{{ $index }}][progress_percent]"
                                                           value="{{ old('tasks.'.$index.'.progress_percent', $task->progress_percent) }}"
                                                           min="0" max="100"
                                                           class="block w-full rounded-md border-gray-300 shadow-sm"
                                                           required>
                                                </div>
                                                <div class="col-span-2">
                                                    <label class="block text-xs font-medium text-gray-500 mb-1">Est. Time (min)</label>
                                                    <input type="number" 
                                                           name="tasks[{{ $index }}][estimated_time]"
                                                           value="{{ old('tasks.'.$index.'.estimated_time', $task->estimated_time) }}"
                                                           min="1"
                                                           class="block w-full rounded-md border-gray-300 shadow-sm"
                                                           required>
                                                </div>
                                                <div class="col-span-2 flex items-end">
                                                    <button type="button" 
                                                            class="remove-task-btn text-red-500 hover:text-red-700"
                                                            onclick="removeTask(this)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <p id="no-tasks-warning" class="text-red-500 text-sm mt-2" style="display: none;">
                                * Vui lòng thêm ít nhất 1 task
                            </p>
                        </div>

                        <!-- Summary & Tomorrow Plan -->
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Summary (Tổng kết ngày)
                                </label>
                                <textarea 
                                    name="summary"
                                    rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Tóm tắt những gì đã làm hôm nay..."
                                >{{ old('summary', $logwork->summary ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tomorrow Plan (Kế hoạch ngày mai)
                                </label>
                                <textarea 
                                    name="tomorrow_plan"
                                    rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Những việc sẽ làm ngày mai..."
                                >{{ old('tomorrow_plan', $logwork->tomorrow_plan ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Submitter Options -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Google Form Submission</h3>
                                    <p class="text-sm text-gray-500">
                                        Auto-submit logwork này lên Google Form
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           name="is_submit_chat" 
                                           value="1"
                                           {{ old('is_submit_chat', $logwork->is_submit_chat ?? false) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3 border-t border-gray-200 pt-6">
                            <a href="{{ route('logwork.history') }}" 
                               class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button 
                                type="submit"
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                onclick="validateForm(event)"
                            >
                                {{ $logwork ? 'Update Logwork' : 'Create Logwork' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js Task Repeater Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            // Alpine component logic
        });

        let taskIndex = {{ $logwork && $logwork->tasks->count() ? $logwork->tasks->count() : 0 }};

        function addTask() {
            taskIndex++;
            const container = document.getElementById('tasks-container');
            const newTask = document.createElement('div');
            newTask.className = 'task-item bg-gray-50 p-4 rounded-lg border border-gray-200';
            newTask.setAttribute('data-index', taskIndex - 1);
            newTask.innerHTML = `
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Task Name</label>
                        <input type="text" 
                               name="tasks[${taskIndex - 1}][title]"
                               class="block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Progress %</label>
                        <input type="number" 
                               name="tasks[${taskIndex - 1}][progress_percent]"
                               value="100"
                               min="0" max="100"
                               class="block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Est. Time (min)</label>
                        <input type="number" 
                               name="tasks[${taskIndex - 1}][estimated_time]"
                               value="60"
                               min="1"
                               class="block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                    </div>
                    <div class="col-span-2 flex items-end">
                        <button type="button" 
                                class="remove-task-btn text-red-500 hover:text-red-700"
                                onclick="removeTask(this)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newTask);
            updateTaskIndices();
        }

        function removeTask(button) {
            button.closest('.task-item').remove();
            updateTaskIndices();
        }

        function updateTaskIndices() {
            const tasks = document.querySelectorAll('.task-item');
            tasks.forEach((task, index) => {
                task.setAttribute('data-index', index);
                const inputs = task.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/tasks\[\d+\]/, `tasks[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }

        function validateForm(event) {
            const tasks = document.querySelectorAll('.task-item');
            if (tasks.length === 0) {
                event.preventDefault();
                document.getElementById('no-tasks-warning').style.display = 'block';
                return false;
            }
            return true;
        }

        // Parse button click
        document.getElementById('parse-btn')?.addEventListener('click', function() {
            const rawInput = document.getElementById('raw-input').value;
            if (!rawInput.trim()) {
                alert('Vui lòng nhập raw input trước khi parse');
                return;
            }

            fetch('/api/logworks/parse', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ raw_text: rawInput })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear existing tasks
                    document.getElementById('tasks-container').innerHTML = '';
                    
                    // Add parsed tasks
                    data.data.tasks.forEach(task => {
                        taskIndex++;
                        const container = document.getElementById('tasks-container');
                        const newTask = document.createElement('div');
                        newTask.className = 'task-item bg-gray-50 p-4 rounded-lg border border-gray-200';
                        newTask.innerHTML = `
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-6">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Task Name</label>
                                    <input type="text" 
                                           name="tasks[${taskIndex - 1}][title]"
                                           value="${task.title}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm"
                                           required>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Progress %</label>
                                    <input type="number" 
                                           name="tasks[${taskIndex - 1}][progress_percent]"
                                           value="${task.progress_percent}"
                                           min="0" max="100"
                                           class="block w-full rounded-md border-gray-300 shadow-sm"
                                           required>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Est. Time (min)</label>
                                    <input type="number" 
                                           name="tasks[${taskIndex - 1}][estimated_time]"
                                           value="${task.estimated_time}"
                                           min="1"
                                           class="block w-full rounded-md border-gray-300 shadow-sm"
                                           required>
                                </div>
                                <div class="col-span-2 flex items-end">
                                    <button type="button" 
                                            class="remove-task-btn text-red-500 hover:text-red-700"
                                            onclick="removeTask(this)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        `;
                        container.appendChild(newTask);
                    });
                    
                    // Update current date if empty
                    if (!document.querySelector('input[name="date"]')?.value) {
                        // Date auto-set by controller
                    }
                } else {
                    alert('Parse failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra');
            });
        });

        // Initialize
        document.getElementById('add-task-btn').addEventListener('click', addTask);
    </script>
</x-app-layout>
```

### 5. Edit Page (reuse create view)
**File**: `resources/views/logwork/edit.blade.php`

```blade
{{-- Reuse create.blade.php --}}
@extends('logwork.form')
```

Update controller để pass `$logwork` variable.

### 6. History List Page
**File**: `resources/views/logwork/history.blade.php`

```blade
<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Logwork History</h1>
                <a href="{{ route('logwork.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    + New Logwork
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <form method="GET" class="flex space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="from" value="{{ request('from') }}" 
                               class="rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" name="to" value="{{ request('to') }}" 
                               class="rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white shadow overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tasks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @foreach($log->tasks as $task)
                                        <div class="flex items-center mb-1">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                            {{ $task->title }} ({{ $task->progress_percent }}%)
                                        </div>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->tasks->sum('estimated_time') }} min
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->submitted_at)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Submitted {{ $log->submitted_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('logwork.edit', $log) }}" class="text-blue-600 hover:text-blue-900">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    No logwork records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
```

### 7. Settings Page
**File**: `resources/views/settings/edit.blade.php`

```blade
<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Settings</h1>

            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white shadow rounded-lg p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Submission Preferences</h3>
                        <p class="text-sm text-gray-500">Configure your daily logwork submission settings</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Auto Submit</label>
                            <p class="text-sm text-gray-500">Automatically submit logwork at preferred time</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="auto_submit_enabled" value="1"
                                   {{ old('auto_submit_enabled', auth()->user()->userSetting?->auto_submit_enabled ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Submit Time
                        </label>
                        <input type="time" name="preferred_submit_time"
                               value="{{ old('preferred_submit_time', auth()->user()->userSetting?->preferred_submit_time ?? '17:00') }}"
                               class="rounded-md border-gray-300 shadow-sm">
                        <p class="text-sm text-gray-500 mt-1">
                            Time when auto-submit should run daily
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Notify Before Submit</label>
                            <p class="text-sm text-gray-500">Send browser notification before auto-submit</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_before_submit" value="1"
                                   {{ old('notify_before_submit', auth()->user()->userSetting?->notify_before_submit ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
```

### 8. Alpine Components
**File**: `resources/js/app.js`

```js
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Global Alpine components
document.addEventListener('alpine:init', () => {
    Alpine.data('toggle', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        }
    }));

    Alpine.data('notification', () => ({
        show: false,
        message: '',
        type: 'info',
        
        notify(message, type = 'info') {
            this.message = message;
            this.type = type;
            this.show = true;
            setTimeout(() => this.show = false, 3000);
        }
    }));
});
```

## Files cần tạo
- `tailwind.config.js`
- `resources/css/app.css`
- `resources/views/layouts/app.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/logwork/create.blade.php`
- `resources/views/logwork/edit.blade.php`
- `resources/views/logwork/history.blade.php`
- `resources/views/settings/edit.blade.php`
- `resources/js/app.js`

## Kiểm tra
```bash
# Compile assets
npm run dev

# Check Tailwind classes
npm run build

# Open browser
http://localhost:8000/dashboard
```

## Notes
- **Responsive**: Grid system: `grid-cols-1 md:grid-cols-4`, `col-span-6`, etc.
- **Form validation**: Client-side + Server-side
- **Alpine.js**: Lightweight, no build step cần (có thể dùng CDN nếu muốn)
- **Task repeater**: Dynamic add/remove without page reload
- **CSRF token**: Meta tag in layout, include in AJAX requests
- **Flash messages**: Filament notification component

---

**Status**: ⏳ Pending  
**Priority**: Medium  
**Dependencies**: Task 1 (Tailwind), Task 7 (Routes)  
**Estimated time**: 45 phút
