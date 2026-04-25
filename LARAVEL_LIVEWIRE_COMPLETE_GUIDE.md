# Daily Report - Laravel Livewire Complete Implementation Guide

**Latest Update:** 2026-04-25  
**Framework:** Laravel 11.x + Livewire 3.x  
**Status:** Production Ready

---

## TABLE OF CONTENTS
1. [Quick Start](#quick-start)
2. [Tech Stack & Versions](#tech-stack--versions)
3. [Project Structure](#project-structure)
4. [Color System & Design Tokens](#color-system--design-tokens)
5. [Typography Scale](#typography-scale)
6. [Spacing System](#spacing-system)
7. [Component Specifications](#component-specifications)
8. [Tailwind Classes Reference](#tailwind-classes-reference)
9. [Livewire Implementation](#livewire-implementation)
10. [Database Schema](#database-schema)
11. [Complete Component Breakdown](#complete-component-breakdown)
12. [Layout Wireframes](#layout-wireframes)
13. [Installation & Setup](#installation--setup)
14. [Development Guidelines](#development-guidelines)

---

## QUICK START

```bash
# 1. Create Laravel project
composer create-project laravel/laravel daily-report
cd daily-report

# 2. Install Livewire
composer require livewire/livewire
php artisan livewire:install

# 3. Install Tailwind CSS
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# 4. Install SweetAlert2
npm install sweetalert2
npm run dev

# 5. Create migrations
php artisan make:migration create_projects_table
php artisan make:migration create_daily_reports_table
php artisan make:migration create_report_tasks_table

# 6. Create Livewire components
php artisan make:livewire DailyReportForm
php artisan make:livewire LogworkHistory

# 7. Run migrations
php artisan migrate

# 8. Start dev server
php artisan serve
npm run dev
```

---

## TECH STACK & VERSIONS

### Backend
```json
{
  "php": "^8.2",
  "laravel/framework": "^11.0",
  "livewire/livewire": "^3.0",
  "laravel/sanctum": "^4.0",
  "livewire/volt": "^1.0" // Optional: for file-based components
}
```

### Frontend
```json
{
  "tailwindcss": "^3.4",
  "postcss": "^8.4",
  "autoprefixer": "^10.4",
  "sweetalert2": "^11.10",
  "@tailwindcss/forms": "^0.5"
}
```

### Optional Libraries
```json
{
  "axios": "^1.6",
  "alpinejs": "^3.x", // For complex JS interactions
  "livewire/plugins": "^1.0"
}
```

---

## PROJECT STRUCTURE

```
daily-report/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── DailyReportController.php
│   ├── Livewire/
│   │   ├── DailyReportForm.php          // Main form component
│   │   ├── DailyReportFormCompact.php   // Compact version
│   │   ├── LogworkHistory.php           // History view
│   │   ├── Components/
│   │   │   ├── TaskInput.php            // Reusable task input
│   │   │   ├── RatingButtons.php        // Rating component
│   │   │   └── ProjectSelect.php        // Project selector
│   │   └── Traits/
│   │       └── HandlesTaskManagement.php
│   ├── Models/
│   │   ├── Project.php
│   │   ├── DailyReport.php
│   │   ├── ReportTask.php
│   │   └── User.php
│   └── Enums/
│       ├── TaskProgress.php
│       └── Rating.php
├── database/
│   └── migrations/
│       ├── 2026_01_01_000000_create_projects_table.php
│       ├── 2026_01_01_000001_create_daily_reports_table.php
│       └── 2026_01_01_000002_create_report_tasks_table.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php            // Main layout
│   │   ├── livewire/
│   │   │   ├── daily-report-form.blade.php
│   │   │   ├── daily-report-form-compact.blade.php
│   │   │   ├── logwork-history.blade.php
│   │   │   └── components/
│   │   │       ├── task-input.blade.php
│   │   │       ├── rating-buttons.blade.php
│   │   │       └── project-select.blade.php
│   │   ├── components/
│   │   │   ├── card.blade.php
│   │   │   ├── input.blade.php
│   │   │   ├── button.blade.php
│   │   │   ├── select.blade.php
│   │   │   └── textarea.blade.php
│   │   └── welcome.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       ├── app.js
│       └── alpine.js
├── routes/
│   ├── web.php
│   └── api.php
├── config/
│   ├── tailwind.config.js
│   └── app.php
├── storage/logs/
└── .env.example
```

---

## COLOR SYSTEM & DESIGN TOKENS

### Primary Color Palette

```css
:root {
  /* Neutral Colors */
  --color-white: #FFFFFF;
  --color-slate-50: #F8FAFC;
  --color-slate-100: #F1F5F9;
  --color-slate-200: #E2E8F0;
  --color-slate-300: #CBD5E1;
  --color-slate-400: #A0AEC0;
  --color-slate-500: #64748B;
  --color-slate-600: #475569;
  --color-slate-700: #334155;
  --color-slate-800: #1E293B;
  --color-slate-900: #0F172A;

  /* Brand Colors */
  --color-blue-50: #EFF6FF;
  --color-blue-100: #BFDBFE;
  --color-blue-500: #3B82F6;
  --color-blue-600: #2563EB;
  --color-blue-700: #1D4ED8;
  --color-blue-800: #1E40AF;

  /* Status Colors */
  --color-red-50: #FEE2E2;
  --color-red-600: #DC2626;
  --color-red-700: #B91C1C;
  --color-green-50: #F0FDF4;
  --color-green-500: #22C55E;
  --color-green-600: #16A34A;

  /* Project Brand Colors */
  --color-jrr: #3B82F6;        /* Blue */
  --color-primas: #A855F7;     /* Purple */
  --color-project-a: #10B981;  /* Green */
  --color-project-b: #F97316;  /* Orange */
  --color-project-c: #EF4444;  /* Red */
  --color-project-d: #EC4899;  /* Pink */
  --color-project-e: #06B6D4;  /* Cyan */
  --color-project-f: #6366F1;  /* Indigo */
  --color-project-g: #14B8A6;  /* Teal */
  --color-project-h: #F59E0B;  /* Amber */
}
```

### Tailwind Color Extensions

```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        'brand': {
          50: '#EFF6FF',
          100: '#BFDBFE',
          500: '#3B82F6',
          600: '#2563EB',
          700: '#1D4ED8',
        },
        'project': {
          'jrr': '#3B82F6',
          'primas': '#A855F7',
          'a': '#10B981',
          'b': '#F97316',
          'c': '#EF4444',
          'd': '#EC4899',
          'e': '#06B6D4',
          'f': '#6366F1',
          'g': '#14B8A6',
          'h': '#F59E0B',
        }
      }
    }
  }
}
```

### Color Usage Guide

| Element | Background | Text | Border | Hover |
|---------|-----------|------|--------|-------|
| Page | slate-50 | slate-900 | - | - |
| Card | white | slate-900 | slate-200 | - |
| Input Default | white | slate-900 | slate-200 | slate-300 |
| Input Focus | white | slate-900 | blue-500 | blue-500 |
| Button Primary | blue-600 | white | blue-600 | blue-700 |
| Button Secondary | slate-100 | slate-700 | slate-200 | slate-200 |
| Button Delete | transparent | red-600 | - | red-50 |
| Task Row Hover | - | - | - | slate-50 |
| Rating Selected | blue-600 | white | blue-600 | blue-700 |
| Rating Unselected | white | slate-700 | slate-200 | slate-100 |

---

## TYPOGRAPHY SCALE

### Font Family Configuration

```css
/* app.css */
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&display=swap');

@layer base {
  html {
    @apply font-sans;
  }
  
  * {
    @apply font-['Geist'];
  }
}
```

### Heading Hierarchy

#### Full Version

| Element | Size | Weight | Line Height | Letter Spacing | Margin |
|---------|------|--------|-------------|-----------------|--------|
| Page Title | 30px (text-3xl) | 700 (bold) | 2.25rem | -0.01em | mb-2 |
| Card Title | 20px (text-xl) | 600 (semibold) | 1.75rem | 0 | mb-2 |
| Card Subtitle | 14px (text-sm) | 400 | 1.25rem | 0 | mb-4 |
| Input Label | 14px (text-sm) | 500 | 1.25rem | 0 | mb-2 |
| Body Text | 14px (text-sm) | 400 | 1.25rem | 0 | - |
| Small Text | 12px (text-xs) | 400 | 1rem | 0 | - |

#### Compact Version

| Element | Size | Weight | Line Height | Margin |
|---------|------|--------|-------------|--------|
| Page Title | 24px (text-2xl) | 700 | 2rem | mb-2 |
| Card Title | 18px (text-lg) | 600 | 1.75rem | mb-2 |
| Body Text | 12px (text-xs) | 400 | 1rem | - |

### Blade Usage Examples

```blade
<!-- Page Title -->
<h1 class="text-3xl font-bold text-slate-900 mb-2">Daily Report</h1>

<!-- Card Title -->
<h2 class="text-xl font-semibold text-slate-900 mb-2">Chọn dự án</h2>

<!-- Card Description -->
<p class="text-sm text-slate-500 mb-4">Chọn dự án để bắt đầu ghi chép</p>

<!-- Input Label -->
<label class="block text-sm font-medium text-slate-700 mb-2">
  Mô tả công việc
</label>

<!-- Body Text -->
<span class="text-sm text-slate-600">{{ $task->description }}</span>

<!-- Small Text -->
<span class="text-xs text-slate-500">Ngày tạo: {{ $task->created_at }}</span>
```

---

## SPACING SYSTEM

### Full Version Spacing Scale

```
Spacing Multiplier: 4px (1 unit = 4px)

0   = 0px      (0)
1   = 4px      (p-1, m-1)
1.5 = 6px      (p-1.5, m-1.5)
2   = 8px      (p-2, m-2)
3   = 12px     (p-3, m-3)
4   = 16px     (p-4, m-4)
6   = 24px     (p-6, m-6)
8   = 32px     (p-8, m-8)
```

### Container & Page Layout

```blade
<!-- Main Container - Full Version -->
<div class="min-h-screen bg-slate-50 py-8 px-4">
  <div class="max-w-4xl mx-auto">
    <!-- Content here -->
  </div>
</div>

<!-- Main Container - Compact Version -->
<div class="min-h-screen bg-slate-50 py-4 px-3">
  <div class="max-w-3xl mx-auto">
    <!-- Content here -->
  </div>
</div>
```

### Card Spacing

```blade
<!-- Full Version Card -->
<div class="bg-white rounded-lg border border-slate-200 shadow-sm">
  <!-- Header -->
  <div class="px-6 py-4 border-b border-slate-200">
    <h2 class="text-xl font-semibold">Title</h2>
    <p class="text-sm text-slate-500 mt-1">Description</p>
  </div>
  
  <!-- Content -->
  <div class="px-6 py-4">
    <!-- Content here -->
  </div>
</div>

<!-- Compact Version Card -->
<div class="bg-white rounded-lg border border-slate-200 shadow-sm">
  <div class="px-3 py-3 border-b border-slate-200">
    <h2 class="text-lg font-semibold">Title</h2>
  </div>
  
  <div class="px-3 py-3">
    <!-- Content here -->
  </div>
</div>
```

### Section Spacing

```blade
<!-- Full Version: space-y-6 = 1.5rem gap between sections -->
<div class="space-y-6">
  <!-- Section 1 -->
  <!-- Section 2 -->
  <!-- Section 3 -->
</div>

<!-- Compact Version: space-y-3 = 0.75rem gap -->
<div class="space-y-3">
  <!-- Section 1 -->
  <!-- Section 2 -->
  <!-- Section 3 -->
</div>
```

### Task Row Spacing

```blade
<!-- Full Version -->
<div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors">
  <!-- Content here -->
</div>

<!-- Compact Version -->
<div class="flex flex-col sm:flex-row gap-1.5 sm:gap-2 items-start sm:items-center p-1.5 hover:bg-slate-50 rounded-md transition-colors">
  <!-- Content here -->
</div>
```

---

## COMPONENT SPECIFICATIONS

### 1. CARD COMPONENT

**Location:** `resources/views/components/card.blade.php`

```blade
@props(['class' => ''])

<div class="bg-white rounded-lg border border-slate-200 shadow-sm {{ $class }}">
  {{ $slot }}
</div>
```

**Usage:**
```blade
<x-card>
  <!-- Content -->
</x-card>
```

**Styling:**
- Background: bg-white (#FFFFFF)
- Border: border-slate-200, border: 1px
- Border radius: rounded-lg (8px)
- Shadow: shadow-sm (0 1px 2px rgba(0,0,0,0.05))

---

### 2. CARD HEADER COMPONENT

**Full Version:**
```blade
<div class="px-6 py-4 border-b border-slate-200">
  <h2 class="text-xl font-semibold text-slate-900">Title</h2>
  <p class="text-sm text-slate-500 mt-1">Description (optional)</p>
</div>
```

**Compact Version:**
```blade
<div class="px-3 py-3 border-b border-slate-200">
  <h2 class="text-lg font-semibold text-slate-900">Title</h2>
  <p class="text-xs text-slate-500 mt-1">Description (optional)</p>
</div>
```

**Styling:**
- Padding: px-6 py-4 (full) / px-3 py-3 (compact)
- Border: border-b border-slate-200
- Title: text-xl font-semibold (full) / text-lg (compact)
- Description: text-sm text-slate-500 (full) / text-xs (compact)

---

### 3. INPUT FIELD COMPONENT

**Location:** `resources/views/components/input.blade.php`

```blade
@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'class' => '',
    'compact' => false,
])

<div>
  @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-2">
      {{ $label }}
      @if($required)
        <span class="text-red-600">*</span>
      @endif
    </label>
  @endif

  <input
    id="{{ $name }}"
    type="{{ $type }}"
    name="{{ $name }}"
    value="{{ $value }}"
    placeholder="{{ $placeholder }}"
    @if($required) required @endif
    @if($disabled) disabled @endif
    class="w-full {{ $compact ? 'h-8' : 'h-9' }} px-3 py-2 border border-slate-200 rounded-md text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors {{ $class }}"
  />
</div>
```

**Full Version Classes:**
```
h-9 px-3 py-2 text-sm border border-slate-200 rounded-md
focus:ring-2 focus:ring-blue-500 focus:border-blue-500
hover:border-slate-300
disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed
```

**Compact Version Classes:**
```
h-8 px-3 py-1 text-xs border border-slate-200 rounded-md
focus:ring-2 focus:ring-blue-500
hover:border-slate-300
```

---

### 4. SELECT COMPONENT

**Location:** `resources/views/components/select.blade.php`

```blade
@props([
    'name' => '',
    'label' => '',
    'options' => [],
    'selected' => '',
    'required' => false,
    'compact' => false,
])

<div>
  @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-2">
      {{ $label }}
    </label>
  @endif

  <select
    id="{{ $name }}"
    name="{{ $name }}"
    @if($required) required @endif
    class="w-full {{ $compact ? 'h-8' : 'h-9' }} px-3 py-2 border border-slate-200 rounded-md text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
  >
    @foreach($options as $value => $label)
      <option value="{{ $value }}" @selected($selected == $value)>
        {{ $label }}
      </option>
    @endforeach
  </select>
</div>
```

**Styling:**
- Height: h-9 (full) / h-8 (compact)
- Padding: px-3 py-2
- Border: border border-slate-200
- Border radius: rounded-md
- Text: text-sm text-slate-900
- Focus: ring-2 ring-blue-500 border-blue-500

---

### 5. BUTTON COMPONENTS

#### Primary Button

```blade
<button
  type="submit"
  class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
>
  Gửi Report
</button>
```

**Styling:**
- Background: bg-blue-600
- Hover: hover:bg-blue-700
- Text: text-white font-medium
- Padding: px-6 py-2.5
- Focus: ring-2 ring-blue-500 ring-offset-2
- Border radius: rounded-md

#### Secondary Button

```blade
<button
  type="button"
  class="px-4 py-2 bg-slate-100 text-slate-700 font-medium border border-slate-200 rounded-md hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 transition-colors"
>
  Thêm công việc
</button>
```

#### Delete Button (Icon)

```blade
<button
  type="button"
  class="flex-shrink-0 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md opacity-0 group-hover:opacity-100 transition-all"
  wire:click="removeTask('{{ $task->id }}')"
  title="Xóa"
>
  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
  </svg>
</button>
```

---

### 6. TEXTAREA COMPONENT

```blade
@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'rows' => 3,
    'compact' => false,
])

<div>
  @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-2">
      {{ $label }}
    </label>
  @endif

  <textarea
    id="{{ $name }}"
    name="{{ $name }}"
    rows="{{ $compact ? 2 : $rows }}"
    placeholder="{{ $placeholder }}"
    class="w-full px-3 py-2 border border-slate-200 rounded-md text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical"
  ></textarea>
</div>
```

**Styling:**
- Rows: 3 (full) / 2 (compact)
- Padding: px-3 py-2
- Min height: min-h-24
- Resize: resize-vertical
- Border & focus: same as input

---

### 7. RATING BUTTONS

**Full Version:**
```blade
<div class="flex gap-2 justify-center">
  @foreach([1, 2, 3, 4, 5] as $rating)
    <button
      type="button"
      wire:click="setQualityRating({{ $rating }})"
      class="flex-1 h-10 rounded-md font-medium transition-all
        @if($qualityRating == $rating)
          bg-blue-600 text-white border border-blue-600
        @else
          bg-white text-slate-700 border border-slate-200 hover:border-slate-300 hover:bg-slate-50
        @endif"
    >
      {{ $rating }}
    </button>
  @endforeach
</div>
```

**Compact Version:**
```blade
<div class="flex gap-1.5 justify-center">
  @foreach([1, 2, 3, 4, 5] as $rating)
    <button
      type="button"
      wire:click="setQualityRating({{ $rating }})"
      class="flex-1 h-8 rounded-md text-sm font-medium transition-all
        @if($qualityRating == $rating)
          bg-blue-600 text-white border border-blue-600
        @else
          bg-white text-slate-700 border border-slate-200 hover:bg-slate-50
        @endif"
    >
      {{ $rating }}
    </button>
  @endforeach
</div>
```

---

### 8. DRAG HANDLE ICON

```blade
<div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors cursor-move">
  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
    <path d="M8 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM8 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM9 1.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3zM9 8.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3zM9 15.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3zM16 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM16 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
  </svg>
</div>
```

**Alternative (SVG from Lucide):**
```blade
<!-- GripVertical icon equivalent -->
<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
  <circle cx="9" cy="5" r="1"></circle>
  <circle cx="9" cy="12" r="1"></circle>
  <circle cx="9" cy="19" r="1"></circle>
  <circle cx="15" cy="5" r="1"></circle>
  <circle cx="15" cy="12" r="1"></circle>
  <circle cx="15" cy="19" r="1"></circle>
</svg>
```

---

## TAILWIND CLASSES REFERENCE

### Complete Class List by Category

#### Sizing
```
Size Classes Used in This Project:
w-3, w-4, w-5              // Icons
h-7, h-8, h-9, h-10        // Inputs, buttons
w-16, h-16                 // Logo
w-20                       // Progress select
w-32                       // Date input
min-h-24                   // Textarea
flex-1                     // Flex grow
flex-shrink-0              // No shrink
```

#### Padding & Margin
```
Padding (p/px/py/pt/pb/pl/pr):
p-1.5, p-2, p-3, p-4, p-6, p-8
px-3, px-4, px-6
py-1, py-2, py-3, py-4

Margin (m/mx/my/mb/mt/ml/mr):
mb-1, mb-2, mb-4
mt-1, mt-3, mt-4
mx-auto
my-4

Gap:
gap-1.5, gap-2, gap-3, gap-4, gap-6
space-y-1.5, space-y-2, space-y-3, space-y-4, space-y-6
```

#### Text & Font
```
Font Weight:
font-normal (400)
font-medium (500)
font-semibold (600)
font-bold (700)

Font Size:
text-xs    (12px)
text-sm    (14px)
text-base  (16px)
text-lg    (18px)
text-xl    (20px)
text-2xl   (24px)
text-3xl   (30px)

Line Height:
leading-5, leading-6, leading-7, leading-9
leading-relaxed (1.625)

Color:
text-slate-400, text-slate-500, text-slate-600, text-slate-700, text-slate-900
text-white
text-red-600, text-red-700
text-blue-600
```

#### Colors
```
Background:
bg-slate-50, bg-slate-100, bg-slate-200, bg-slate-800
bg-white
bg-blue-50, bg-blue-100, bg-blue-500, bg-blue-600, bg-blue-700
bg-red-50
bg-green-600

Text:
text-slate-400, text-slate-500, text-slate-600, text-slate-700, text-slate-900
text-white
text-red-600, text-red-700
text-blue-600

Border:
border border-slate-200
border-b border-slate-200
border-blue-500
border-red-600
```

#### Border & Radius
```
Border Width:
border (1px)

Border Radius:
rounded-md (6px)
rounded-lg (8px)

Shadow:
shadow-sm
```

#### Display & Layout
```
Display:
flex
grid
hidden
block
inline-block

Flex Properties:
flex-col, flex-row
flex-1
flex-shrink-0
items-center, items-start
justify-between, justify-center
gap-2, gap-3, gap-4, gap-6

Grid:
grid-cols-1, grid-cols-2
md:grid-cols-2 (responsive)
```

#### Responsive
```
Mobile First Breakpoints:
sm: (640px)
md: (768px)
lg: (1024px)

Usage:
flex-col sm:flex-row
grid-cols-1 md:grid-cols-2
w-full sm:w-32
px-4 sm:px-6
text-xs sm:text-sm
```

#### States
```
Hover:
hover:bg-slate-50, hover:bg-slate-100, hover:bg-slate-200
hover:border-slate-300
hover:text-slate-600, hover:text-red-700
hover:bg-red-50

Focus:
focus:outline-none
focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
focus:border-blue-500

Active:
active:bg-blue-800

Disabled:
disabled:bg-slate-50
disabled:text-slate-500
disabled:cursor-not-allowed

Group Hover:
group-hover:opacity-100
group-hover:bg-red-50
```

#### Transitions & Animations
```
Transition:
transition-all (all properties)
transition-colors (color properties only)
transition-opacity (opacity only)
duration-200 (200ms)

Ease:
ease (default)

Opacity:
opacity-0 (hidden)
opacity-100 (visible)

Cursor:
cursor-pointer
cursor-move
cursor-not-allowed
```

#### Utilities
```
Text Align:
text-center
text-left
text-right

Overflow:
overflow-hidden
overflow-auto

Min/Max Width/Height:
min-h-24
max-w-3xl (compact)
max-w-4xl (full)

Aspect Ratio:
aspect-square

Box Shadow:
shadow-sm
```

---

## LIVEWIRE IMPLEMENTATION

### Main Livewire Component Class

**File:** `app/Livewire/DailyReportForm.php`

```php
<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\DailyReport;
use App\Models\ReportTask;
use Livewire\Component;

class DailyReportForm extends Component
{
    // Properties
    public $selectedProject = 'JRR';
    public $todayTasks = [
        ['id' => 1, 'description' => '', 'progress' => 0, 'expectedDate' => ''],
    ];
    public $tomorrowTasks = [
        ['id' => 1, 'description' => ''],
    ];
    public $qualityRating = 3;
    public $spiritRating = 3;
    public $notes = '';
    public $submitToGForm = true;
    
    // Computed properties
    #[Computed]
    public function projects()
    {
        return [
            'JRR' => 'JRR',
            'Primas' => 'Primas',
            'Project A' => 'Project A',
            'Project B' => 'Project B',
            'Project C' => 'Project C',
            'Project D' => 'Project D',
            'Project E' => 'Project E',
            'Project F' => 'Project F',
            'Project G' => 'Project G',
            'Project H' => 'Project H',
        ];
    }

    // Mount lifecycle
    public function mount()
    {
        $this->todayTasks = [
            ['id' => uniqid(), 'description' => '', 'progress' => 0, 'expectedDate' => ''],
        ];
        $this->tomorrowTasks = [
            ['id' => uniqid(), 'description' => ''],
        ];
    }

    // Today Tasks Handlers
    public function addTodayTask()
    {
        $this->todayTasks[] = [
            'id' => uniqid(),
            'description' => '',
            'progress' => 0,
            'expectedDate' => '',
        ];
    }

    public function removeTodayTask($id)
    {
        if (count($this->todayTasks) > 1) {
            $this->todayTasks = array_filter(
                $this->todayTasks,
                fn($task) => $task['id'] !== $id
            );
        }
    }

    public function updateTodayTask($id, $field, $value)
    {
        $this->todayTasks = array_map(function ($task) use ($id, $field, $value) {
            if ($task['id'] === $id) {
                $task[$field] = $value;
            }
            return $task;
        }, $this->todayTasks);
    }

    // Tomorrow Tasks Handlers
    public function addTomorrowTask()
    {
        $this->tomorrowTasks[] = [
            'id' => uniqid(),
            'description' => '',
        ];
    }

    public function removeTomorrowTask($id)
    {
        if (count($this->tomorrowTasks) > 1) {
            $this->tomorrowTasks = array_filter(
                $this->tomorrowTasks,
                fn($task) => $task['id'] !== $id
            );
        }
    }

    public function updateTomorrowTask($id, $value)
    {
        $this->tomorrowTasks = array_map(function ($task) use ($id, $value) {
            if ($task['id'] === $id) {
                $task['description'] = $value;
            }
            return $task;
        }, $this->tomorrowTasks);
    }

    // Rating Handlers
    public function setQualityRating($rating)
    {
        $this->qualityRating = $rating;
    }

    public function setSpiritRating($rating)
    {
        $this->spiritRating = $rating;
    }

    // Form Submission
    public function submitReport()
    {
        // Validation
        $validTodayTasks = array_filter(
            $this->todayTasks,
            fn($task) => !empty(trim($task['description']))
        );

        if (empty($validTodayTasks)) {
            $this->addError('todayTasks', 'Phải có ít nhất một công việc hôm nay');
            return;
        }

        // Save to database
        $report = DailyReport::create([
            'user_id' => auth()->id(),
            'project' => $this->selectedProject,
            'quality_rating' => $this->qualityRating,
            'spirit_rating' => $this->spiritRating,
            'notes' => $this->notes,
            'report_date' => now(),
        ]);

        // Save tasks
        foreach ($validTodayTasks as $task) {
            ReportTask::create([
                'daily_report_id' => $report->id,
                'description' => $task['description'],
                'progress' => $task['progress'],
                'expected_date' => $task['progress'] < 100 ? $task['expectedDate'] : null,
                'type' => 'today',
            ]);
        }

        foreach ($this->tomorrowTasks as $task) {
            if (!empty(trim($task['description']))) {
                ReportTask::create([
                    'daily_report_id' => $report->id,
                    'description' => $task['description'],
                    'type' => 'tomorrow',
                ]);
            }
        }

        // Show success message
        $this->dispatch('swal:success', [
            'title' => 'Thành công!',
            'message' => 'Report đã được lưu',
        ]);

        // Reset form
        $this->reset();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.daily-report-form');
    }
}
```

### Livewire Blade Template

**File:** `resources/views/livewire/daily-report-form.blade.php`

```blade
<div class="min-h-screen bg-slate-50 py-8 px-4">
  <div class="max-w-4xl mx-auto">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-slate-900">Daily Report</h1>
      <a href="{{ route('logwork-history') }}" class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:text-blue-700 transition-colors">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
          <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
        </svg>
        Lịch sử
      </a>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="submitReport" class="space-y-6">

      <!-- Project Selection -->
      <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200">
          <h2 class="text-xl font-semibold text-slate-900">Chọn dự án</h2>
        </div>
        <div class="px-6 py-4">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 flex-shrink-0">
              <img 
                src="{{ asset('logos/' . strtolower(str_replace(' ', '-', $selectedProject)) . '.jpg') }}" 
                alt="{{ $selectedProject }}"
                class="w-full h-full object-cover rounded-lg"
              >
            </div>
            <select 
              wire:model.live="selectedProject"
              class="flex-1 h-9 px-3 py-2 border border-slate-200 rounded-md text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              @foreach(['JRR', 'Primas', 'Project A', 'Project B', 'Project C', 'Project D', 'Project E', 'Project F', 'Project G', 'Project H'] as $project)
                <option value="{{ $project }}" @selected($selectedProject === $project)>
                  {{ $project }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <!-- Today's Tasks -->
      <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200">
          <h2 class="text-xl font-semibold text-slate-900">Task ngày hôm nay</h2>
          <p class="text-sm text-slate-500 mt-1">Nhập các công việc đã làm hôm nay</p>
        </div>
        <div class="px-6 py-4">
          <div class="space-y-2">
            @foreach($todayTasks as $index => $task)
              <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
                
                <!-- Drag Handle -->
                <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="5" r="1"></circle>
                    <circle cx="9" cy="12" r="1"></circle>
                    <circle cx="9" cy="19" r="1"></circle>
                    <circle cx="15" cy="5" r="1"></circle>
                    <circle cx="15" cy="12" r="1"></circle>
                    <circle cx="15" cy="19" r="1"></circle>
                  </svg>
                </div>

                <!-- Description -->
                <input
                  type="text"
                  wire:model.lazy="todayTasks.{{ $index }}.description"
                  placeholder="Mô tả..."
                  class="flex-1 h-9 px-3 py-2 border border-slate-200 rounded-md text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >

                <!-- Progress -->
                <select
                  wire:model.live="todayTasks.{{ $index }}.progress"
                  class="w-20 h-9 px-3 py-2 border border-slate-200 rounded-md text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  @foreach([0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100] as $progress)
                    <option value="{{ $progress }}" @selected($task['progress'] == $progress)>{{ $progress }}%</option>
                  @endforeach
                </select>

                <!-- Expected Date (show only if progress < 100) -->
                @if($task['progress'] < 100)
                  <input
                    type="date"
                    wire:model.lazy="todayTasks.{{ $index }}.expectedDate"
                    class="w-32 h-9 px-3 py-2 border border-slate-200 rounded-md text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                @endif

                <!-- Delete Button -->
                @if(count($todayTasks) > 1)
                  <button
                    type="button"
                    wire:click="removeTodayTask('{{ $task['id'] }}')"
                    class="flex-shrink-0 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md opacity-0 group-hover:opacity-100 transition-all"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                @endif
              </div>
            @endforeach

            <!-- Add More Button -->
            <button
              type="button"
              wire:click="addTodayTask"
              class="w-full mt-2 px-4 py-2 bg-slate-100 text-slate-700 font-medium border border-slate-200 rounded-md hover:bg-slate-200 transition-colors text-sm flex items-center justify-center gap-1"
            >
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
              </svg>
              Thêm
            </button>
          </div>

          @error('todayTasks')
            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-600">
              {{ $message }}
            </div>
          @enderror
        </div>
      </div>

      <!-- Tomorrow's Tasks -->
      <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200">
          <h2 class="text-xl font-semibold text-slate-900">Task dự kiến ngày mai</h2>
          <p class="text-sm text-slate-500 mt-1">Lên kế hoạch cho ngày mai</p>
        </div>
        <div class="px-6 py-4">
          <div class="space-y-2">
            @foreach($tomorrowTasks as $index => $task)
              <div class="flex gap-2 items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
                
                <!-- Drag Handle -->
                <div class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="5" r="1"></circle>
                    <circle cx="9" cy="12" r="1"></circle>
                    <circle cx="9" cy="19" r="1"></circle>
                    <circle cx="15" cy="5" r="1"></circle>
                    <circle cx="15" cy="12" r="1"></circle>
                    <circle cx="15" cy="19" r="1"></circle>
                  </svg>
                </div>

                <!-- Description -->
                <input
                  type="text"
                  wire:model.lazy="tomorrowTasks.{{ $index }}.description"
                  placeholder="Mô tả..."
                  class="flex-1 h-9 px-3 py-2 border border-slate-200 rounded-md text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >

                <!-- Delete Button -->
                @if(count($tomorrowTasks) > 1)
                  <button
                    type="button"
                    wire:click="removeTomorrowTask('{{ $task['id'] }}')"
                    class="flex-shrink-0 p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md opacity-0 group-hover:opacity-100 transition-all"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                @endif
              </div>
            @endforeach

            <!-- Add More Button -->
            <button
              type="button"
              wire:click="addTomorrowTask"
              class="w-full mt-2 px-4 py-2 bg-slate-100 text-slate-700 font-medium border border-slate-200 rounded-md hover:bg-slate-200 transition-colors text-sm flex items-center justify-center gap-1"
            >
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
              </svg>
              Thêm
            </button>
          </div>
        </div>
      </div>

      <!-- Self Evaluation -->
      <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200">
          <h2 class="text-xl font-semibold text-slate-900">Tự đánh giá</h2>
          <p class="text-sm text-slate-500 mt-1">Đánh giá chất lượng công việc và tinh thần làm việc</p>
        </div>
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Quality Rating -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-4">Chất lượng công việc</label>
              <div class="flex gap-2 justify-center">
                @foreach([1, 2, 3, 4, 5] as $rating)
                  <button
                    type="button"
                    wire:click="setQualityRating({{ $rating }})"
                    class="flex-1 h-10 rounded-md font-medium transition-all
                      @if($qualityRating == $rating)
                        bg-blue-600 text-white border border-blue-600
                      @else
                        bg-white text-slate-700 border border-slate-200 hover:border-slate-300 hover:bg-slate-50
                      @endif"
                  >
                    {{ $rating }}
                  </button>
                @endforeach
              </div>
            </div>

            <!-- Spirit Rating -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-4">Tinh thần làm việc</label>
              <div class="flex gap-2 justify-center">
                @foreach([1, 2, 3, 4, 5] as $rating)
                  <button
                    type="button"
                    wire:click="setSpiritRating({{ $rating }})"
                    class="flex-1 h-10 rounded-md font-medium transition-all
                      @if($spiritRating == $rating)
                        bg-blue-600 text-white border border-blue-600
                      @else
                        bg-white text-slate-700 border border-slate-200 hover:border-slate-300 hover:bg-slate-50
                      @endif"
                  >
                    {{ $rating }}
                  </button>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Notes -->
      <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200">
          <h2 class="text-xl font-semibold text-slate-900">Ghi chú</h2>
          <p class="text-sm text-slate-500 mt-1">Thêm ghi chú tùy chọn</p>
        </div>
        <div class="px-6 py-4">
          <textarea
            wire:model.lazy="notes"
            rows="3"
            placeholder="Nhập ghi chú (tùy chọn)..."
            class="w-full px-3 py-2 border border-slate-200 rounded-md text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical"
          ></textarea>
        </div>
      </div>

      <!-- Submit -->
      <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="px-6 py-4">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  wire:model="submitToGForm"
                  class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                >
                <span class="text-sm text-slate-700">Gửi tới Google Form</span>
              </label>
            </div>
            <button
              type="submit"
              wire:loading.attr="disabled"
              class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span wire:loading.remove>Gửi Report</span>
              <span wire:loading>Đang gửi...</span>
            </button>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('livewire:navigated', () => {
    Livewire.on('swal:success', (data) => {
      Swal.fire({
        title: data[0].title,
        text: data[0].message,
        icon: 'success',
        confirmButtonText: 'OK'
      });
    });
  });
</script>
@endpush
```

---

## DATABASE SCHEMA

### Migrations

**File:** `database/migrations/2026_01_01_000000_create_projects_table.php`

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
            $table->string('color')->default('#3B82F6');
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
```

**File:** `database/migrations/2026_01_01_000001_create_daily_reports_table.php`

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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->integer('quality_rating')->default(3); // 1-5
            $table->integer('spirit_rating')->default(3);  // 1-5
            $table->text('notes')->nullable();
            $table->boolean('submitted_to_gform')->default(false);
            $table->date('report_date');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('report_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
```

**File:** `database/migrations/2026_01_01_000002_create_report_tasks_table.php`

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
            $table->foreignId('daily_report_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->integer('progress')->default(0); // 0-100
            $table->date('expected_date')->nullable();
            $table->enum('type', ['today', 'tomorrow'])->default('today');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index('daily_report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_tasks');
    }
};
```

### Models

**File:** `app/Models/Project.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'color', 'logo_path'];

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}
```

**File:** `app/Models/DailyReport.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'quality_rating',
        'spirit_rating',
        'notes',
        'submitted_to_gform',
        'report_date',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(ReportTask::class)->orderBy('order');
    }
}
```

**File:** `app/Models/ReportTask.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportTask extends Model
{
    protected $fillable = [
        'daily_report_id',
        'description',
        'progress',
        'expected_date',
        'type',
        'order',
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

## COMPLETE COMPONENT BREAKDOWN

### Level 1: Page Structure

```
Page (Full Version)
├── Header
│   ├── Title
│   └── History Link
├── Main Container (max-w-4xl)
│   ├── Project Selection Card
│   ├── Today's Tasks Card
│   ├── Tomorrow's Tasks Card
│   ├── Self Evaluation Card
│   ├── Notes Card
│   └── Submit Card

Page (Compact Version)
├── Header
├── Main Container (max-w-3xl)
└── Same structure with adjusted spacing
```

### Level 2: Card Structure

```
Card
├── Card Header
│   ├── Title
│   └── Description (optional)
└── Card Content
    └── Content specific to section
```

### Level 3: Section Specifics

#### Project Selection Section
```
Card
├── Header: "Chọn dự án"
└── Content
    ├── Logo (w-16 h-16)
    └── Select Dropdown (flex-1)
```

#### Task Section (Today/Tomorrow)
```
Card
├── Header
└── Content
    ├── Task List (space-y-2)
    │   └── Task Row (repeated)
    │       ├── Drag Handle
    │       ├── Input (description)
    │       ├── Select (progress) - Today only
    │       ├── Input (date) - Today only, conditional
    │       └── Delete Button (hidden, shown on hover)
    └── Add Button
```

#### Rating Section
```
Card
├── Header
└── Content
    ├── Grid (cols-1 md:cols-2)
    │   ├── Quality Rating
    │   │   ├── Label
    │   │   └── Rating Buttons (1-5)
    │   └── Spirit Rating
    │       ├── Label
    │       └── Rating Buttons (1-5)
```

#### Notes Section
```
Card
├── Header
└── Content
    └── Textarea
```

#### Submit Section
```
Card
└── Content
    ├── Google Form Toggle
    └── Submit Button
```

---

## LAYOUT WIREFRAMES

### Full Version Wireframe

```
┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ Daily Report                    📋 Lịch sử      ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

[Max-width: 1000px, centered, py-8, px-4]

┌───────────────────────────────────────────────┐
│ Chọn dự án                                    │ [px-6 py-4]
├───────────────────────────────────────────────┤
│ [Logo 64x64] [Select            ▼]           │ [gap-4]
└───────────────────────────────────────────────┘
[space-y-6]

┌───────────────────────────────────────────────┐
│ Task ngày hôm nay                             │
│ Nhập các công việc...                         │
├───────────────────────────────────────────────┤
│ ≡ [Input] [50%] [2024-01-15] [✕]            │ [p-2]
│ ≡ [Input] [100%]                             │ [gap-2]
│ ≡ [Input] [30%] [2024-01-15] [✕]            │ [space-y-2]
│ [+ Thêm]                                      │
└───────────────────────────────────────────────┘
[space-y-6]

┌───────────────────────────────────────────────┐
│ Task ngày mai                                 │
├───────────────────────────────────────────────┤
│ ≡ [Input] [✕]                                │
│ ≡ [Input] [✕]                                │
│ [+ Thêm]                                      │
└───────────────────────────────────────────────┘
[space-y-6]

┌───────────────────────────────────────────────┐
│ Tự đánh giá                                   │
├──────────────────┬──────────────────────────┤
│ Chất lượng        │ Tinh thần               │
│ [1][2][3][4][5]  │ [1][2][3][4][5]        │
└──────────────────┴──────────────────────────┘
[space-y-6]

┌───────────────────────────────────────────────┐
│ Ghi chú                                       │
├───────────────────────────────────────────────┤
│ [Textarea min-h-24 rows-3]                   │
└───────────────────────────────────────────────┘
[space-y-6]

┌───────────────────────────────────────────────┐
│ Gửi tới Google Form  ☑ │  [Gửi Report]       │
└───────────────────────────────────────────────┘
```

### Compact Version Wireframe

```
[Max-width: 750px, py-4, px-3]

┌──────────────────────────────────┐
│ Chọn dự án                       │ [px-3 py-3]
├──────────────────────────────────┤
│ [Logo] [Select ▼]               │
└──────────────────────────────────┘
[space-y-3]

┌──────────────────────────────────┐
│ Task ngày hôm nay                │
├──────────────────────────────────┤
│ ≡[Input][50%][Date][✕]
│ ≡[Input][100%]
│ ≡[Input][30%][Date][✕]
│ [+ Thêm]                         │
└──────────────────────────────────┘
[space-y-3]

...similar structure...
[space-y-3 between all sections]
[p-1.5 on task rows]
[gap-1.5 on task rows]
```

---

## INSTALLATION & SETUP

### Step 1: Create Laravel Project

```bash
composer create-project laravel/laravel daily-report
cd daily-report
```

### Step 2: Install Dependencies

```bash
# Install Livewire
composer require livewire/livewire

# Install Tailwind CSS
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# Install SweetAlert2
npm install sweetalert2
```

### Step 3: Configure Tailwind

**File:** `tailwind.config.js`

```javascript
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./app/Livewire/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#EFF6FF',
          100: '#BFDBFE',
          500: '#3B82F6',
          600: '#2563EB',
          700: '#1D4ED8',
        },
      },
      fontFamily: {
        sans: ['Geist', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
}
```

### Step 4: Create Database Tables

```bash
php artisan make:migration create_projects_table
php artisan make:migration create_daily_reports_table
php artisan make:migration create_report_tasks_table

php artisan migrate
```

### Step 5: Create Livewire Component

```bash
php artisan make:livewire DailyReportForm
php artisan make:livewire LogworkHistory
```

### Step 6: Setup Routes

**File:** `routes/web.php`

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/daily-report', App\Livewire\DailyReportForm::class)->name('daily-report');
    Route::get('/logwork-history', App\Livewire\LogworkHistory::class)->name('logwork-history');
});
```

### Step 7: Run Development Servers

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Build frontend assets
npm run dev
```

---

## DEVELOPMENT GUIDELINES

### Code Organization

```
Components
├── Single Responsibility: Each component does one thing
├── Props/Parameters: Pass data clearly
├── Naming: descriptive (e.g., TaskInput, RatingButtons)
└── Reusability: Build composable components

CSS Classes
├── Always use Tailwind classes
├── Never hardcode colors/sizes
├── Use design tokens consistently
└── Keep media queries consistent

Livewire Methods
├── Use wire:model for two-way binding
├── Use wire:click for button actions
├── Use wire:submit for form submission
├── Keep logic in PHP, keep template clean
└── Use validation rules in component

Database
├── Keep data normalized
├── Use relationships (belongsTo, hasMany)
├── Add indexes for frequently queried columns
└── Soft delete if needed
```

### Tailwind Best Practices

```
Do:
✅ Use utility classes directly
✅ Use responsive prefixes (sm:, md:, lg:)
✅ Group related utilities
✅ Use @apply for repeated patterns
✅ Keep specificity low

Don't:
❌ Use inline styles
❌ Create custom CSS for Tailwind utilities
❌ Overuse arbitrary values ([w-123px])
❌ Mix Tailwind with other CSS frameworks
❌ Repeat long class strings
```

### Livewire Best Practices

```
Do:
✅ Use computed properties for derived state
✅ Use rules() for validation
✅ Use #[On('event')] for event listeners
✅ Keep component state minimal
✅ Use wire:loading for UX

Don't:
❌ Put business logic in templates
❌ Use too many reactive properties
❌ Dispatch too many events
❌ Make components do too much
❌ Ignore performance (use debounce)
```

---

## DEPLOYMENT CHECKLIST

- [ ] Set `.env` variables for production
- [ ] Run `php artisan config:cache`
- [ ] Run `npm run build` for production assets
- [ ] Migrate database: `php artisan migrate --force`
- [ ] Clear caches: `php artisan cache:clear`
- [ ] Test form submission and validation
- [ ] Test responsive design on mobile/tablet/desktop
- [ ] Verify Google Form integration (if needed)
- [ ] Setup database backups
- [ ] Configure error monitoring (Sentry, etc.)
- [ ] Test authentication and authorization
- [ ] Setup logging and monitoring

---

## QUICK REFERENCE - COMMON TAILWIND PATTERNS

### Task Row (Full)
```blade
<div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
```

### Task Row (Compact)
```blade
<div class="flex flex-col sm:flex-row gap-1.5 sm:gap-2 items-start sm:items-center p-1.5 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
```

### Input (Full)
```blade
<input class="w-full h-9 px-3 py-2 border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
```

### Input (Compact)
```blade
<input class="w-full h-8 px-3 py-1 border border-slate-200 rounded-md text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
```

### Card (Full Header)
```blade
<div class="px-6 py-4 border-b border-slate-200">
  <h2 class="text-xl font-semibold text-slate-900">Title</h2>
  <p class="text-sm text-slate-500 mt-1">Description</p>
</div>
```

### Button Primary
```blade
<button class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
```

### Button Secondary
```blade
<button class="px-4 py-2 bg-slate-100 text-slate-700 font-medium border border-slate-200 rounded-md hover:bg-slate-200 transition-colors">
```

---

**Generated:** 2026-04-25  
**Framework:** Laravel 11.x + Livewire 3.x  
**Status:** Complete & Production Ready

For questions or updates, refer to official documentation:
- [Laravel](https://laravel.com)
- [Livewire](https://livewire.laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [SweetAlert2](https://sweetalert2.github.io)
