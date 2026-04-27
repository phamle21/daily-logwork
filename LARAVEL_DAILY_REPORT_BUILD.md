# Laravel Livewire Daily Report - Complete Build Guide

## 1. QUICK START

### Installation (5 phút)
```bash
composer create-project laravel/laravel daily-report
cd daily-report
composer require livewire/livewire
composer require tailwindcss/tailwindcss
npm install
php artisan migrate
php artisan serve
```

### Packages Required
```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "livewire/livewire": "^3.0"
  },
  "require-dev": {
    "tailwindcss": "^4.0"
  }
}
```

---

## 2. PROJECT STRUCTURE

```
daily-report/
├── app/
│   ├── Http/
│   │   └── Livewire/
│   │       ├── DailyReportPage.php
│   │       └── Components/
│   │           ├── TaskSection.php
│   │           ├── ProjectSelect.php
│   │           └── SelfEvaluation.php
│   ├── Models/
│   │   ├── Project.php
│   │   ├── DailyReport.php
│   │   └── ReportTask.php
│   └── Http/Controllers/
│       └── DailyReportController.php
├── database/
│   └── migrations/
│       ├── 2024_01_01_create_projects_table.php
│       ├── 2024_01_01_create_daily_reports_table.php
│       └── 2024_01_01_create_report_tasks_table.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── livewire/
│   │   │   ├── daily-report-page.blade.php
│   │   │   └── components/
│   │   │       ├── task-section.blade.php
│   │   │       ├── project-select.blade.php
│   │   │       └── self-evaluation.blade.php
│   │   └── components/
│   │       ├── card.blade.php
│   │       ├── input.blade.php
│   │       ├── button.blade.php
│   │       └── select.blade.php
│   └── css/
│       └── app.css
├── public/
│   └── logos/
│       ├── jrr.jpg
│       ├── primas.jpg
│       ├── project-a.jpg
│       ... (8 more logos)
└── routes/
    └── web.php
```

---

## 3. COLOR & DESIGN SYSTEM

### CSS Variables (in `resources/css/app.css`)
```css
@layer base {
  :root {
    /* Backgrounds */
    --bg-page: #F8FAFC;
    --bg-card: #FFFFFF;
    --bg-input: #FFFFFF;
    --bg-hover: #F8FAFC;
    --bg-hover-subtle: #F1F5F9;
    
    /* Text Colors */
    --text-primary: #0F172A;
    --text-secondary: #475569;
    --text-tertiary: #64748B;
    --text-disabled: #A0AEC0;
    
    /* Borders & UI */
    --border-color: #E2E8F0;
    --border-color-hover: #CBD5E1;
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
    
    /* Semantic Colors */
    --color-primary: #2563EB;
    --color-primary-hover: #1D4ED8;
    --color-danger: #DC2626;
    --color-danger-hover: #B91C1C;
    --color-success: #16A34A;
  }
}

@layer components {
  .card {
    @apply bg-white border border-slate-200 rounded-lg shadow-sm;
  }
  
  .card-header {
    @apply px-6 py-4 border-b border-slate-200;
  }
  
  .card-title {
    @apply text-xl font-semibold text-slate-900;
  }
  
  .card-description {
    @apply text-sm text-slate-500 mt-1;
  }
  
  .card-content {
    @apply px-6 py-4;
  }
  
  .input-base {
    @apply w-full h-9 px-3 py-2 text-sm rounded-md;
    @apply border border-slate-200 bg-white text-slate-900;
    @apply placeholder:text-slate-400 placeholder:italic;
    @apply focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none;
    @apply transition-all duration-200;
    @apply disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed;
  }
  
  .select-base {
    @apply w-full h-9 px-3 py-2 text-sm rounded-md;
    @apply border border-slate-200 bg-white text-slate-900;
    @apply focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none;
    @apply transition-all duration-200;
  }
  
  .button-primary {
    @apply px-6 py-3 bg-blue-600 text-white rounded-md font-medium;
    @apply hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
    @apply transition-all duration-150;
  }
  
  .button-secondary {
    @apply px-4 py-2 bg-slate-100 text-slate-700 rounded-md font-medium border border-slate-200;
    @apply hover:bg-slate-200 focus:ring-2 focus:ring-slate-400 focus:ring-offset-2;
    @apply transition-all duration-150;
  }
  
  .button-ghost {
    @apply px-3 py-2 text-slate-700 rounded-md;
    @apply hover:bg-slate-100;
    @apply transition-colors duration-200;
  }
  
  .button-danger {
    @apply px-3 py-2 text-red-600 rounded-md;
    @apply hover:text-red-700 hover:bg-red-50;
    @apply transition-colors duration-200;
  }
  
  .textarea-base {
    @apply w-full px-3 py-2 text-sm rounded-md;
    @apply border border-slate-200 bg-white text-slate-900;
    @apply placeholder:text-slate-400 placeholder:italic;
    @apply focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none;
    @apply transition-all duration-200;
    @apply min-h-24;
  }
  
  .rating-button {
    @apply flex-1 h-10 rounded-md font-medium;
    @apply transition-all duration-150;
  }
  
  .rating-button-unselected {
    @apply bg-white border border-slate-200 text-slate-700;
    @apply hover:border-slate-300;
  }
  
  .rating-button-selected {
    @apply border-0;
  }
}
```

