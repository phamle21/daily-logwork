# Daily Report Application - Detailed UI/UX Prompt

## Project Overview

Build a Daily Report application with two versions: Full and Compact. The application allows users to track daily tasks, plan for tomorrow, self-evaluate, and optionally submit to Google Forms.

**Tech Stack:** Next.js 16 (App Router), React, TypeScript, Tailwind CSS, shadcn/ui

---

## Routes Structure

| Route              | Purpose                     | Version |
| ------------------ | --------------------------- | ------- |
| `/`                | Main daily report form      | Full    |
| `/compact`         | Condensed daily report form | Compact |
| `/logwork-history` | Historical reports view     | Full    |

---

## Data Models

### Project

```typescript
const PROJECTS = [
    "JRR",
    "Primas",
    "Project A",
    "Project B",
    "Project C",
    "Project D",
    "Project E",
    "Project F",
    "Project G",
    "Project H",
];

const PROJECT_LOGOS: Record<string, string> = {
    JRR: "/logos/jrr.jpg",
    Primas: "/logos/primas.jpg",
    // ... etc for all projects
};
```

### Task Models

```typescript
interface TodayTask {
  id: string
  description: string
  progress: number (0, 10, 20, ..., 100)
  expectedDate?: string (ISO date string, only if progress < 100)
}

interface TomorrowTask {
  id: string
  description: string
}

interface ReportData {
  project: string
  date: string (localized date)
  todayTasks: TodayTask[]
  tomorrowTasks: TomorrowTask[]
  qualityRating: number (1-5)
  spiritRating: number (1-5)
  notes: string (optional)
  submitToGForm: boolean
  submittedAt: string (ISO timestamp)
}
```

### Rating Scale

- 1: Rất kém (Very Poor)
- 2: Kém (Poor)
- 3: Bình thường (Normal)
- 4: Tốt (Good)
- 5: Rất tốt (Very Good)

---

## FULL VERSION UI (/page.tsx)

### 1. Header Section

- **Layout:** Flex row, space-between
- **Content:**
    - Left: Page title "Daily Report" (text-3xl font-bold)
    - Right: Link button to logwork history with HistoryIcon + text "Lịch sử" (py-2 px-4)
- **Styling:** bg-white, border-b, shadow-sm

### 2. Main Container

- **Layout:** Grid layout, centered
- **Max Width:** 1000px (max-w-4xl)
- **Padding:** px-4 py-8
- **Background:** bg-slate-50

### 3. Form Sections (in order)

#### 3.1 Project Selection Card

- **Structure:**
    - Card with header "Chọn dự án"
    - Content: flex row with gap-4
        - Logo: 64x64px image, rounded-lg, project logo image
        - Select dropdown: flex-1, full width
            - Show all PROJECTS in SelectContent
            - Selected value shows project name

#### 3.2 Today's Tasks Section

- **Card Title:** "Task ngày hôm nay"
- **Card Description:** "Nhập các công việc đã làm hôm nay với tiến độ"
- **Content:** Repeater list
    - **Each Task Row:**
        - Flex row (responsive: flex-col on sm, flex-row on sm+)
        - Gap: 2-3 units
        - Padding: p-2, hover:bg-slate-50, rounded-md
        - Cursor: move
        - Components:
            1. **Drag Handle Icon:** GripVertical (4x4), text-slate-400, hover:text-slate-600
            2. **Description Input:**
                - Placeholder: "Mô tả..."
                - flex-1
                - Height: h-9, text-sm
                - Responds to onUpdateTask with 'description'
            3. **Progress Select:**
                - Options: 0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100
                - Width: w-20
                - Height: h-9
                - Text: sm
                - Shows "XX%" in trigger
                - Responds to onUpdateTask with 'progress'
            4. **Expected Date Input (conditional):**
                - Only shows if progress < 100
                - Type: date
                - Width: w-32
                - Height: h-9
                - Text: sm
                - Responds to onUpdateTask with 'expectedDate'
            5. **Delete Button (conditional):**
                - Only shows if tasks.length > 1
                - Icon: Trash2 (4x4)
                - Opacity: 0 on normal, 100 on group-hover
                - Colors: text-red-600, hover:text-red-700, hover:bg-red-50
                - Hidden until hover

    - **Add Task Button:**
        - Type: button, variant: outline, w-full
        - Icon: Plus (3x3)
        - Text: "Thêm"
        - Margin-top: mt-2
        - Size: sm

#### 3.3 Tomorrow's Tasks Section

- **Card Title:** "Task dự kiến ngày mai"
- **Card Description:** "Lên kế hoạch cho ngày mai"
- **Content:** Repeater list
    - **Each Task Row:**
        - Flex row, gap-2, p-2, items-center
        - hover:bg-slate-50, rounded-md
        - Cursor: move
        - Components:
            1. **Drag Handle Icon:** GripVertical (4x4), text-slate-400, hover:text-slate-600
            2. **Description Input:**
                - Placeholder: "Mô tả..."
                - flex-1
                - Height: h-9
                - Text: sm
            3. **Delete Button (conditional):**
                - Only if tasks.length > 1
                - Same styling as Today tasks

    - **Add Task Button:**
        - Same as Today's section

#### 3.4 Self Evaluation Section

