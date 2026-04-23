# Daily Report Application - Laravel Livewire Complete Implementation Guide

## PART 1: TECHNOLOGY STACK & VERSIONS

### Required Framework Versions
```
Laravel: 11.x (latest)
Livewire: 3.x
PHP: 8.2+
Tailwind CSS: 4.x
Node.js: 20.x+
npm/pnpm: latest
```

### Required PHP Packages
```composer
- laravel/framework: ^11.0
- livewire/livewire: ^3.0
- livewire/volt: ^1.0 (optional, for SFC syntax)
- laravel/tinker: ^2.0
```

### Frontend Dependencies
```json
{
  "devDependencies": {
    "tailwindcss": "^4.0.0",
    "postcss": "^8.4.0",
    "autoprefixer": "^10.4.0",
    "@tailwindcss/forms": "^0.5.0",
    "@tailwindcss/typography": "^0.5.0",
    "alpinejs": "^3.x" (optional, for additional interactivity)
  }
}
```

### Additional Packages (Optional but Recommended)
```
- laravel/breeze or laravel/jetstream (for auth scaffolding)
- laravel/tinker (for REPL)
- barryvdh/laravel-ide-helper (for IDE support)
```

### Directory Structure
```
laravel-app/
├── app/
│   ├── Livewire/
│   │   ├── DailyReport/
│   │   │   ├── Index.php (Full version - page component)
│   │   │   ├── Compact.php (Compact version - page component)
│   │   │   ├── TodayTaskSection.php
│   │   │   ├── TomorrowTaskSection.php
│   │   │   ├── SelfEvaluationSection.php
│   │   │   └── ProjectSelector.php
│   │   └── LogworkHistory/
│   │       └── Index.php
│   ├── Models/
│   │   ├── DailyReport.php
│   │   ├── ReportTask.php
│   │   └── Project.php
│   └── Http/
│       └── Controllers/ (optional, if needed)
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   ├── daily-report/
│   │   │   │   ├── index.blade.php (Full version)
│   │   │   │   ├── compact.blade.php (Compact version)
│   │   │   │   ├── today-task-section.blade.php
│   │   │   │   ├── tomorrow-task-section.blade.php
│   │   │   │   ├── self-evaluation-section.blade.php
│   │   │   │   └── project-selector.blade.php
│   │   │   └── logwork-history/
│   │   │       └── index.blade.php
│   │   ├── layouts/
│   │   │   └── app.blade.php (Main layout)
│   │   └── components/
│   │       └── (Any reusable components)
│   ├── css/
│   │   └── app.css (Tailwind directives)
│   └── js/
│       └── app.js (Bootstrap/entry point)
├── public/
│   └── logos/
│       ├── jrr.jpg
│       ├── primas.jpg
│       └── ... (8 more logos)
├── routes/
│   └── web.php (Route definitions)
├── database/
│   └── migrations/
│       ├── create_daily_reports_table.php
│       ├── create_report_tasks_table.php
│       └── create_projects_table.php
└── tailwind.config.js (Tailwind configuration)
```

---

## PART 2: DATABASE SCHEMA

### Migrations & Models

#### DailyReport Model/Migration
```php
// Migration: create_daily_reports_table
Schema::create('daily_reports', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id'); // if multi-user
    $table->string('project'); // Project name (JRR, Primas, etc)
    $table->date('report_date')->default(now());
    $table->integer('quality_rating'); // 1-5
    $table->integer('spirit_rating'); // 1-5
    $table->text('notes')->nullable();
    $table->boolean('submit_to_gform')->default(true);
    $table->string('gform_response_id')->nullable();
    $table->timestamps();
});

// Model: app/Models/DailyReport.php
class DailyReport extends Model {
    protected $fillable = [
        'user_id',
        'project',
        'report_date',
        'quality_rating',
        'spirit_rating',
        'notes',
        'submit_to_gform',
        'gform_response_id',
    ];

    public function tasks() {
        return $this->hasMany(ReportTask::class);
    }
}
```