### Tailwind Configuration
```javascript
// tailwind.config.js
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./app/View/Components/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        slate: {
          50: '#F8FAFC',
          100: '#F1F5F9',
          200: '#E2E8F0',
          300: '#CBD5E1',
          400: '#A0AEC0',
          500: '#64748B',
          600: '#475569',
          700: '#334155',
          800: '#1E293B',
          900: '#0F172A',
        }
      },
      spacing: {
        'card-px': '1.5rem',
        'card-py': '1rem',
      },
      fontSize: {
        xs: ['0.75rem', { lineHeight: '1rem' }],
        sm: ['0.875rem', { lineHeight: '1.25rem' }],
        base: ['1rem', { lineHeight: '1.5rem' }],
        lg: ['1.125rem', { lineHeight: '1.75rem' }],
        xl: ['1.25rem', { lineHeight: '1.75rem' }],
        '2xl': ['1.5rem', { lineHeight: '2rem' }],
        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
      }
    }
  },
  plugins: [],
}
```

---

## 4. TYPOGRAPHY SCALE

### Heading Styles
```html
<!-- Page Title -->
<h1 class="text-3xl font-bold text-slate-900 leading-tight">Daily Report</h1>

<!-- Card Header Title -->
<h2 class="text-xl font-semibold text-slate-900">Chọn dự án</h2>

<!-- Card Description -->
<p class="text-sm text-slate-500 mt-1">Chọn dự án bạn muốn report</p>

<!-- Field Label -->
<label class="text-sm font-medium text-slate-700 block mb-2">Mô tả công việc</label>

<!-- Body Text (default) -->
<p class="text-sm text-slate-900">...</p>

<!-- Helper/Placeholder -->
<p class="text-sm text-slate-400 italic">Nhập ghi chú (tùy chọn)...</p>

<!-- Rating Label -->
<span class="text-sm font-medium text-slate-600">Rất tốt</span>
```

---

## 5. SPACING & LAYOUT REFERENCE

### Container & Cards
```html
<!-- Main Container -->
<div class="max-w-4xl mx-auto px-4 py-8 bg-slate-50 min-h-screen">
  <!-- Page content -->
</div>

<!-- Card Structure -->
<div class="bg-white border border-slate-200 rounded-lg shadow-sm">
  <!-- Header -->
  <div class="px-6 py-4 border-b border-slate-200">
    <h3 class="text-xl font-semibold text-slate-900">Title</h3>
    <p class="text-sm text-slate-500 mt-1">Description</p>
  </div>
  
  <!-- Content -->
  <div class="px-6 py-4">
    <!-- Card body -->
  </div>
</div>

<!-- Spacing Between Cards -->
<div class="space-y-6">
  <!-- Cards here - gap 1.5rem -->
</div>
```