- **Card Title:** "Tự đánh giá"
- **Card Description:** "Đánh giá chất lượng công việc và tinh thần"
- **Content:** Grid 2 columns (on md+)
    - **Column 1: Quality Rating**
        - Label: "Chất lượng công việc"
        - Display: flex gap-2, justify-center
        - Rating buttons (5): numbered 1-5
            - Each button: variant-outline initially, variant-default when selected
            - Width: flex-1
            - Rounded corners
            - Responds to setQualityRating

    - **Column 2: Spirit Rating**
        - Label: "Tinh thần làm việc"
        - Same layout as Quality Rating
        - Responds to setSpiritRating

#### 3.5 Notes Section

- **Card Title:** "Ghi chú"
- **Card Description:** "Thêm ghi chú tùy chọn"
- **Content:**
    - Textarea
    - Placeholder: "Nhập ghi chú (tùy chọn)..."
    - Min rows: 3
    - Responds to setNotes

#### 3.6 Submit Section

- **Layout:** Flex row, space-between, items-center
- **Left Side:**
    - Label: "Gửi tới Google Form"
    - Switch component
    - Default: on (true)
    - Responds to setSubmitToGForm

- **Right Side:**
    - Submit button
    - Type: submit
    - Variant: default
    - Size: lg
    - Text: "Gửi Report"
    - Class: bg-blue-600 hover:bg-blue-700

---

## COMPACT VERSION UI (/compact/page.tsx)

Same structure as Full version with these spacing/size adjustments:

- **Card Padding:** p-3 instead of p-6
- **Sections Gap:** space-y-3 instead of space-y-6
- **Task Row Padding:** p-1.5 instead of p-2
- **Inputs Height:** h-8 instead of h-9 (exceptions: date stays similar)
- **Font Sizes:**
    - Heading: text-2xl instead of text-3xl
    - Card title: text-lg instead of text-xl
    - Input/select text: text-xs instead of text-sm
- **Gaps:** Reduce gap-3 to gap-2 throughout
- **Self Evaluation:** Single column grid (not 2 columns)
- **Notes + Submit:** Flex column layout (not grid)

---

## ASSETS & STYLING

### Logo Assets (in `/public/logos/`)