#### ReportTask Model/Migration
```php
// Migration: create_report_tasks_table
Schema::create('report_tasks', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('daily_report_id');
    $table->string('description');
    $table->integer('progress'); // 0-100
    $table->date('expected_date')->nullable();
    $table->enum('task_type', ['today', 'tomorrow']); // Distinguish task type
    $table->integer('order')->default(0); // For ordering
    $table->timestamps();
    
    $table->foreign('daily_report_id')
        ->references('id')
        ->on('daily_reports')
        ->onDelete('cascade');
});

// Model: app/Models/ReportTask.php
class ReportTask extends Model {
    protected $fillable = [
        'daily_report_id',
        'description',
        'progress',
        'expected_date',
        'task_type',
        'order',
    ];

    public function dailyReport() {
        return $this->belongsTo(DailyReport::class);
    }
}
```

#### Project Model (Optional, for storing project metadata)
```php
class Project extends Model {
    protected $fillable = ['name', 'logo_path', 'color'];

    public static $projects = [
        'JRR' => '/logos/jrr.jpg',
        'Primas' => '/logos/primas.jpg',
        'Project A' => '/logos/project-a.jpg',
        'Project B' => '/logos/project-b.jpg',
        'Project C' => '/logos/project-c.jpg',
        'Project D' => '/logos/project-d.jpg',
        'Project E' => '/logos/project-e.jpg',
        'Project F' => '/logos/project-f.jpg',
        'Project G' => '/logos/project-g.jpg',
        'Project H' => '/logos/project-h.jpg',
    ];
}
```

---

## PART 3: LIVEWIRE COMPONENT ARCHITECTURE

### Livewire 3 Fundamentals

#### Component Lifecycle
```
1. Boot → 2. Mount → 3. Hydrate → 4. Render → 5. Dehydrate
```

#### Key Directives & Syntax
| Directive | Purpose | Example |
|-----------|---------|---------|
| `wire:model` | Two-way binding | `<input wire:model="taskDescription">` |
| `wire:model.live` | Real-time sync | `<input wire:model.live="progress">` |
| `wire:model.debounce` | Debounced sync | `<input wire:model.debounce-500ms="notes">` |
| `wire:click` | Click handler | `<button wire:click="addTask">Add</button>` |
| `wire:submit` | Form submission | `<form wire:submit="save">` |
| `wire:key` | Key for list items | `<div wire:key="task-{{ $task->id }}">` |
| `@foreach` | Loop with wire:key | `@foreach($tasks as $task)` |
| `wire:loading` | Show during request | `<div wire:loading>Loading...</div>` |
| `wire:target` | Target specific action | `<div wire:loading.target="addTask">` |

#### Component Class Structure
```php
<?php
namespace App\Livewire\DailyReport;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\DailyReport;

class Index extends Component
{
    // 1. PUBLIC PROPERTIES (reactive data)
    public string $selectedProject = 'JRR';
    
    #[Validate('required|string')]
    public string $taskDescription = '';
    
    public array $todayTasks = [];
    public array $tomorrowTasks = [];
    
    public int $qualityRating = 3;
    public int $spiritRating = 3;
    
    #[Validate('nullable|string|max:500')]
    public string $notes = '';
    
    public bool $submitToGForm = true;
    
    // 2. PRIVATE PROPERTIES (internal state)
    private array $projects = [
        'JRR', 'Primas', 'Project A', 'Project B', 'Project C',
        'Project D', 'Project E', 'Project F', 'Project G', 'Project H'
    ];

    // 3. MOUNT - Initialize component
    public function mount()
    {
        $this->initializeTasks();
    }

    // 4. METHODS - Action handlers
    public function addTodayTask()
    {
        $this->todayTasks[] = [
            'id' => uniqid(),
            'description' => '',
            'progress' => 0,
            'expectedDate' => null,
        ];
    }

    public function removeTodayTask($taskId)
    {
        if (count($this->todayTasks) > 1) {
            $this->todayTasks = array_filter(
                $this->todayTasks,
                fn($task) => $task['id'] !== $taskId
            );
        }
    }

    public function updateTodayTask($taskId, $field, $value)
    {
        foreach ($this->todayTasks as &$task) {
            if ($task['id'] === $taskId) {
                $task[$field] = $value;
                break;
            }
        }
    }

    public function save()
    {
        $this->validate();

        // Create daily report
        $report = DailyReport::create([
            'project' => $this->selectedProject,
            'quality_rating' => $this->qualityRating,
            'spirit_rating' => $this->spiritRating,
            'notes' => $this->notes,
            'submit_to_gform' => $this->submitToGForm,
        ]);

        // Create tasks
        foreach ($this->todayTasks as $task) {
            $report->tasks()->create([
                'description' => $task['description'],
                'progress' => $task['progress'],
                'expected_date' => $task['expectedDate'],
                'task_type' => 'today',
            ]);
        }

        // Success notification
        session()->flash('message', 'Daily report submitted!');
        $this->reset();
    }

    // 5. COMPUTED PROPERTIES (calculated values)
    #[Computed]
    public function projectLogos()
    {
        return [
            'JRR' => '/logos/jrr.jpg',
            'Primas' => '/logos/primas.jpg',
            // ... etc
        ];
    }

    // 6. RENDER - Return view
    public function render()
    {
        return view('livewire.daily-report.index', [
            'projects' => $this->projects,
        ]);
    }

    // HELPER METHODS
    private function initializeTasks()
    {
        if (empty($this->todayTasks)) {
            $this->todayTasks = [[
                'id' => uniqid(),
                'description' => '',
                'progress' => 0,
                'expectedDate' => null,
            ]];
        }
        // ... similar for tomorrow tasks
    }
}
```