### Form Spacing
```html
<!-- Form Layout -->
<form class="space-y-6">
  <!-- Each section 1.5rem apart -->
  
  <!-- Input with Label -->
  <div class="space-y-2">
    <label class="text-sm font-medium text-slate-700">Label</label>
    <input class="input-base" />
  </div>
  
  <!-- Task List Spacing -->
  <div class="space-y-2">
    <!-- Task rows 0.5rem apart -->
    <div class="p-2 hover:bg-slate-50 rounded-md">Task row</div>
  </div>
  
  <!-- Between major sections -->
  <div class="mt-6">Next section</div>
</form>
```

### Task Row Structure
```html
<!-- Task Row Layout -->
<div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
  <!-- Mobile: column stack (gap-2) -->
  <!-- Desktop: row layout (gap-3) -->
  <!-- Padding: p-2 (0.5rem all sides) -->
  <!-- Hover: bg-slate-50 -->
  
  <!-- Drag Handle - flex-shrink-0 width-4 height-4 -->
  <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
    <!-- GripVertical icon -->
  </div>
  
  <!-- Input - flex-1 for flexible width -->
  <input class="flex-1 h-9 text-sm input-base" />
  
  <!-- Select - fixed width w-20 (5rem) -->
  <select class="w-20 h-9 text-sm select-base" />
  
  <!-- Date Input (conditional) - width w-32 (8rem) -->
  <input type="date" class="w-32 h-9 text-sm input-base" />
  
  <!-- Delete Button (hover reveal) - flex-shrink-0 -->
  <button class="opacity-0 group-hover:opacity-100 transition-opacity">
    <!-- Trash icon -->
  </button>
</div>
```

---

## 6. COMPONENT SPECIFICATIONS

### Project Selection Card
```html
<div class="bg-white border border-slate-200 rounded-lg shadow-sm">
  <div class="px-6 py-4 border-b border-slate-200">
    <h3 class="text-xl font-semibold text-slate-900">Chọn dự án</h3>
  </div>
  
  <div class="px-6 py-4">
    <div class="flex items-center gap-4">
      <!-- Logo: 64x64px -->
      <img src="logo.jpg" class="w-16 h-16 object-cover rounded-lg" />
      
      <!-- Select: flex-1 -->
      <select class="flex-1 h-9 text-sm select-base">
        <option>JRR</option>
        <option>Primas</option>
        <!-- ... more options -->
      </select>
    </div>
  </div>
</div>
```

### Task Section Header
```html
<div class="bg-white border border-slate-200 rounded-lg shadow-sm">
  <div class="px-6 py-4 border-b border-slate-200">
    <h3 class="text-xl font-semibold text-slate-900">Task ngày hôm nay</h3>
    <p class="text-sm text-slate-500 mt-1">Danh sách các công việc hoàn thành hoặc đang tiến hành</p>
  </div>
  
  <div class="px-6 py-4">
    <!-- Task list here -->
  </div>
</div>
```

### Button Styles

#### Primary Button (Submit)
```html
<button class="px-6 py-3 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition-all duration-150">
  Gửi Report
</button>
```

#### Secondary Button (Add Task)
```html
<button class="w-full py-2 px-4 bg-slate-100 text-slate-700 border border-slate-200 rounded-md hover:bg-slate-200 transition-all duration-150">
  + Thêm công việc
</button>
```

#### Icon Button (Delete)
```html
<button class="h-9 w-9 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity">
  <!-- Trash icon 4x4 -->
</button>
```

### Rating Buttons (1-5)
```html
<div class="flex gap-2">
  <button class="flex-1 h-10 rounded-md font-medium border border-slate-200 text-slate-700 hover:border-slate-300">
    1
  </button>
  <button class="flex-1 h-10 rounded-md font-medium bg-blue-600 text-white border-0">
    3
  </button>
  <!-- ... 4, 5 -->
</div>
```

### Input Field Examples

#### Text Input
```html
<input 
  type="text" 
  placeholder="Mô tả..."
  class="w-full h-9 px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all duration-200"
/>
```

#### Select Dropdown
```html
<select class="w-20 h-9 px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none">
  <option>0%</option>
  <option>10%</option>
  <!-- ... -->
  <option>100%</option>
</select>
```

#### Date Input
```html
<input 
  type="date" 
  class="w-32 h-9 px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none"
/>
```

#### Textarea
```html
<textarea 
  rows="3"
  placeholder="Nhập ghi chú (tùy chọn)..."
  class="w-full px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none min-h-24 transition-all duration-200"
></textarea>
```

