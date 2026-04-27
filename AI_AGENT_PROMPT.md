# Daily Report System - AI Agent Implementation Prompt

## 📋 Project Brief

Bạn sẽ xây dựng một ứng dụng Daily Report (Báo cáo hàng ngày) bằng **Laravel 11** + **Livewire 3** + **Tailwind CSS v4** + **SweetAlert2**.

Tất cả chi tiết thiết kế (màu sắc, kích thước, spacing) đã được định nghĩa chi tiết. **Tuân thủ 100% spec để đảm bảo UI/UX giống hệt như tham chiếu.**

---

## 🎯 Hai File Chính:

1. **DAILY_REPORT_EXACT_SPEC.md** - Spec UI chi tiết (colors, sizes, spacing)
2. **LARAVEL_DAILY_REPORT_BUILD.md** - Hướng dẫn build Laravel Livewire

**Hãy đọc cả 2 file này trước khi bắt đầu.**

---

## 📁 Project Structure

```
laravel-daily-report/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── DailyReportController.php
│   │   └── Requests/
│   ├── Livewire/
│   │   ├── DailyReportForm.php
│   │   ├── DailyReportCompact.php
│   │   └── LogworkHistory.php
│   ├── Models/
│   │   ├── Project.php
│   │   ├── DailyReport.php
│   │   └── ReportTask.php
│   └── Casts/
├── database/
│   └── migrations/
│       ├── 2024_01_01_000000_create_projects_table.php
│       ├── 2024_01_01_000001_create_daily_reports_table.php
│       └── 2024_01_01_000002_create_report_tasks_table.php
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   ├── daily-report-form.blade.php
│   │   │   ├── logwork-history.blade.php
│   │   │   └── components/
│   │   │       ├── task-row.blade.php
│   │   │       ├── rating-buttons.blade.php
│   │   │       └── project-select.blade.php
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   └── welcome.blade.php
│   └── css/
│       └── app.css
├── routes/
│   └── web.php
├── public/
│   └── logos/
│       ├── jrr.jpg
│       ├── primas.jpg
│       ├── project-a.jpg
│       └── ... (10 logos total)
└── tailwind.config.js
```

---

## 🚀 Quick Start (8 Steps)

### Step 1: Setup Base Project
```bash
composer create-project laravel/laravel laravel-daily-report
cd laravel-daily-report
php artisan serve
```

### Step 2: Install Dependencies
```bash
composer require livewire/livewire
npm install
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
npm install sweetalert2
npm install sortablejs
```

### Step 3: Configure Tailwind
Edit `tailwind.config.js` - **copy full config từ LARAVEL_DAILY_REPORT_BUILD.md**

Edit `resources/css/app.css` - **copy full CSS từ LARAVEL_DAILY_REPORT_BUILD.md**

### Step 4: Create Database Tables
```bash
php artisan migrate
```
**Database migrations code từ LARAVEL_DAILY_REPORT_BUILD.md**

### Step 5: Create Models
**Copy code từ LARAVEL_DAILY_REPORT_BUILD.md:**
- `app/Models/Project.php`
- `app/Models/DailyReport.php`
- `app/Models/ReportTask.php`

### Step 6: Create Livewire Components
**Copy code từ LARAVEL_DAILY_REPORT_BUILD.md:**
- `app/Livewire/DailyReportForm.php`
- `app/Livewire/LogworkHistory.php`

### Step 7: Create Blade Templates
**Copy code từ LARAVEL_DAILY_REPORT_BUILD.md:**
- `resources/views/livewire/daily-report-form.blade.php`
- `resources/views/livewire/logwork-history.blade.php`
- `resources/views/layouts/app.blade.php`

### Step 8: Add Routes
**Copy code từ LARAVEL_DAILY_REPORT_BUILD.md vào `routes/web.php`**

---

## 🎨 Design System (From DAILY_REPORT_EXACT_SPEC.md)

### Colors - Copy Exact Hex Codes

**Background:**
- Page: `#F8FAFC` (bg-slate-50)
- Card: `#FFFFFF` (bg-white)
- Hover: `#F8FAFC` (hover:bg-slate-50)

**Text:**
- Primary: `#0F172A` (text-slate-900)
- Secondary: `#475569` (text-slate-600)
- Tertiary: `#78716C` (text-slate-500)
- Disabled: `#CBD5E1` (text-slate-300)

**Interactive:**
- Primary Button: `#2563EB` (bg-blue-600)
- Primary Hover: `#1D4ED8` (hover:bg-blue-700)
- Delete: `#DC2626` (text-red-600)
- Delete Hover: `#B91C1C` (hover:text-red-700)
- Input Border: `#E2E8F0` (border-slate-200)
- Input Focus: `#3B82F6` (border-blue-500)

### Typography - Exact Sizes

**Headings:**
- Page Title: `text-3xl font-bold` (1.875rem, 700 weight)
- Card Title: `text-xl font-semibold` (1.25rem, 600 weight)
- Card Description: `text-sm text-slate-500` (0.875rem)