---

## PART 4: LIVEWIRE VIEW STRUCTURE (Blade Templates)

### Layout: Full Version (resources/views/livewire/daily-report/index.blade.php)

```blade
<div class="min-h-screen bg-slate-50 py-8 px-4">
    <!-- HEADER -->
    <div class="max-w-4xl mx-auto mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-slate-900">Daily Report</h1>
            <a href="{{ route('logwork-history') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">...</svg>
                View History
            </a>
        </div>
    </div>

    <form wire:submit="save" class="max-w-4xl mx-auto space-y-6">
        <!-- PROJECT SECTION -->
        @livewire('daily-report.project-selector', ['selectedProject' => $this->selectedProject])

        <!-- TODAY'S TASKS -->
        @livewire('daily-report.today-task-section', ['tasks' => $this->todayTasks])

        <!-- TOMORROW'S TASKS -->
        @livewire('daily-report.tomorrow-task-section', ['tasks' => $this->tomorrowTasks])

        <!-- SELF-EVALUATION -->
        @livewire('daily-report.self-evaluation-section', [
            'qualityRating' => $this->qualityRating,
            'spiritRating' => $this->spiritRating,
        ])

        <!-- NOTES SECTION -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-xl font-semibold text-slate-900">Ghi chú</h3>
                <p class="text-sm text-slate-500 mt-1">Thêm ghi chú tùy chọn</p>
            </div>
            <div class="px-6 py-4">
                <textarea 
                    wire:model.debounce-500ms="notes"
                    placeholder="Nhập ghi chú (tùy chọn)..."
                    class="w-full min-h-24 px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    rows="3"
                ></textarea>
            </div>
        </div>

        <!-- SUBMIT SECTION -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
            <div class="flex flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-slate-700">
                        Gửi tới Google Form
                    </label>
                    <button 
                        type="button"
                        wire:click="$toggle('submitToGForm')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $submitToGForm ? 'bg-green-500' : 'bg-slate-300' }}"
                    >
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $submitToGForm ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
                <button 
                    type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition-colors"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Gửi Report</span>
                    <span wire:loading>
                        <svg class="inline w-4 h-4 animate-spin" fill="none" stroke="currentColor">...</svg>
                        Đang gửi...
                    </span>
                </button>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-600">{{ session('message') }}</p>
            </div>
        @endif
    </form>
</div>
```

### Component: Today Task Section (resources/views/livewire/daily-report/today-task-section.blade.php)