---

## 7. PAGE LAYOUT - WIREFRAME

```
┌─────────────────────────────────────────┐
│  Daily Report              Lịch sử      │  Header + Navigation
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ Chọn dự án                              │
├─────────────────────────────────────────┤
│ [Logo] [JRR ▼                      ]    │  Project selection
│ 64x64   flex-1                          │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ Task ngày hôm nay                       │
│ Danh sách các công việc...              │
├─────────────────────────────────────────┤
│ ≡ [Mô tả...] [50%] [2024-01-15] [✕]   │
│ ≡ [Mô tả...] [100%]                    │
│ ≡ [Mô tả...] [30%] [2024-01-15] [✕]   │
│                                         │
│ [+ Thêm]                                │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ Task ngày mai                           │
│ Lên kế hoạch cho ngày mai              │
├─────────────────────────────────────────┤
│ ≡ [Mô tả...            ] [✕]           │
│ ≡ [Mô tả...            ] [✕]           │
│                                         │
│ [+ Thêm]                                │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ Tự đánh giá                             │
│ Đánh giá chất lượng và tinh thần       │
├─────────────────────────────────────────┤
│                                         │
│ Chất lượng công việc: Bình thường       │
│ [1] [2] [3] [4] [5]                    │
│                                         │
│ Tinh thần làm việc: Tốt                 │
│ [1] [2] [3] [4] [5]                    │
│                                         │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ Ghi chú                                 │
│ Thêm ghi chú tùy chọn                  │
├─────────────────────────────────────────┤
│ [Textarea rows-3                     ] │
│ [                                    ] │
│ [                                    ] │
└─────────────────────────────────────────┘

Gửi tới Google Form ⊙  
┌──────────────────────┐
│  Gửi Report          │  Primary button
└──────────────────────┘

Space-y-6 (1.5rem) between all major sections
```

---

## 8. RESPONSIVE BEHAVIOR

### Mobile (< 640px)
```tailwind
flex flex-col           /* Stack vertically */
gap-2                   /* Reduced gap */
w-full                  /* Full width */
px-4 py-8              /* Reduced padding */
text-sm                /* Smaller text */
h-9                    /* Standard input height */
```

### Tablet (640px - 1024px)
```tailwind
sm:flex-row             /* Row layout starts here */
sm:gap-3               /* Increased gap */
sm:items-center        /* Center items vertically */
sm:px-6 sm:py-4       /* Increased padding */
```

### Desktop (> 1024px)
```tailwind
/* Full layout applied */
max-w-4xl             /* Max width container */
px-6 py-4             /* Full padding */
gap-3                 /* Full gap */
flex-row              /* Row layout */
```

---

## 9. LIVEWIRE COMPONENT CLASS