**Body:**
- Input Text: `text-sm` (0.875rem)
- Labels: `text-sm font-medium` (0.875rem, 500 weight)
- Placeholder: `text-slate-400` (0.875rem)

**Font Family:** Geist / Inter / System Font

### Spacing & Sizing - Exact Values

**Cards:**
- Header Padding: `px-6 py-4` (24px × 16px)
- Content Padding: `px-6 py-4` (24px × 16px)
- Gap Between: `space-y-6` (24px)

**Inputs:**
- Height: `h-9` (36px)
- Padding: `px-3 py-2` (12px × 8px)
- Border: `1px solid #E2E8F0`
- Border Radius: `rounded-md` (6px)

**Task Rows:**
- Padding: `p-2` (8px all)
- Gap: `gap-3` (12px)
- Hover: `hover:bg-slate-50`
- Border Radius: `rounded-md` (6px)

**Progress Select:**
- Width: `w-20` (80px)
- Height: `h-9` (36px)

**Date Input:**
- Width: `w-32` (128px)
- Height: `h-9` (36px)

**Buttons:**
- Primary: `h-10 px-6 py-2.5` (40px height)
- Secondary: `h-9 px-4 py-2` (36px height)
- Icon: `h-9 w-9` (36×36px)
- Small: `h-8 px-3 py-1` (32px height)

**Textarea:**
- Min Height: `min-h-24` (96px)
- Rows: `3`
- Padding: `px-3 py-2`

**Logo:**
- Size: `w-16 h-16` (64×64px)
- Border Radius: `rounded-lg` (8px)

### Layout - Grid & Flex

**Main Container:**
- Max Width: `max-w-4xl` (896px)
- Padding: `px-4 py-8` (16px × 32px)
- Centered: `mx-auto`
- Background: `bg-slate-50`

**Task Row:**
```
flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2
Mobile: column stack
Desktop: row with gap-3
```

**Rating Buttons:**
```
grid grid-cols-2 gap-6 (or grid-cols-1 for single rating)
Mobile: 1 column
Desktop: 2 columns for quality + spirit side-by-side
```

**Form Sections:**
```
space-y-6 (24px gap between sections)
```

---

## 📝 Feature Requirements

### Page 1: Daily Report Form (`/daily-report`)

**Section 1: Project Selection**
- Dropdown select từ 10 projects
- Display logo 64×64px next to select
- Default: JRR
- Styling: Card with header

**Section 2: Today's Tasks (Task ngày hôm nay)**
- Repeater component (allow 1-20 tasks)
- Mỗi task row có:
  - Drag handle (GripVertical icon) - ≡
  - Input: Short description
  - Select: Progress (0%, 10%, 20%, ..., 100%)
  - Input: Expected completion date (chỉ show nếu progress < 100%)
  - Button: Delete (chỉ show nếu > 1 task, ẩn theo hover)
- Button: Add more task
- Spacing: space-y-2 (8px) giữa tasks

**Section 3: Tomorrow's Tasks (Task dự kiến ngày mai)**
- Repeater component
- Mỗi task chỉ cần:
  - Drag handle (≡)
  - Input: Description
  - Button: Delete (nếu > 1)
- Button: Add more task
- Spacing: space-y-2

**Section 4: Self Evaluation (Tự đánh giá)**
- 2 rating groups:
  - Chất lượng công việc (Quality)
  - Tinh thần làm việc (Spirit)
- Mỗi group: 5 buttons (1, 2, 3, 4, 5)
- Button styling: Blue when selected, white when not
- Layout: Grid 2 columns (side-by-side desktop)
- Heights: h-10 (40px)

**Section 5: Notes (Ghi chú)**
- Textarea (min-h-24, rows-3)
- Placeholder: "Nhập ghi chú (tùy chọn)..."
- Optional field
- Styling: Like inputs, focus states

**Section 6: Submit**
- Toggle: "Gửi tới Google Form" (default ON)
- Button: "Gửi Report" (text-white bg-blue-600)
- Show SweetAlert2 success message after submit
- Store to database

### Page 2: Logwork History (`/logwork-history`)
- Table or list của past reports
- Columns: Date, Project, Tasks count, Quality, Spirit, Actions
- Filter by project
- Sort by date (desc)
- View/Edit/Delete actions

---

## 🔧 Technical Details

### Livewire Component Structure

```php
// app/Livewire/DailyReportForm.php
class DailyReportForm extends Component {
    public string $selectedProject = 'JRR';
    public array $todayTasks = [];
    public array $tomorrowTasks = [];
    public int $qualityRating = 3;
    public int $spiritRating = 3;
    public string $notes = '';
    public bool $submitToGForm = true;
    
    // Validation rules
    protected function rules() { ... }
    
    // Add/remove/update task methods
    public function addTodayTask() { ... }
    public function removeTodayTask($id) { ... }
    public function updateTodayTask($id, $field, $value) { ... }
    
    // Similar for tomorrow tasks
    
    // Drag-drop handling
    public function reorderTasks($tasks, $type) { ... }
    
    // Submit
    public function submitReport() { ... }
    
    // Render
    public function render() { ... }
}
```