```blade
<div class="bg-white rounded-lg border border-slate-200 shadow-sm">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-xl font-semibold text-slate-900">Task ngày hôm nay</h3>
        <p class="text-sm text-slate-500 mt-1">Nhập các công việc đã làm hôm nay</p>
    </div>
    
    <div class="px-6 py-4">
        <div class="space-y-2">
            @foreach ($todayTasks as $index => $task)
                <div 
                    wire:key="today-task-{{ $task['id'] }}"
                    class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move"
                >
                    <!-- Drag Handle -->
                    <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors mt-1 sm:mt-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <!-- GripVertical icon -->
                            <path d="M9 3h2v2H9V3zm0 4h2v2H9V7zm0 4h2v2H9v-2zm4-8h2v2h-2V3zm0 4h2v2h-2V7zm0 4h2v2h-2v-2z"/>
                        </svg>
                    </div>

                    <!-- Input: Description -->
                    <input 
                        type="text"
                        wire:model.live="todayTasks.{{ $index }}.description"
                        placeholder="Mô tả..."
                        class="flex-1 h-9 px-3 py-2 text-sm border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />

                    <!-- Select: Progress -->
                    <select 
                        wire:model.live="todayTasks.{{ $index }}.progress"
                        class="w-20 h-9 px-2 py-1 text-sm border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        @foreach ([0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100] as $percent)
                            <option value="{{ $percent }}">{{ $percent }}%</option>
                        @endforeach
                    </select>

                    <!-- Input: Expected Date (show only if progress < 100) -->
                    @if ($task['progress'] < 100)
                        <input 
                            type="date"
                            wire:model.live="todayTasks.{{ $index }}.expectedDate"
                            class="w-32 h-9 px-3 py-2 text-sm border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    @endif

                    <!-- Button: Delete -->
                    @if (count($todayTasks) > 1)
                        <button 
                            type="button"
                            wire:click="removeTodayTask('{{ $task['id'] }}')"
                            class="h-9 w-9 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 rounded-md flex items-center justify-center"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <!-- Trash icon -->
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Button: Add Task -->
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
```

### Compact Version Layout (resources/views/livewire/daily-report/compact.blade.php)

```blade
<div class="min-h-screen bg-slate-50 py-4 px-3">
    <div class="max-w-3xl mx-auto mb-3">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900">Daily Report</h1>
            <a href="{{ route('logwork-history') }}" class="text-slate-600 hover:text-slate-900">
                <svg class="w-4 h-4" />
            </a>
        </div>
    </div>

    <form wire:submit="save" class="max-w-3xl mx-auto space-y-3">
        <!-- All sections same as full, but with compact spacing -->
        <!-- px-3 py-3 instead of px-6 py-4 -->
        <!-- space-y-3 instead of space-y-6 -->
        <!-- text-xs instead of text-sm -->
        <!-- h-8 instead of h-9 -->
        <!-- p-1.5 instead of p-2 -->
    </form>
</div>
```

---

## PART 5: TAILWIND CSS CONFIGURATION FOR LARAVEL

### tailwind.config.js
```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        slate: {
          50: '#f8fafc',
          100: '#f1f5f9',
          200: '#e2e8f0',
          300: '#cbd5e1',
          400: '#a0aec0',
          500: '#64748b',
          600: '#475569',
          700: '#334155',
          800: '#1e293b',
          900: '#0f172a',
        },
      },
      spacing: {
        'sm': '0.5rem',
        'md': '1rem',
        'lg': '1.5rem',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
```

### app.css (Tailwind Directives)
```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom component utilities */
@layer components {
  .card-header {
    @apply px-6 py-4 border-b border-slate-200;
  }

  .card-compact-header {
    @apply px-3 py-3 border-b border-slate-200;
  }

  .input-base {
    @apply px-3 py-2 text-sm border border-slate-200 rounded-md;
    @apply focus:outline-none focus:ring-2 focus:ring-blue-500;
    @apply transition-colors duration-200;
  }

  .button-primary {
    @apply px-6 py-3 bg-blue-600 text-white rounded-md font-medium;
    @apply hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
    @apply transition-colors duration-150;
  }

  .button-secondary {
    @apply px-4 py-2 bg-slate-100 text-slate-700 rounded-md;
    @apply hover:bg-slate-200 focus:ring-2 focus:ring-slate-400;
    @apply transition-colors duration-200;
  }

  .task-row {
    @apply flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center;
    @apply p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move;
  }

  .task-row-compact {
    @apply flex flex-col sm:flex-row gap-1.5 sm:gap-2 items-start sm:items-center;
    @apply p-1.5 hover:bg-slate-50 rounded-md transition-colors group cursor-move;
  }

  .drag-handle {
    @apply flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors;
  }
}
```

---

## PART 6: LIVEWIRE-SPECIFIC PATTERNS

### Two-Way Binding Patterns

#### Simple Property Binding
```blade
<input type="text" wire:model="taskDescription">
<!-- Updates property in real-time -->
```

