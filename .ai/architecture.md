# Daily Logwork — Architecture

## System Overview

```
┌─────────────────────────────────────────────────┐
│                   Nginx (8082)                   │
├─────────────────────────────────────────────────┤
│              PHP-FPM (Laravel 13)                │
│  ┌──────────┐  ┌───────────┐  ┌───────────────┐ │
│  │ Livewire │  │  Models   │  │  Controllers  │ │
│  │  Pages   │  │ Eloquent  │  │   (optional)  │ │
│  └────┬─────┘  └─────┬─────┘  └───────┬───────┘ │
│       │              │                 │          │
│  ┌────▼──────────────▼─────────────────▼───────┐ │
│  │          Blade Templates (Views)            │ │
│  └────────────────────────┬────────────────────┘ │
├────────────────────────────┼──────────────────────┤
│              MySQL 8.0 (3307)                     │
│              Redis 7 (6379)                       │
└─────────────────────────────────────────────────┘
```

## Component Architecture

### Livewire Page Components

```
App\Livewire\
├── DailyReport\
│   ├── Index.php        # Full version daily report form
│   └── Compact.php      # Compact version (smaller spacing)
└── LogworkHistory\
    └── Index.php        # Paginated list with filters
```

### Models

```
App\Models\
├── DailyReport.php      # Report with quality/spirit ratings
├── ReportTask.php       # Individual task (today/tomorrow)
└── User.php             # Fortify auth
```

### Views

```
resources/views/
├── livewire/
│   ├── daily-report/
│   │   ├── index.blade.php
│   │   └── compact.blade.php
│   └── logwork-history/
│       └── index.blade.php
└── layouts/
    └── app.blade.php
```

### Public Assets

```
public/
├── logos/               # 10 project logos (64x64px)
├── css/                 # Built assets
└── js/                  # Built assets
```

## Data Flow

1. User fills form → Livewire reactive properties update
2. User clicks submit → `wire:submit="save"` → Livewire server method
3. Validation → create DailyReport + ReportTask records
4. Success → redirect to logwork-history
5. History page → paginated list with project filter

## Key Interactions

### Form Submission
```
User Input
    ↓
Livewire Component (reactive state)
    ↓
validate()
    ↓
DailyReport::create()
    ↓
ReportTask::createMany()
    ↓
session()->flash()
    ↓
redirectRoute()
```

### Task Management
```
Add Task → array_push()
Remove Task → array_filter() + array_values()
Update Task → foreach + array reference
Reorder Task → usort() with ordered IDs
```

### History Filtering
```
Filter by project → where('project', $filter)
Search → where('project', 'like') + where('notes', 'like')
Sort → orderBy('report_date', 'desc')
Pagination → ->paginate(20)
```