### Database Schema

**projects table:**
- id, name, logo_path, color_hex, created_at, updated_at

**daily_reports table:**
- id, user_id, project_id, quality_rating, spirit_rating, notes, submit_to_gform, created_at, updated_at

**report_tasks table:**
- id, daily_report_id, type (today/tomorrow), description, progress, expected_date, order, created_at, updated_at

### Blade Template Pattern

```blade
<div class="space-y-6">
    {{-- Project Selection --}}
    @livewire('components.project-select', ['selected' => $selectedProject])
    
    {{-- Today Tasks --}}
    @livewire('components.task-repeater', [
        'tasks' => $todayTasks,
        'type' => 'today'
    ])
    
    {{-- Tomorrow Tasks --}}
    @livewire('components.task-repeater', [
        'tasks' => $tomorrowTasks,
        'type' => 'tomorrow'
    ])
    
    {{-- Self Evaluation --}}
    @livewire('components.rating-buttons', [
        'qualityRating' => $qualityRating,
        'spiritRating' => $spiritRating
    ])
    
    {{-- Notes --}}
    <x-card>
        <x-card.header>
            <x-card.title>Ghi chú</x-card.title>
        </x-card.header>
        <x-card.content>
            <textarea wire:model="notes" class="w-full px-3 py-2 border border-slate-200 rounded-md"></textarea>
        </x-card.content>
    </x-card>
    
    {{-- Submit --}}
    <div class="flex justify-between items-center">
        <label class="flex items-center gap-2">
            <input type="checkbox" wire:model="submitToGForm">
            Gửi tới Google Form
        </label>
        <button wire:click="submitReport" class="bg-blue-600 text-white px-6 py-2.5 rounded-md">
            Gửi Report
        </button>
    </div>
</div>
```

---

## ✅ Implementation Checklist

**Database:**
- [ ] Create projects table + seed 10 projects with logos
- [ ] Create daily_reports table
- [ ] Create report_tasks table
- [ ] Create Project model with relationships
- [ ] Create DailyReport model
- [ ] Create ReportTask model

**Livewire:**
- [ ] Create DailyReportForm component
- [ ] Implement add/remove/update task methods
- [ ] Implement reorder task method (drag-drop)
- [ ] Implement validation
- [ ] Implement submitReport method

**Blade Templates:**
- [ ] Create daily-report-form.blade.php
- [ ] Create task-row component
- [ ] Create rating-buttons component
- [ ] Create project-select component
- [ ] Create logwork-history.blade.php

**Styling:**
- [ ] Configure Tailwind v4
- [ ] Add custom CSS components
- [ ] Apply colors (exact hex codes)
- [ ] Apply typography (exact sizes)
- [ ] Apply spacing (exact values)
- [ ] Test responsive (mobile/tablet/desktop)

**Features:**
- [ ] Drag-drop reorder tasks (SortableJS)
- [ ] SweetAlert2 success/error messages
- [ ] Google Form integration (optional)
- [ ] Form validation with error messages
- [ ] Task progress states
- [ ] Expected date conditional display

**Testing:**
- [ ] Add 10+ tasks, verify layout
- [ ] Test drag-drop on both task types
- [ ] Test date picker (shows only when progress < 100%)
- [ ] Test rating selection (both quality & spirit)
- [ ] Test form submission
- [ ] Verify colors match exact hex codes
- [ ] Verify sizes match exact pixel values
- [ ] Test on mobile/tablet/desktop

**Deployment:**
- [ ] Database migrations ready
- [ ] Environment variables set
- [ ] Assets compiled (npm run build)
- [ ] No console errors
- [ ] All routes working

---

## 📚 Reference Files Included

1. **DAILY_REPORT_EXACT_SPEC.md** - Complete UI specification
   - Color system (hex codes)
   - Typography (sizes, weights)
   - Spacing (padding, margin, gaps)
   - Component specs
   - Wireframes

2. **LARAVEL_DAILY_REPORT_BUILD.md** - Implementation guide
   - Tech stack versions
   - Database migrations
   - Eloquent models
   - Livewire components
   - Blade templates
   - Tailwind config
   - Installation steps

---

## 🎯 Success Criteria

✅ UI matches spec exactly (colors, sizes, spacing)
✅ All features working (add/remove/reorder/submit)
✅ Responsive design (mobile/tablet/desktop)
✅ Database integration (CRUD operations)
✅ SweetAlert2 notifications
✅ Drag-drop functionality
✅ Form validation
✅ No console errors
✅ Performance optimized
✅ Code clean & documented

---

## 📞 Questions to Clarify

If anything is unclear, refer to:
1. **DAILY_REPORT_EXACT_SPEC.md** for visual/design details
2. **LARAVEL_DAILY_REPORT_BUILD.md** for implementation details
3. Existing code examples in both files

**Do NOT deviate from the spec. Maintain 100% consistency.**

Good luck! 🚀