```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\DailyReport;
use App\Models\Project;

class DailyReportPage extends Component
{
    // Projects
    public $projects;
    #[Validate('required|exists:projects,id')]
    public $selectedProjectId;
    
    // Today's Tasks
    public $todayTasks = [];
    
    // Tomorrow's Tasks
    public $tomorrowTasks = [];
    
    // Ratings
    #[Validate('required|integer|min:1|max:5')]
    public $qualityRating = 3;
    
    #[Validate('required|integer|min:1|max:5')]
    public $spiritRating = 3;
    
    // Notes
    #[Validate('nullable|string|max:1000')]
    public $notes = '';
    
    // Google Form
    public $submitToGForm = true;

    public function mount()
    {
        $this->projects = Project::all();
        $this->selectedProjectId = $this->projects->first()->id ?? null;
        
        // Initialize with one task
        $this->todayTasks = [
            ['id' => uniqid(), 'description' => '', 'progress' => 0, 'expectedDate' => null]
        ];
        
        $this->tomorrowTasks = [
            ['id' => uniqid(), 'description' => '']
        ];
    }

    // Today's Tasks Methods
    public function addTodayTask()
    {
        $this->todayTasks[] = [
            'id' => uniqid(),
            'description' => '',
            'progress' => 0,
            'expectedDate' => null
        ];
    }

    public function removeTodayTask($id)
    {
        if (count($this->todayTasks) > 1) {
            $this->todayTasks = array_filter(
                $this->todayTasks,
                fn($task) => $task['id'] !== $id
            );
            // Reindex array
            $this->todayTasks = array_values($this->todayTasks);
        }
    }

    public function updateTodayTask($id, $field, $value)
    {
        $this->todayTasks = array_map(
            fn($task) => $task['id'] === $id ? [...$task, $field => $value] : $task,
            $this->todayTasks
        );
    }

    // Tomorrow's Tasks Methods
    public function addTomorrowTask()
    {
        $this->tomorrowTasks[] = [
            'id' => uniqid(),
            'description' => ''
        ];
    }

    public function removeTomorrowTask($id)
    {
        if (count($this->tomorrowTasks) > 1) {
            $this->tomorrowTasks = array_filter(
                $this->tomorrowTasks,
                fn($task) => $task['id'] !== $id
            );
            $this->tomorrowTasks = array_values($this->tomorrowTasks);
        }
    }

    public function updateTomorrowTask($id, $value)
    {
        $this->tomorrowTasks = array_map(
            fn($task) => $task['id'] === $id ? [...$task, 'description' => $value] : $task,
            $this->tomorrowTasks
        );
    }

    // Form Methods
    public function submitReport()
    {
        $this->validate();

        // Filter empty tasks
        $todayTasks = array_filter(
            $this->todayTasks,
            fn($task) => !empty($task['description'])
        );

        if (empty($todayTasks)) {
            $this->addError('todayTasks', 'Cần ít nhất 1 công việc hôm nay');
            return;
        }

        // Save to database
        $report = DailyReport::create([
            'project_id' => $this->selectedProjectId,
            'quality_rating' => $this->qualityRating,
            'spirit_rating' => $this->spiritRating,
            'notes' => $this->notes,
            'submitted_at' => now(),
        ]);

        // Save tasks
        foreach ($todayTasks as $task) {
            $report->tasks()->create([
                'type' => 'today',
                'description' => $task['description'],
                'progress' => $task['progress'],
                'expected_date' => $task['expectedDate'] ?? null,
            ]);
        }

        // Tomorrow's tasks
        $tomorrowTasks = array_filter(
            $this->tomorrowTasks,
            fn($task) => !empty($task['description'])
        );

        foreach ($tomorrowTasks as $task) {
            $report->tasks()->create([
                'type' => 'tomorrow',
                'description' => $task['description'],
            ]);
        }

        // Submit to Google Form if enabled
        if ($this->submitToGForm) {
            $this->submitToGoogleForm($report);
        }

        // Show success message (SweetAlert2)
        $this->dispatch('swal', [
            'title' => 'Thành công!',
            'text' => 'Report đã được gửi',
            'icon' => 'success'
        ]);

        // Reset form
        $this->reset(['todayTasks', 'tomorrowTasks', 'notes', 'qualityRating', 'spiritRating']);
    }

    private function submitToGoogleForm($report)
    {
        // Implementation for Google Form submission
        // Use guzzle or similar to send POST request
    }

    public function render()
    {
        return view('livewire.daily-report-page');
    }
}
```

---

## 10. BLADE TEMPLATE