#### Array/Collection Binding
```blade
@foreach ($todayTasks as $index => $task)
    <input wire:model.live="todayTasks.{{ $index }}.description">
    <select wire:model.live="todayTasks.{{ $index }}.progress">
@endforeach
```

#### Debounced Binding (for textarea, slow APIs)
```blade
<textarea wire:model.debounce-500ms="notes"></textarea>
<!-- Updates after 500ms of inactivity -->
```

#### Lazy Binding (on blur)
```blade
<input wire:model.lazy="qualityRating" type="number">
<!-- Only updates when field loses focus -->
```

### Event Handling Patterns

#### Method Calls with Parameters
```blade
<button wire:click="removeTodayTask('{{ $task['id'] }}')">Delete</button>
```

#### Toggle/Computed Actions
```blade
<button wire:click="$toggle('submitToGForm')">Toggle</button>
```

#### Form Submission
```blade
<form wire:submit="save">
    <!-- form content -->
    <button type="submit">Submit</button>
</form>
```

#### Loading States
```blade
<button wire:loading.attr="disabled">
    <span wire:loading.remove>Save</span>
    <span wire:loading>Saving...</span>
</button>

<div wire:loading wire:target="save">
    Processing...
</div>
```

### List Rendering with wire:key

```blade
@foreach ($todayTasks as $index => $task)
    <div wire:key="task-{{ $task['id'] }}">
        <!-- Item content -->
    </div>
@endforeach
```
- **Important:** Always use `wire:key` on dynamic lists for proper reactivity
- Use unique IDs (not array indexes) to maintain form state

### Form Validation in Livewire

```php
// In component class
use Livewire\Attributes\Validate;

#[Validate('required|string|max:255')]
public string $taskDescription = '';

#[Validate('required|integer|min:0|max:100')]
public int $progress = 0;

// In blade
@error('taskDescription')
    <span class="text-red-600 text-sm">{{ $message }}</span>
@enderror
```

---

## PART 7: ROUTING CONFIGURATION

### routes/web.php
```php
<?php

use App\Livewire\DailyReport\Index as DailyReportIndex;
use App\Livewire\DailyReport\Compact as DailyReportCompact;
use App\Livewire\LogworkHistory\Index as LogworkHistoryIndex;

Route::middleware(['web'])->group(function () {
    // Full version
    Route::get('/', DailyReportIndex::class)->name('daily-report');

    // Compact version
    Route::get('/compact', DailyReportCompact::class)->name('daily-report.compact');

    // History
    Route::get('/logwork-history', LogworkHistoryIndex::class)->name('logwork-history');
});
```

---

## PART 8: TAILWIND CLASSES QUICK REFERENCE

### Spacing & Layout
```
p-2   = padding: 0.5rem (8px)
p-3   = padding: 0.75rem (12px)
p-4   = padding: 1rem (16px)
px-3  = padding-left/right: 0.75rem
py-4  = padding-top/bottom: 1rem
gap-2 = gap: 0.5rem
space-y-2 = margin-bottom: 0.5rem (on children except last)
space-y-6 = margin-bottom: 1.5rem
```

### Typography
```
text-sm   = font-size: 0.875rem (14px)
text-xs   = font-size: 0.75rem (12px)
text-base = font-size: 1rem (16px)
text-lg   = font-size: 1.125rem (18px)
text-xl   = font-size: 1.25rem (20px)
text-2xl  = font-size: 1.5rem (24px)
text-3xl  = font-size: 1.875rem (30px)

font-medium = font-weight: 500
font-semibold = font-weight: 600
font-bold = font-weight: 700
```

### Colors
```
bg-slate-50   = background: #f8fafc
bg-slate-100  = background: #f1f5f9
bg-white      = background: #ffffff
text-slate-600 = color: #475569
text-slate-900 = color: #0f172a
border-slate-200 = border-color: #e2e8f0
```

### Sizing
```
h-8   = height: 2rem
h-9   = height: 2.25rem
h-10  = height: 2.5rem
w-20  = width: 5rem
w-32  = width: 8rem
min-h-24 = min-height: 6rem
flex-1 = flex: 1 (grow equally)
```

### Responsive
```
sm:  = @media (min-width: 640px)
md:  = @media (min-width: 768px)
lg:  = @media (min-width: 1024px)
flex-col sm:flex-row = column on mobile, row on tablet+
```