Generate 10 professional minimal logos (64x64px, square format):
| Project | Colors | Style |
|---------|--------|-------|
| jrr.jpg | Blue (#3B82F6) + White | Modern geometric with rounded corners |
| primas.jpg | Purple (#A855F7) + White | Elegant minimalist |
| project-a.jpg | Green (#10B981) + White | Clean tech style |
| project-b.jpg | Orange (#F97316) + White | Warm & vibrant |
| project-c.jpg | Red (#EF4444) + White | Bold & energetic |
| project-d.jpg | Pink (#EC4899) + White | Playful modern |
| project-e.jpg | Cyan (#06B6D4) + White | Fresh & digital |
| project-f.jpg | Indigo (#6366F1) + White | Professional tech |
| project-g.jpg | Teal (#14B8A6) + White | Balanced & calm |
| project-h.jpg | Amber (#F59E0B) + White | Warm professional |

### Complete Color System

#### FULL VERSION

- **Page Background:** #F8FAFC (bg-slate-50) - Light, clean background
- **Card Background:** #FFFFFF (bg-white) - Pure white for content
- **Card Border:** #E2E8F0 (border-slate-200, border: 1px)
- **Card Shadow:** shadow-sm (0 1px 2px rgba(0,0,0,0.05))

#### Text Colors

- **Primary Text:** #0F172A (text-slate-900) - Dark charcoal for readability
- **Secondary Text:** #475569 (text-slate-600) - Medium gray for labels
- **Tertiary Text:** #78716C (text-slate-500) - Light gray for hints
- **Disabled Text:** #CBD5E1 (text-slate-300) - Very light for disabled states

#### Interactive Elements

- **Input Background:** #FFFFFF (bg-white)
- **Input Border:** #E2E8F0 (border-slate-200, border: 1px)
- **Input Focus Border:** #3B82F6 (border-blue-500, border: 2px)
- **Input Hover:** hover:border-slate-300

#### Buttons & Actions

- **Primary Button Background:** #2563EB (bg-blue-600)
- **Primary Button Hover:** #1D4ED8 (hover:bg-blue-700)
- **Primary Button Text:** #FFFFFF (text-white)
- **Secondary Button Background:** #F1F5F9 (bg-slate-100)
- **Secondary Button Hover:** #E2E8F0 (hover:bg-slate-200)
- **Secondary Button Text:** #334155 (text-slate-700)

#### Status Colors

- **Delete/Warning:** #DC2626 (text-red-600)
- **Delete Hover:** #B91C1C (hover:text-red-700)
- **Delete Background Hover:** #FEE2E2 (hover:bg-red-50)
- **Success:** #16A34A (text-green-600)
- **Info:** #0EA5E9 (text-cyan-500)

#### Hover States

- **Default Hover Background:** #F1F5F9 (hover:bg-slate-100)
- **Subtle Hover:** #F8FAFC (hover:bg-slate-50)
- **Transition:** transition-colors duration-200 (smooth)

### Typography Scale

#### Heading Hierarchy (Full Version)

- **Page Title:**
    - Font size: 1.875rem (text-3xl)
    - Font weight: 700 (font-bold)
    - Line height: 2.25rem (leading-9)
    - Color: #0F172A (text-slate-900)
    - Letter spacing: -0.01em (tracking-tight)

- **Section Headers (Card Titles):**
    - Font size: 1.25rem (text-xl)
    - Font weight: 600 (font-semibold)
    - Line height: 1.75rem (leading-7)
    - Color: #0F172A (text-slate-900)
    - Margin bottom: 0.5rem (mb-2)

- **Card Descriptions:**
    - Font size: 0.875rem (text-sm)
    - Font weight: 400 (font-normal)
    - Color: #64748B (text-slate-500)
    - Margin bottom: 1rem (mb-4)

#### Body Text (Full Version)

- **Input/Select/Textarea Text:**
    - Font size: 0.875rem (text-sm)
    - Font weight: 400 (font-normal)
    - Line height: 1.25rem (leading-5)
    - Color: #0F172A (text-slate-900)

- **Labels:**
    - Font size: 0.875rem (text-sm)
    - Font weight: 500 (font-medium)
    - Color: #334155 (text-slate-700)
    - Margin bottom: 0.5rem (mb-2)

- **Placeholder Text:**
    - Font size: 0.875rem (text-sm)
    - Color: #A0AEC0 (text-slate-400)
    - Font style: italic

#### Heading Hierarchy (Compact Version)

- **Page Title:** text-2xl (1.5rem) instead of text-3xl
- **Section Headers:** text-lg (1.125rem) instead of text-xl
- **Card Descriptions:** text-xs (0.75rem) instead of text-sm

#### Body Text (Compact Version)

- **Input/Select/Textarea:** text-xs (0.75rem) instead of text-sm
- **Labels:** text-xs instead of text-sm

### Spacing & Layout

#### FULL VERSION - Padding & Margins

- **Page Container:**
    - Padding X: px-4 (1rem)
    - Padding Y: py-8 (2rem)
    - Max width: max-w-4xl (56rem / 896px)
    - Centered: mx-auto

- **Cards:**
    - Header Padding: px-6 py-4 (1.5rem, 1rem)
    - Content Padding: px-6 py-4 (1.5rem, 1rem)
    - Margin between cards: gap-6 (1.5rem)

- **Sections:**
    - Grid gap: space-y-6 (1.5rem vertical spacing)
    - Task repeater gap: space-y-2 (0.5rem)
    - Task row padding: p-2 (0.5rem all sides)
    - Task row gap: gap-2 sm:gap-3 (0.5rem - mobile, 0.75rem - desktop)

- **Form Elements:**
    - Label margin bottom: mb-2 (0.5rem)
    - Input margin bottom: mb-3 (0.75rem)
    - Between sections: my-4 (1rem top & bottom)

#### COMPACT VERSION - Adjusted Spacing

- **Page Container:** px-3 py-4 (smaller padding)
- **Cards:** px-3 py-3 (reduce from 6/4)
- **Sections Gap:** space-y-3 (0.75rem instead of 1.5rem)
- **Task repeater:**
    - Gap: space-y-1.5 (instead of space-y-2)
    - Row padding: p-1.5 (instead of p-2)
    - Row gap: gap-1.5 (instead of gap-2/3)

#### Component Sizing

- **Input Fields:**
    - Full Version: h-9 (2.25rem height)
    - Compact Version: h-8 (2rem height)
    - Border radius: rounded-md (0.375rem)
    - Border width: 1px

- **Select Dropdowns:**
    - Full Version: h-9 (2.25rem)
    - Compact Version: h-8 (2rem)
    - Width: w-20 (5rem) for progress select

- **Buttons:**
    - Default: h-10 (2.5rem)
    - Small: h-9 (2.25rem)
    - Compact: h-8 (2rem)
    - Icon buttons: w-9 h-9 (9 x 9 units)
    - Border radius: rounded-md (0.375rem)

- **Textarea:**
    - Min height: min-h-24 (6rem / 96px)
    - Full Version: rows-3
    - Compact Version: rows-2

- **Logo Display:**
    - Width: w-16 (4rem)
    - Height: h-16 (4rem)
    - Border radius: rounded-lg (0.5rem)
    - Object fit: object-cover

#### Icons

- **Standard Icons:**
    - Small: w-3 h-3 (0.75rem)
    - Medium: w-4 h-4 (1rem)
    - Large: w-5 h-5 (1.25rem)
    - Grid icon: w-4 h-4 (GripVertical drag handle)

- **Icon Colors:**
    - Default: text-slate-600
    - Hover: text-slate-800
    - Disabled: text-slate-300
    - Delete: text-red-600
    - Delete hover: text-red-700

### Border & Spacing Details

#### Cards

- Border: 1px solid #E2E8F0
- Border radius: rounded-lg (0.5rem)
- Background: #FFFFFF
- Shadow: shadow-sm (0 1px 2px rgba(0,0,0,0.05))
- Margin between: 1.5rem (full) / 0.75rem (compact)

#### Inputs & Selects

- Border: 1px solid #E2E8F0
- Border radius: rounded-md (0.375rem)
- Focus: border-2 #3B82F6, ring: 2px #BFDBFE
- Background: #FFFFFF
- Transition: all 200ms ease

#### Task Rows

- Border: none
- Background on hover: #F8FAFC (full), #F1F5F9 (compact)
- Border radius: rounded-md
- Padding: p-2 (full) / p-1.5 (compact)
- Transition: all duration-200

### Font Family

- **Sans-serif (default):** Geist, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto
- **Mono (if needed):** Geist Mono, Fira Code, Courier New
- **Line height:** leading-relaxed (1.625 / 1.5-1.6)

---

## INTERACTIONS & BEHAVIORS

### Task Management

- **Add Task:** Append new task with generated ID (Date.now().toString())
- **Update Task:** Update field in task object
- **Delete Task:** Remove task from array (only if count > 1)
- **Reorder Tasks:** Drag handle icon on each task (visual only - implement drag-and-drop)

### Validation

- **Progress Field:** Must be from 0-100 in 10% increments
- **Expected Date:** Only required if progress < 100
- **Ratings:** Must be 1-5
- **Submit:** All sections can be submitted, notes optional

### Form Submission

- Collect all form data
- Log to console: `console.log('Report submitted:', reportData)`
- If submitToGForm is true: Send data to Google Form (endpoint to be configured)
- Clear form or redirect to success page
- Timestamp: Include submittedAt as ISO string

---

## RESPONSIVE BEHAVIOR & LAYOUT DETAILS

### Full Version Layout Structure

#### Header Bar (Always Visible)

```
┌─────────────────────────────────────┐
│ Daily Report          📋 Lịch sử    │  bg-white, border-b, shadow-sm
└─────────────────────────────────────┘
```

#### Main Container

```
Max Width: 1000px (max-w-4xl)
Padding: px-4 (1rem), py-8 (2rem)
Background: bg-slate-50
Centered: mx-auto
```

#### Project Selection Card

```
┌─────────────────────────────────────┐
│ Chọn dự án                          │
├─────────────────────────────────────┤
│ [Logo] [Select Dropdown             │
│ 64x64  flex-1]                      │
└─────────────────────────────────────┘
Layout: flex flex-row items-center gap-4
Card padding: px-6 py-4
```

#### Task Sections Layout

```
┌─────────────────────────────────────┐
│ Task ngày hôm nay                   │
│ Nhập các công việc...               │
├─────────────────────────────────────┤
│ ≡ [Input] [Progress▼] [Date] [X]   │  Each row: flex, gap-2/3, p-2
│ ≡ [Input] [Progress▼] [Date] [X]   │  hover:bg-slate-50
│ ≡ [Input] [Progress▼] [Date] [X]   │
├─────────────────────────────────────┤
│ [+ Thêm]                            │
└─────────────────────────────────────┘
```

#### Self Evaluation (Full Version)

```
┌─────────────────────────────────────┐
│ Tự đánh giá                         │
├──────────────────┬──────────────────┤
│ Chất lượng       │ Tinh thần        │
│                 │                  │
│ [1][2][3][4][5] │ [1][2][3][4][5] │
│ flex grid-cols-2│ flex grid-cols-2 │
└──────────────────┴──────────────────┘
```

#### Notes + Submit (Full Version)

```
┌─────────────────────────────────────┐
│ Ghi chú                             │
├─────────────────────────────────────┤
│ [Textarea min-h-24                 │
│  rows-3]                            │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ Gửi tới Google Form │ ⊙ │          │
│                    [Gửi Report]     │
└─────────────────────────────────────┘
Layout: flex flex-row justify-between items-center
```

### Mobile Breakpoint (< 640px, max-w-full)

- **Task Rows:**
    - Layout: flex-col (stack vertically)
    - Drag handle margin: mt-1 (shift down slightly)
    - All inputs: full width
    - Progress select: w-full (instead of w-20)
    - Date input: w-full (instead of w-32)
    - Gap between: gap-2 (reduced)

- **Self Evaluation (Mobile):**
    - Layout: grid-cols-1 (single column instead of 2)
    - Each rating: full width
    - Margin between: space-y-4

- **Form Controls:**
    - Select dropdowns: w-full
    - Buttons: w-full (except icons)
    - Textarea: w-full

- **Project Selection:**
    - Layout: flex-col (stack logo above select)
    - Logo: centered, mx-auto
    - Select: w-full

### Tablet Breakpoint (640px - 1024px, sm:)

- **Task Rows:**
    - Layout: flex-row (horizontal)
    - Items center: sm:items-center
    - Gap: sm:gap-3
    - Responsive widths start here

- **Self Evaluation (Tablet):**
    - Layout: grid-cols-2 (two columns)
    - Each column: flex-1

- **Project Selection:**
    - Layout: flex-row items-center
    - Logo: w-16 h-16
    - Select: flex-1

### Desktop Breakpoint (> 1024px, md:, lg:)

- **Full Layout:** As specified in main design
- **Task Rows:** Optimized spacing with full widths
- **Grid Layouts:** Two-column where specified
- **Padding:** Full padding applied

---

## FLEXBOX & GRID PATTERNS

### Pattern 1: Task Row (Today/Tomorrow)

```tsx
<div className="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move">
    {/* Flex children align properly */}
    {/* Mobile: column, stacked | Desktop: row, aligned */}
</div>
```

### Pattern 2: Self Evaluation Two-Column (Full Only)

```tsx
<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
    {/* Mobile: single column | Desktop: two columns */}
    {/* Responsive gap: gap-6 full, gap-3 compact */}
</div>
```

### Pattern 3: Submit Section

```tsx
<div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-4 bg-white rounded-lg border border-slate-200">
    {/* Mobile: column stack | Desktop: row space-between */}
</div>
```

### Pattern 4: Card Layout

```tsx
<Card className="bg-white border border-slate-200 shadow-sm">
    <CardHeader className="px-6 py-4 border-b border-slate-200">
        <CardTitle className="text-xl font-semibold text-slate-900">
            ...
        </CardTitle>
        <CardDescription className="text-sm text-slate-500 mt-1">
            ...
        </CardDescription>
    </CardHeader>
    <CardContent className="px-6 py-4">{/* Content here */}</CardContent>
</Card>
```

### Pattern 5: Rating Buttons

```tsx
<div className="flex gap-2 justify-center">
    {[1, 2, 3, 4, 5].map((rating) => (
        <Button
            key={rating}
            variant={qualityRating === rating ? "default" : "outline"}
            className="flex-1 h-10 rounded-md"
        >
            {rating}
        </Button>
    ))}
</div>
```

---

## COMPACT VERSION - SPECIFIC LAYOUT CHANGES

### Grid & Flexbox Adjustments

```diff
- Main container: max-w-4xl → max-w-3xl (adjust if needed)
- Card header padding: px-6 py-4 → px-3 py-3
- Card content padding: px-6 py-4 → px-3 py-3
- Card gaps: space-y-6 → space-y-3
- Task row padding: p-2 → p-1.5
- Task row gap: gap-2 sm:gap-3 → gap-1.5 sm:gap-2
- Task repeater gap: space-y-2 → space-y-1.5
- Self Evaluation: grid-cols-2 → grid-cols-1 (single column)
- Self Evaluation gap: gap-6 → gap-3
- Rating buttons: h-10 → h-8
- Button padding: px-4 py-2 → px-3 py-1
```

### Full Comparison Table

| Element           | Full      | Compact   |
| ----------------- | --------- | --------- |
| Card padding      | px-6 py-4 | px-3 py-3 |
| Section gap       | space-y-6 | space-y-3 |
| Task row padding  | p-2       | p-1.5     |
| Task row gap      | gap-2/3   | gap-1.5/2 |
| Input height      | h-9       | h-8       |
| Font size (sm)    | text-sm   | text-xs   |
| Header (page)     | text-3xl  | text-2xl  |
| Card title        | text-xl   | text-lg   |
| Self Eval columns | 2 (md)    | 1         |

---

## INPUT STATES & INTERACTIONS

### Text Inputs (Description, Notes)

- **Default State:**
    - Border: 1px solid #E2E8F0
    - Background: #FFFFFF
    - Text color: #0F172A
    - Placeholder color: #A0AEC0

- **Focus State:**
    - Border: 2px solid #3B82F6
    - Background: #FFFFFF
    - Outline: ring-2 ring-blue-100 ring-offset-0
    - Transition: all 200ms ease

- **Hover State (unfocused):**
    - Border: 1px solid #CBD5E1
    - Background: #FFFFFF

- **Disabled State:**
    - Border: 1px solid #E2E8F0
    - Background: #F1F5F9
    - Text color: #94A3B8
    - Cursor: not-allowed

- **Error State (if needed):**
    - Border: 2px solid #DC2626
    - Background: #FEE2E2
    - Ring: ring-2 ring-red-100

### Select Dropdowns (Progress, Projects)

- **Closed State (Default):**
    - Border: 1px solid #E2E8F0
    - Background: #FFFFFF
    - Text: #0F172A
    - Height: 2.25rem (h-9, full) / 2rem (h-8, compact)
    - Padding: px-3 py-2

- **Focus/Open State:**
    - Border: 2px solid #3B82F6
    - Ring: ring-2 ring-blue-100
    - Background: #FFFFFF

- **Hover State:**
    - Border: 1px solid #CBD5E1

- **Content Options:**
    - Background: #FFFFFF
    - Item padding: px-2 py-1.5
    - Item hover: bg-slate-100
    - Item selected: bg-blue-50 text-blue-900
    - Separator: border-t 1px #E2E8F0

### Buttons

#### Primary Submit Button

- **Default:**
    - Background: #2563EB (bg-blue-600)
    - Text: #FFFFFF
    - Padding: px-6 py-3 (lg size)
    - Border radius: rounded-md
    - Font weight: font-medium

- **Hover:**
    - Background: #1D4ED8 (hover:bg-blue-700)
    - Cursor: pointer
    - Transition: all 150ms ease

- **Focus:**
    - Outline: ring-2 ring-blue-500 ring-offset-2
    - Background: #1D4ED8

- **Active/Pressed:**
    - Background: #1E40AF (bg-blue-800)
    - Transform: scale-98

#### Secondary Buttons (Add, Remove)

- **Default:**
    - Background: #F1F5F9 (bg-slate-100)
    - Text: #334155 (text-slate-700)
    - Border: 1px solid #E2E8F0

- **Hover:**
    - Background: #E2E8F0 (hover:bg-slate-200)
    - Border: 1px solid #CBD5E1

- **Focus:**
    - Outline: ring-2 ring-slate-400 ring-offset-2

#### Delete Buttons

- **Default:**
    - Background: transparent
    - Text: #DC2626 (text-red-600)
    - Icon: Trash2 (4x4)
    - Opacity: 0 (hidden)

- **Hover:**
    - Opacity: 100 (visible)
    - Text: #B91C1C (hover:text-red-700)
    - Background: #FEE2E2 (hover:bg-red-50)
    - Transition: all 200ms ease

#### Rating Buttons (1-5)

- **Unselected:**
    - Background: #FFFFFF
    - Border: 1px solid #E2E8F0
    - Text: #334155
    - Flex: flex-1

- **Selected:**
    - Background: #2563EB (bg-blue-600)
    - Text: #FFFFFF
    - Border: 1px solid #2563EB

- **Hover (unselected):**
    - Border: 1px solid #CBD5E1
    - Background: #F8FAFC

### Textarea

- **Default:**
    - Border: 1px solid #E2E8F0
    - Background: #FFFFFF
    - Padding: px-3 py-2
    - Min height: min-h-24 (6rem)
    - Rows: 3 (full) / 2 (compact)
    - Resize: vertical only (resize-y)

- **Focus:**
    - Border: 2px solid #3B82F6
    - Ring: ring-2 ring-blue-100

- **Placeholder:**
    - Color: #A0AEC0
    - Font style: italic
    - Text: "Nhập ghi chú (tùy chọn)..."

### Toggle Switch

- **Off State:**
    - Background: #CBD5E1 (bg-slate-300)
    - Circle: bg-white, positioned left

- **On State:**
    - Background: #10B981 (bg-green-500)
    - Circle: bg-white, positioned right
    - Transition: all 200ms ease

- **Focus:**
    - Ring: ring-2 ring-offset-2 ring-green-400

### Drag Handle

- **Icon:** GripVertical (4x4)
- **Default Color:** #A0AEC0 (text-slate-400)
- **Hover Color:** #475569 (text-slate-600)
- **Cursor:** grab (default), grabbing (while dragging)
- **Opacity:** 100 (always visible)

### Task Row Hover State

- **Background:** #F8FAFC (hover:bg-slate-50)
- **Transition:** all 200ms ease
- **Delete Button Reveal:** opacity-0 → opacity-100
- **Cursor:** move (indicates draggable)

---

## ANIMATIONS & TRANSITIONS

### Standard Transitions

- **Property:** all
- **Duration:** 200ms (transition-duration: 200ms)
- **Timing:** ease (transition-timing-function: ease)
- **Delay:** none

### Specific Animations

- **Button hover:** 150ms ease
- **Focus ring:** instant (0ms)
- **Delete button reveal:** 200ms ease
- **Toggle switch:** 200ms ease
- **Page load:** fade-in 300ms ease (if needed)

### Transform Effects

- **Button press:** scale-98 (scale(0.98))
- **Hover lift:** none (flat design)
- **Focus:** ring-2 ring-offset-2 (no translate)

---

## ACCESSIBILITY REQUIREMENTS

### ARIA Labels & Roles

- **Form wrapper:** `<form onSubmit={handleSubmit}>`
- **Fieldsets:** Use for grouped ratings (Quality + Spirit)
- **Labels:** All inputs must have associated labels (id + htmlFor)
- **Descriptions:** Use CardDescription for help text
- **Icons only:** Add title attributes for context

```tsx
// Example for rating buttons
<fieldset className="space-y-3">
    <legend className="text-sm font-medium">Chất lượng công việc</legend>
    <div className="flex gap-2" role="group">
        {[1, 2, 3, 4, 5].map((rating) => (
            <Button
                key={rating}
                aria-pressed={qualityRating === rating}
                aria-label={`Quality rating: ${rating} out of 5`}
            >
                {rating}
            </Button>
        ))}
    </div>
</fieldset>
```

### Keyboard Navigation

- Tab order: Project → Today tasks → Tomorrow tasks → Ratings → Notes → Submit
- Delete buttons: Keyboard accessible via Tab
- Drag handles: Skip in tab order (use mouse/touch)
- Enter key: Submit form from last input

### Color Contrast

- **Text on white:** #0F172A (99% contrast)
- **Text on blue:** white (100% contrast)
- **Text on red:** white (100% contrast)
- **Placeholder text:** #A0AEC0 on white (45% - acceptable)
- All interactive elements: minimum 4.5:1 ratio

### Screen Reader Support

```tsx
{
    /* Drag handle - skip for SR, described by context */
}
<div className="flex-shrink-0 text-slate-400" aria-hidden="true">
    <GripVertical className="w-4 h-4" />
</div>;

{
    /* Task row numbering */
}
<div aria-label={`Task ${index + 1} of ${tasks.length}`}>
    {/* Task content */}
</div>;

{
    /* Delete button */
}
<Button
    type="button"
    aria-label={`Delete task: ${task.description}`}
    onClick={() => onRemoveTask(task.id)}
>
    <Trash2 className="w-4 h-4" />
</Button>;
```

### Focus Indicators

- Visible ring on all interactive elements: `focus:ring-2 focus:ring-offset-2`
- Ring color: matches element theme (blue for primary, red for delete)
- Ring offset: 2px for visibility

---

## STATE MANAGEMENT

Use React hooks (useState) for:

- selectedProject: string
- todayTasks: TodayTask[]
- tomorrowTasks: TomorrowTask[]
- qualityRating: 1-5 (default: 3)
- spiritRating: 1-5 (default: 3)
- notes: string (empty by default)
- submitToGForm: boolean (true by default)

Handlers:

- addTodayTask(): Generate new task with Date.now().toString() as id
- removeTodayTask(id: string): Remove from array if length > 1
- updateTodayTask(id: string, field: keyof TodayTask, value: any): Update specific field
- addTomorrowTask(): Generate new task with Date.now().toString() as id
- removeTomorrowTask(id: string): Remove from array if length > 1
- updateTomorrowTask(id: string, value: string): Update description
- handleSubmit(e: React.FormEvent): Collect all data, validate, log/submit

Validation Rules:

- All tasks require description (non-empty string)
- Progress: must be in [0, 10, 20, ..., 100]
- If progress < 100: expectedDate must be provided
- Ratings: must be 1-5
- Notes: optional (can be empty)
- At least 1 task in today's tasks (enforced by UI)

---

## COMPONENT STRUCTURE

```
/app
  /layout.tsx (Root layout with fonts, metadata)
  /page.tsx (Full version - main daily report)
  /compact
    /page.tsx (Compact version - condensed daily report)
  /logwork-history
    /page.tsx (History view - list of past reports)

/components/daily-report
  /today-task-section.tsx (Full version)
  /today-task-section-compact.tsx (Compact version)
  /tomorrow-task-section.tsx (Full version)
  /tomorrow-task-section-compact.tsx (Compact version)
  /self-evaluation-section.tsx (Full version)
  /self-evaluation-section-compact.tsx (Compact version)

/components/ui
  [shadcn/ui components pre-installed]
  - button.tsx
  - card.tsx
  - input.tsx
  - select.tsx
  - switch.tsx
  - textarea.tsx

/public
  /logos
    /jrr.jpg
    /primas.jpg
    /project-a.jpg
    /project-b.jpg
    /project-c.jpg
    /project-d.jpg
    /project-e.jpg
    /project-f.jpg
    /project-g.jpg
    /project-h.jpg
```

---

## DETAILED WIREFRAME - FULL VERSION

```
┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ Daily Report              📋 View History     ┃  Header
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

  Main Container (max-w-4xl, centered)

  ┌───────────────────────────────────────┐
  │ Chọn dự án                            │
  ├───────────────────────────────────────┤
  │ [Logo] [Select: JRR ▼              ] │
  │ 64x64    flex-1                       │
  └───────────────────────────────────────┘
            Gap: 1.5rem (space-y-6)

  ┌───────────────────────────────────────┐
  │ Task ngày hôm nay                     │
  │ Nhập các công việc đã làm hôm nay...  │
  ├───────────────────────────────────────┤
  │ ≡ [Mô tả...      ] [50%] [2024-01-15] [✕]
  │ ≡ [Mô tả...      ] [100%]
  │ ≡ [Mô tả...      ] [30%] [2024-01-15] [✕]
  │
  │ [+ Thêm công việc]                    │
  └───────────────────────────────────────┘
            Gap: 1.5rem (space-y-6)

  ┌───────────────────────────────────────┐
  │ Task dự kiến ngày mai                 │
  │ Lên kế hoạch cho ngày mai             │
  ├───────────────────────────────────────┤
  │ ≡ [Mô tả...               ] [✕]
  │ ≡ [Mô tả...               ] [✕]
  │
  │ [+ Thêm công việc]                    │
  └───────────────────────────────────────┘
            Gap: 1.5rem (space-y-6)

  ┌───────────────────────────────────────┐
  │ Tự đánh giá                           │
  │ Đánh giá chất lượng công việc...      │
  ├──────────────────┬────────────────────┤
  │ Chất lượng        │ Tinh thần         │
  │                  │                   │
  │ [1][2][3][4][5] │ [1][2][3][4][5]  │
  └──────────────────┴────────────────────┘
            Gap: 1.5rem (space-y-6)

  ┌───────────────────────────────────────┐
  │ Ghi chú                               │
  │ Thêm ghi chú tùy chọn                 │
  ├───────────────────────────────────────┤
  │ [Textarea min-h-24                 ] │
  │ [rows-3                            ] │
  └───────────────────────────────────────┘
            Gap: 1.5rem (space-y-6)

  ┌───────────────────────────────────────┐
  │ Gửi tới Google Form  ⊙ │ [Gửi Report] │
  └───────────────────────────────────────┘
```

---

## DETAILED WIREFRAME - COMPACT VERSION

```
┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ Daily Report       📋 History         ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

  ┌──────────────────────────────────┐
  │ Chọn dự án                       │
  ├──────────────────────────────────┤
  │ [Logo] [Select ▼         ]       │
  └──────────────────────────────────┘
        Gap: 0.75rem (space-y-3)

  ┌──────────────────────────────────┐
  │ Task ngày hôm nay                │
  ├──────────────────────────────────┤
  │ ≡[Mô tả...][50%][Date] [✕]
  │ ≡[Mô tả...][100%]
  │ ≡[Mô tả...][30%][Date] [✕]
  │ [+ Thêm]                         │
  └──────────────────────────────────┘
        Gap: 0.75rem (space-y-3)

  ┌──────────────────────────────────┐
  │ Task ngày mai                    │
  ├──────────────────────────────────┤
  │ ≡[Mô tả...           ][✕]
  │ ≡[Mô tả...           ][✕]
  │ [+ Thêm]                         │
  └──────────────────────────────────┘
        Gap: 0.75rem (space-y-3)

  ┌──────────────────────────────────┐
  │ Chất lượng công việc             │
  │ [1][2][3][4][5]                 │
  └──────────────────────────────────┘
        Gap: 0.75rem (space-y-3)

  ┌──────────────────────────────────┐
  │ Tinh thần làm việc               │
  │ [1][2][3][4][5]                 │
  └──────────────────────────────────┘
        Gap: 0.75rem (space-y-3)

  ┌──────────────────────────────────┐
  │ Ghi chú                          │
  │ [Textarea rows-2             ]  │
  └──────────────────────────────────┘
        Gap: 0.75rem (space-y-3)

  Gửi tới Google Form  ⊙
  [Gửi Report]
```

---

## IMPLEMENTATION CHECKLIST

### Phase 1: Setup & Assets

- [ ] Create project structure (folders/files)
- [ ] Generate all 10 logo images
- [ ] Verify Next.js 16 setup with App Router
- [ ] Install/verify shadcn/ui components
- [ ] Setup Tailwind CSS v4 with correct config

### Phase 2: Full Version Page

- [ ] Create /app/page.tsx main form
- [ ] Implement project selection card with logo display
- [ ] Create today-task-section.tsx component
- [ ] Create tomorrow-task-section.tsx component
- [ ] Create self-evaluation-section.tsx component
- [ ] Implement form state management (useState)
- [ ] Implement all handlers (add/remove/update tasks)
- [ ] Style with exact spacing/colors as specified
- [ ] Test task repeater with 10+ tasks
- [ ] Test responsive behavior (mobile/tablet/desktop)

### Phase 3: Compact Version Page

- [ ] Create /app/compact/page.tsx
- [ ] Create compact component variants (-compact.tsx)
- [ ] Adjust spacing (space-y-3, p-1.5, etc)
- [ ] Adjust font sizes (text-xs, text-2xl, etc)
- [ ] Single column self-evaluation
- [ ] Test with 10+ tasks for compactness
- [ ] Verify responsive on all breakpoints

### Phase 4: Styling & Polish

- [ ] Verify color system (all hex values)
- [ ] Check input focus states (ring-2 ring-blue-100)
- [ ] Implement hover effects (delete button opacity)
- [ ] Add transitions (all 200ms ease)
- [ ] Test drag handle appearance (GripVertical icons)
- [ ] Verify button states (hover, focus, active)
- [ ] Check card shadows (shadow-sm)

### Phase 5: Accessibility & UX

- [ ] Add ARIA labels to all interactive elements
- [ ] Test keyboard navigation (Tab order)
- [ ] Verify color contrast (4.5:1 minimum)
- [ ] Add focus indicators (ring styles)
- [ ] Screen reader testing
- [ ] Test with mouse, keyboard, touch
- [ ] Verify form submission flow

### Phase 6: History & Navigation

- [ ] Create /logwork-history/page.tsx
- [ ] Add history link in header
- [ ] Implement report storage (localStorage/database)
- [ ] Display list of past reports
- [ ] Add filtering/sorting (by date/project)

### Phase 7: Testing & QA

- [ ] Test all form inputs
- [ ] Test task add/remove/update
- [ ] Test rating selections
- [ ] Test Google Form toggle
- [ ] Test responsive on real devices
- [ ] Test form submission
- [ ] Console logging for debugging
- [ ] Cross-browser testing

### Phase 8: Deployment Ready

- [ ] Remove debug console.log statements
- [ ] Optimize images (logos)
- [ ] Test production build
- [ ] Verify all paths work
- [ ] Setup environment variables (Google Form URL if needed)
- [ ] Deploy to Vercel

---

## EXAMPLE CODE SNIPPETS

### Task Row Component (Reusable Pattern)

```tsx
{
    tasks.map((task) => (
        <div
            key={task.id}
            className="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move"
        >
            {/* Drag Handle */}
            <div className="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors mt-1 sm:mt-0">
                <GripVertical className="w-4 h-4" />
            </div>

            {/* Input Fields */}
            <Input
                placeholder="Mô tả..."
                value={task.description}
                onChange={(e) =>
                    onUpdateTask(task.id, "description", e.target.value)
                }
                className="flex-1 h-9 text-sm"
            />

            {/* Delete Button */}
            {tasks.length > 1 && (
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    className="h-9 w-9 text-red-600 hover:text-red-700 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"
                    onClick={() => onRemoveTask(task.id)}
                >
                    <Trash2 className="w-4 h-4" />
                </Button>
            )}
        </div>
    ));
}
```

### Rating Buttons Component

```tsx
<div className="flex gap-2 justify-center">
    {[1, 2, 3, 4, 5].map((rating) => (
        <Button
            key={rating}
            variant={qualityRating === rating ? "default" : "outline"}
            className="flex-1 h-10 rounded-md"
            onClick={() => setQualityRating(rating)}
        >
            {rating}
        </Button>
    ))}
</div>
```

### Form Structure

```tsx
<form onSubmit={handleSubmit} className="space-y-6">
    {/* Each section uses Card component */}
    <Card>
        <CardHeader>
            <CardTitle>Section Title</CardTitle>
            <CardDescription>Section description</CardDescription>
        </CardHeader>
        <CardContent>{/* Section content */}</CardContent>
    </Card>
</form>
```

---

## KEY DESIGN PRINCIPLES

1. **Compact by Default:** Minimize whitespace, use density effectively
2. **Clear Visual Hierarchy:** Task rows prominent, secondary actions on hover
3. **Consistent Spacing:** Use Tailwind scale consistently (p-2, p-3, p-4, gap-2, gap-3)
4. **Accessible:** ARIA labels, keyboard navigation, color contrast
5. **Responsive:** Mobile-first approach, scale up for larger screens
6. **Predictable Interactions:** Hover states, focus indicators, loading states
7. **Data Integrity:** Validation, error messages, confirmation dialogs for destructive actions

---

## NEXT STEPS FOR IMPLEMENTATION

1. Generate all 10 logo assets
2. Create page.tsx and all full-version components
3. Create compact/page.tsx and all compact-version components
4. Create logwork-history/page.tsx for history view
5. Implement drag-and-drop reordering (optional enhancement)
6. Configure Google Form integration (API endpoint)
7. Add local storage or database persistence
8. Test responsive behavior on all breakpoints
9. Add form validation and error handling
10. Deploy to Vercel

---

## NOTES FOR AI IMPLEMENTERS

- Follow Tailwind CSS v4 patterns (no arbitrary values unless necessary)
- Use shadcn/ui components consistently
- Implement proper TypeScript types
- Keep component files focused and reusable
- Use semantic HTML elements
- Add proper alt text for images
- Test keyboard navigation
- Ensure color contrast meets WCAG standards
- Use meaningful variable and function names
- Add comments for complex logic