```blade
<div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-slate-900">Daily Report</h1>
            <a href="/logwork-history" class="flex items-center gap-2 text-blue-600 hover:text-blue-700">
                <x-lucide-history class="w-5 h-5" />
                Lịch sử
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        <form wire:submit="submitReport" class="space-y-6">
            <!-- Project Selection -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Chọn dự án</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center gap-4">
                        <img 
                            src="{{ asset('logos/' . $selectedProject . '.jpg') }}" 
                            alt="Project Logo"
                            class="w-16 h-16 object-cover rounded-lg"
                        />
                        <select 
                            wire:model.live="selectedProjectId"
                            class="flex-1 h-9 px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Today's Tasks -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Task ngày hôm nay</h2>
                    <p class="text-sm text-slate-500 mt-1">Danh sách các công việc hoàn thành hoặc đang tiến hành</p>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-2">
                        @foreach($todayTasks as $index => $task)
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
                                <!-- Drag Handle -->
                                <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                                    <x-lucide-grip-vertical class="w-4 h-4" />
                                </div>

                                <!-- Description Input -->
                                <input 
                                    type="text"
                                    placeholder="Mô tả..."
                                    wire:model.defer="todayTasks.{{ $index }}.description"
                                    class="flex-1 h-9 px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all duration-200"
                                />

                                <!-- Progress Select -->
                                <select 
                                    wire:model.defer="todayTasks.{{ $index }}.progress"
                                    class="w-20 h-9 px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                                    @for($i = 0; $i <= 100; $i += 10)
                                        <option value="{{ $i }}">{{ $i }}%</option>
                                    @endfor
                                </select>

                                <!-- Expected Date (if progress < 100) -->
                                @if($task['progress'] < 100)
                                    <input 
                                        type="date"
                                        wire:model.defer="todayTasks.{{ $index }}.expectedDate"
                                        class="w-32 h-9 px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    />
                                @endif

                                <!-- Delete Button -->
                                @if(count($todayTasks) > 1)
                                    <button 
                                        type="button"
                                        wire:click="removeTodayTask('{{ $task['id'] }}')"
                                        class="h-9 w-9 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 rounded-md"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4 mx-auto" />
                                    </button>
                                @endif
                            </div>
                        @endforeach

                        <!-- Add Button -->
                        <button 
                            type="button"
                            wire:click="addTodayTask"
                            class="w-full mt-2 py-2 px-4 bg-slate-100 text-slate-700 border border-slate-200 rounded-md hover:bg-slate-200 transition-all duration-150 text-sm font-medium"
                        >
                            + Thêm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tomorrow's Tasks -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Task ngày mai</h2>
                    <p class="text-sm text-slate-500 mt-1">Lên kế hoạch cho ngày mai</p>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-2">
                        @foreach($tomorrowTasks as $index => $task)
                            <div class="flex gap-2 items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
                                <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                                    <x-lucide-grip-vertical class="w-4 h-4" />
                                </div>

                                <input 
                                    type="text"
                                    placeholder="Mô tả..."
                                    wire:model.defer="tomorrowTasks.{{ $index }}.description"
                                    class="flex-1 h-9 px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all duration-200"
                                />

                                @if(count($tomorrowTasks) > 1)
                                    <button 
                                        type="button"
                                        wire:click="removeTomorrowTask('{{ $task['id'] }}')"
                                        class="h-9 w-9 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 rounded-md"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4 mx-auto" />
                                    </button>
                                @endif
                            </div>
                        @endforeach

                        <button 
                            type="button"
                            wire:click="addTomorrowTask"
                            class="w-full mt-2 py-2 px-4 bg-slate-100 text-slate-700 border border-slate-200 rounded-md hover:bg-slate-200 transition-all duration-150 text-sm font-medium"
                        >
                            + Thêm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Self Evaluation -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Tự đánh giá</h2>
                    <p class="text-sm text-slate-500 mt-1">Đánh giá chất lượng công việc và tinh thần làm việc</p>
                </div>
                <div class="px-6 py-4 space-y-8">
                    <!-- Quality Rating -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="text-sm font-semibold text-slate-900">Chất lượng công việc</label>
                            <span class="text-sm font-medium text-slate-600">
                                {{ ['', 'Rất kém', 'Kém', 'Bình thường', 'Tốt', 'Rất tốt'][$qualityRating] }}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button 
                                    type="button"
                                    wire:click="$set('qualityRating', {{ $i }})"
                                    class="flex-1 h-10 rounded-md font-medium transition-all duration-150
                                        @if($qualityRating === $i)
                                            @if($i == 1) bg-red-500 text-white
                                            @elseif($i == 2) bg-orange-500 text-white
                                            @elseif($i == 3) bg-yellow-500 text-white
                                            @elseif($i == 4) bg-green-500 text-white
                                            @else bg-emerald-500 text-white
                                            @endif
                                        @else
                                            bg-white border border-slate-200 text-slate-700 hover:border-slate-300
                                        @endif
                                    "
                                >
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                    </div>

                    <!-- Spirit Rating -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="text-sm font-semibold text-slate-900">Tinh thần làm việc</label>
                            <span class="text-sm font-medium text-slate-600">
                                {{ ['', 'Rất kém', 'Kém', 'Bình thường', 'Tốt', 'Rất tốt'][$spiritRating] }}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button 
                                    type="button"
                                    wire:click="$set('spiritRating', {{ $i }})"
                                    class="flex-1 h-10 rounded-md font-medium transition-all duration-150
                                        @if($spiritRating === $i)
                                            @if($i == 1) bg-red-500 text-white
                                            @elseif($i == 2) bg-orange-500 text-white
                                            @elseif($i == 3) bg-yellow-500 text-white
                                            @elseif($i == 4) bg-green-500 text-white
                                            @else bg-emerald-500 text-white
                                            @endif
                                        @else
                                            bg-white border border-slate-200 text-slate-700 hover:border-slate-300
                                        @endif
                                    "
                                >
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Ghi chú</h2>
                    <p class="text-sm text-slate-500 mt-1">Thêm ghi chú tùy chọn</p>
                </div>
                <div class="px-6 py-4">
                    <textarea 
                        rows="3"
                        placeholder="Nhập ghi chú (tùy chọn)..."
                        wire:model.defer="notes"
                        class="w-full px-3 py-2 text-sm rounded-md border border-slate-200 bg-white text-slate-900 placeholder:text-slate-400 placeholder:italic focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none min-h-24 transition-all duration-200"
                    ></textarea>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-slate-700">Gửi tới Google Form</span>
                            <button 
                                type="button"
                                wire:click="$toggle('submitToGForm')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $submitToGForm ? 'bg-green-500' : 'bg-slate-300' }}"
                            >
                                <span 
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $submitToGForm ? 'translate-x-6' : 'translate-x-1' }}"
                                ></span>
                            </button>
                        </div>

                        <button 
                            type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150"
                        >
                            Gửi Report
                        </button>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-red-900 mb-2">Lỗi:</h3>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('swal', (data) => {
            Swal.fire({
                title: data[0].title,
                text: data[0].text,
                icon: data[0].icon,
            });
        });
    });
</script>
@endpush
```