### States
```
hover:  = on mouse hover
focus:  = on focus
disabled: = when disabled
opacity-0 / opacity-100 = 0% / 100% opacity
ring-2 ring-blue-500 = focus outline
transition-all duration-200 = smooth animation
```

---

## PART 9: COMPONENT BREAKDOWN (Hierarchical)

### Level 1: Page Component (App-level)
```
DailyReport\Index (Full)
└── ProjectSelector (child component)
└── TodayTaskSection (child component)
└── TomorrowTaskSection (child component)
└── SelfEvaluationSection (child component)
```

### Level 2: Section Components
Each section is a separate Livewire component for modularity:
- `ProjectSelector.php` - Project dropdown + logo
- `TodayTaskSection.php` - Today's task repeater
- `TomorrowTaskSection.php` - Tomorrow's task repeater
- `SelfEvaluationSection.php` - Rating buttons (quality + spirit)

### Level 3: Reusable Sub-components (Optional)
```
TaskRow.php - Single task row (can be reused)
RatingButtons.php - Reusable rating widget
TextInput.php - Wrapper around input with validation
```

### Level 4: HTML/Blade Elements
Individual inputs, buttons, selects

---

## PART 10: STATE MANAGEMENT IN LIVEWIRE VS REACT

### Livewire (PHP Classes)
```php
class Index extends Component {
    public string $selectedProject = 'JRR'; // Reactive property
    public array $todayTasks = []; // Array binding
    
    public function addTask() { // Action method
        $this->todayTasks[] = [...];
    }
    
    public function save() { // Submit handler
        $this->validate();
        DailyReport::create([...]);
    }
}
```

### React (JavaScript)
```typescript
const [selectedProject, setSelectedProject] = useState('JRR');
const [todayTasks, setTodayTasks] = useState([]);

const addTask = () => {
    setTodayTasks([...todayTasks, {...}]);
};

const save = async () => {
    // Fetch API call
};
```

### Key Differences
| Aspect | Livewire | React |
|--------|----------|-------|
| State | PHP properties | useState hooks |
| Binding | `wire:model` | controlled inputs |
| Events | `wire:click` | onClick handlers |
| Validation | Server-side (#[Validate]) | Client/Server |
| Rendering | Full page/partial re-render | Component re-render |
| API | HTTP (AJAX) | Fetch/Axios |

---

## PART 11: IMPLEMENTATION WORKFLOW FOR LARAVEL

### Step 1: Setup
```bash
# Create Laravel app
laravel new daily-report

# Install Livewire
composer require livewire/livewire

# Install Tailwind
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

### Step 2: Create Database
```bash
# Create migrations
php artisan make:migration create_daily_reports_table
php artisan make:migration create_report_tasks_table

# Run migrations
php artisan migrate
```

### Step 3: Create Models
```bash
php artisan make:model DailyReport -m
php artisan make:model ReportTask -m
```

### Step 4: Create Livewire Components
```bash
php artisan make:livewire daily-report.index
php artisan make:livewire daily-report.today-task-section
php artisan make:livewire daily-report.tomorrow-task-section
php artisan make:livewire daily-report.self-evaluation-section
```

### Step 5: Create Views
Create `.blade.php` files in `resources/views/livewire/daily-report/`

### Step 6: Setup Routes
Edit `routes/web.php` with Livewire route bindings

### Step 7: Add Styling
Configure `tailwind.config.js` and import Tailwind in `app.css`

### Step 8: Development
```bash
npm run dev  # Start Tailwind watcher
php artisan serve  # Start Laravel
```

---

## PART 12: DEPLOYMENT CHECKLIST

- [ ] All PHP dependencies installed (`composer install --no-dev`)
- [ ] All Node dependencies installed (`npm ci`)
- [ ] Frontend assets built (`npm run build`)
- [ ] Environment variables configured (`.env`)
- [ ] Database migrations run (`php artisan migrate`)
- [ ] Livewire caching configured (if needed)
- [ ] Tailwind CSS purged for production
- [ ] Assets cached correctly
- [ ] Error logging configured
- [ ] Security headers set (CSRF, etc)

---

## END OF DOCUMENT

Use this prompt for any Laravel Livewire + Tailwind implementation. All specifications match the original Next.js design exactly for visual consistency across platforms.