---

## 11. DATABASE MIGRATIONS & MODELS

### Migration: Create Projects Table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('logo_path')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
```

### Migration: Create Daily Reports Table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->integer('quality_rating')->default(3);
            $table->integer('spirit_rating')->default(3);
            $table->text('notes')->nullable();
            $table->boolean('submitted_to_gform')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->date('report_date')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
```

### Migration: Create Report Tasks Table
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->enum('type', ['today', 'tomorrow']);
            $table->text('description');
            $table->integer('progress')->default(0)->nullable();
            $table->date('expected_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_tasks');
    }
};
```

### Model: Project
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'logo_path', 'color', 'description', 'active'];

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}
```

### Model: DailyReport
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'project_id',
        'quality_rating',
        'spirit_rating',
        'notes',
        'submitted_to_gform',
        'submitted_at',
        'report_date'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'report_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(ReportTask::class);
    }
}
```

### Model: ReportTask
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportTask extends Model
{
    protected $fillable = [
        'daily_report_id',
        'type',
        'description',
        'progress',
        'expected_date'
    ];

    protected $casts = [
        'expected_date' => 'date',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
```

---

## 12. ROUTES SETUP

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\DailyReportPage;
use App\Http\Controllers\LogworkHistoryController;

Route::middleware(['web'])->group(function () {
    Route::get('/', DailyReportPage::class)->name('daily-report');
    
    Route::get('/logwork-history', [LogworkHistoryController::class, 'index'])
        ->name('logwork-history');
});
```

---

## 13. INSTALLATION STEPS

### Step 1: Create Project
```bash
composer create-project laravel/laravel daily-report
cd daily-report
```

### Step 2: Install Dependencies
```bash
composer require livewire/livewire
composer require laravel/breeze --dev
npm install
```

### Step 3: Setup Tailwind CSS
```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

### Step 4: Create Livewire Component
```bash
php artisan livewire:make DailyReportPage
```

### Step 5: Create Models & Migrations
```bash
php artisan make:model Project -m
php artisan make:model DailyReport -m
php artisan make:model ReportTask -m
```

### Step 6: Run Migrations
```bash
php artisan migrate
php artisan db:seed # if you have seeders
```

### Step 7: Setup Routes
Update `routes/web.php` with the routes provided above

### Step 8: Add SweetAlert2
```bash
npm install sweetalert2
```

In your layout: `<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>`

---

## 14. COLOR CODES REFERENCE

| Element | Color | Hex | Tailwind |
|---------|-------|-----|----------|
| Page Background | Light Slate | #F8FAFC | bg-slate-50 |
| Card Background | White | #FFFFFF | bg-white |
| Primary Text | Dark Slate | #0F172A | text-slate-900 |
| Secondary Text | Slate | #475569 | text-slate-600 |
| Border | Light Slate | #E2E8F0 | border-slate-200 |
| Primary Button | Blue | #2563EB | bg-blue-600 |
| Primary Button Hover | Dark Blue | #1D4ED8 | hover:bg-blue-700 |
| Delete Button | Red | #DC2626 | text-red-600 |
| Success/Rating 5 | Emerald | #10B981 | bg-emerald-500 |
| Rating 4 | Green | #16A34A | bg-green-500 |
| Rating 3 | Yellow | #EAB308 | bg-yellow-500 |
| Rating 2 | Orange | #EA580C | bg-orange-500 |
| Rating 1 | Red | #DC2626 | bg-red-500 |

---

## 15. KEY TAILWIND CLASSES

### Layout
- `max-w-4xl` - Container max width (56rem / 896px)
- `mx-auto` - Center container
- `px-4` - Mobile padding
- `px-6` - Desktop padding
- `py-4` - Vertical padding (cards)
- `py-8` - Page vertical padding
- `space-y-6` - Vertical gap between sections (1.5rem)
- `space-y-2` - Gap between task rows (0.5rem)
- `gap-2`, `gap-3` - Flex gap
- `flex-1` - Flexible width
- `flex-shrink-0` - No shrink

### Typography
- `text-3xl` - Page title
- `text-xl` - Card header
- `text-sm` - Labels, body text
- `font-bold` - Heavy weight
- `font-semibold` - Semi-bold
- `font-medium` - Medium weight

### Colors
- `bg-white` - White background
- `bg-slate-50` - Page background
- `text-slate-900` - Primary text
- `text-slate-600` - Secondary text
- `border-slate-200` - Border color
- `hover:bg-slate-50` - Hover background
- `hover:text-red-700` - Hover text color

### States
- `focus:border-blue-500` - Focus border
- `focus:ring-2 focus:ring-blue-100` - Focus ring
- `hover:bg-slate-200` - Hover background
- `opacity-0 group-hover:opacity-100` - Show on hover
- `disabled:bg-slate-100` - Disabled state
- `transition-all duration-200` - Smooth transition

---

## 16. QUICK REFERENCE - COMPONENT HEIGHTS

| Component | Height | Tailwind |
|-----------|--------|----------|
| Input | 2.25rem | h-9 |
| Select | 2.25rem | h-9 |
| Button | 2.5rem | h-10 |
| Icon Button | 2.25rem | h-9 w-9 |
| Rating Button | 2.5rem | h-10 |
| Textarea | 6rem+ | min-h-24 |

---

## 17. DEVELOPMENT CHECKLIST

- [ ] Setup Laravel project
- [ ] Install Livewire & Tailwind
- [ ] Create migrations & models
- [ ] Create Livewire components
- [ ] Setup routing
- [ ] Create Blade templates
- [ ] Add CSS variables
- [ ] Test task add/remove
- [ ] Test rating buttons
- [ ] Test form submission
- [ ] Add SweetAlert2
- [ ] Test responsive design
- [ ] Add database seeding
- [ ] Deploy to production

---

## 18. TIPS & TRICKS

1. **Use `wire:model.defer`** instead of `wire:model` to batch updates
2. **Use `wire:click="$toggle('property')`** for toggle switches
3. **Use `wire:model.live`** for select dropdowns to see changes immediately
4. **Always validate** user input in component or controller
5. **Use `array_values()`** after filtering arrays to reindex
6. **Use `uniqid()`** for temporary task IDs
7. **Use conditional rendering** with `@if/@endif` for optional fields
8. **Test on mobile** - use responsive classes (sm:, md:, lg:)

Đây là file hoàn chỉnh! Bạn có thể copy-paste code trực tiếp vào Laravel project.
