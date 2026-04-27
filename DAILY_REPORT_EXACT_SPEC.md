# Daily Report - Exact UI Specification

## Table of Contents
1. [Overview](#overview)
2. [Color System](#color-system)
3. [Typography System](#typography-system)
4. [Spacing & Layout System](#spacing--layout-system)
5. [Component Specifications](#component-specifications)
6. [Page Layout - Full Version](#page-layout---full-version)
7. [Interactive Elements](#interactive-elements)
8. [Button Specifications](#button-specifications)
9. [Input & Form Elements](#input--form-elements)
10. [Icon Specifications](#icon-specifications)
11. [Responsive Breakpoints](#responsive-breakpoints)
12. [Complete Component Breakdown](#complete-component-breakdown)

---

## Overview

**Project:** Daily Report Application  
**Framework:** Next.js 16 / React 19 (or Laravel Livewire)  
**CSS Framework:** Tailwind CSS v4  
**UI Components:** shadcn/ui  
**Drag & Drop:** @dnd-kit/sortable  
**Alerts:** SweetAlert2 (optional)  
**Target:** Production-ready, pixel-perfect UI consistency  

---

## Color System

### CSS Variables (in globals.css or Tailwind config)

```css
:root {
  /* Primary Colors */
  --color-primary: #2563EB;           /* Blue 600 */
  --color-primary-hover: #1D4ED8;     /* Blue 700 */
  --color-primary-dark: #1E40AF;      /* Blue 800 */
  
  /* Backgrounds */
  --color-bg-page: #F8FAFC;           /* Slate 50 */
  --color-bg-card: #FFFFFF;           /* White */
  --color-bg-hover: #F1F5F9;          /* Slate 100 */
  --color-bg-subtle: #F8FAFC;         /* Slate 50 */
  
  /* Text Colors */
  --color-text-primary: #0F172A;      /* Slate 900 */
  --color-text-secondary: #475569;    /* Slate 600 */
  --color-text-tertiary: #64748B;     /* Slate 500 */
  --color-text-muted: #A0AEC0;        /* Slate 400 */
  
  /* Borders */
  --color-border-default: #E2E8F0;    /* Slate 200 */
  --color-border-hover: #CBD5E1;      /* Slate 300 */
  
  /* Status Colors */
  --color-delete: #DC2626;            /* Red 600 */
  --color-delete-hover: #B91C1C;      /* Red 700 */
  --color-delete-bg: #FEE2E2;         /* Red 50 */
  --color-success: #16A34A;           /* Green 600 */
  --color-warning: #EAB308;           /* Yellow 400 */
}
```

### Tailwind Color Classes Mapping

| Element | Tailwind Class | Hex Value | Usage |
|---------|---|---|---|
| Page Background | `bg-slate-50` | #F8FAFC | Main page background |
| Card Background | `bg-white` | #FFFFFF | Card, input backgrounds |
| Card Border | `border-slate-200` | #E2E8F0 | 1px solid borders |
| Card Hover | `hover:bg-slate-50` | #F8FAFC | Row hover effect |
| Primary Text | `text-slate-900` | #0F172A | Headings, primary text |
| Secondary Text | `text-slate-600` | #475569 | Labels, descriptions |
| Tertiary Text | `text-slate-500` | #64748B | Hints, disabled text |
| Muted Text | `text-slate-400` | #A0AEC0 | Placeholders |
| Primary Button | `bg-blue-600` | #2563EB | Submit button |
| Button Hover | `hover:bg-blue-700` | #1D4ED8 | Hover state |
| Delete Text | `text-red-600` | #DC2626 | Delete action |
| Delete Hover | `hover:bg-red-50` | #FEE2E2 | Delete button hover |
| Icon Gray | `text-slate-400` | #A0AEC0 | Drag handle, icons |
| Focus Ring | `ring-blue-100` | #DBEAFE | Focus state ring |

---

## Typography System

### Heading Hierarchy

| Level | Element | Tailwind Classes | Size | Weight | Line Height | Color | Usage |
|-------|---------|---|---|---|---|---|---|
| H1 | Page Title | `text-3xl font-bold` | 1.875rem (30px) | 700 | 2.25rem | text-slate-900 | "Daily Report" header |
| H2 | Card Title | `text-xl font-semibold` | 1.25rem (20px) | 600 | 1.75rem | text-slate-900 | "Task ngày hôm nay" |
| H3 | Sub-section | `text-lg font-medium` | 1.125rem (18px) | 500 | 1.75rem | text-slate-900 | Self evaluation titles |
| Label | Form Label | `text-sm font-medium` | 0.875rem (14px) | 500 | 1.25rem | text-slate-700 | Input labels |
| Body | Input Text | `text-sm font-normal` | 0.875rem (14px) | 400 | 1.25rem | text-slate-900 | Form inputs |
| Small | Descriptions | `text-xs font-normal` | 0.75rem (12px) | 400 | 1rem | text-slate-500 | Card descriptions |
| Placeholder | Placeholders | `text-sm italic` | 0.875rem (14px) | 400 (italic) | 1.25rem | text-slate-400 | Input placeholders |

### Font Family
- **Primary:** Geist, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif
- **Fallback:** System fonts

---

## Spacing & Layout System

### Padding & Margin Reference

| Size | Tailwind | Pixels | Usage |
|------|----------|--------|-------|
| xs | `p-1` | 4px | Small elements |
| sm | `p-2` | 8px | Task row padding |
| md | `p-3` | 12px | Default padding |
| lg | `p-4` | 16px | Section padding |
| xl | `px-6 py-4` | 24px x 16px | Card padding |
| 2xl | `p-8` | 32px | Page padding |

### Gap & Spacing

| Component | Gap/Space | Tailwind | Pixels | Type |
|-----------|-----------|----------|--------|------|
| Page sections | Vertical | `space-y-6` | 24px | gap between cards |
| Task items | Vertical | `space-y-2` | 8px | gap between tasks |
| Task row | Horizontal | `gap-2 sm:gap-3` | 8px / 12px | gap between inputs |
| Form inputs | Vertical | `mb-3` | 12px | margin after input |
| Card header | Padding | `px-6 py-4` | 24px x 16px | inside card |
| Card content | Padding | `px-6 py-4` | 24px x 16px | inside card |
| Page container | Padding | `px-4 py-8` | 16px x 32px | page sides |

---

## Component Specifications

### 1. Card Component

**Structure:** Wrapper → Header → Content

**Card Container**
```
Class: bg-white border border-slate-200 rounded-lg shadow-sm
Width: Full width (max-w-4xl parent)
Border: 1px solid #E2E8F0
Border Radius: 8px (0.5rem)
Box Shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05)
Margin: Controlled by parent (space-y-6)
```

**Card Header**
```
Class: border-b border-slate-200 px-6 py-4
Padding: 24px horizontal, 16px vertical
Border Bottom: 1px solid #E2E8F0
Background: Inherit from card (white)
```

**Card Title**
```
Class: text-xl font-semibold text-slate-900
Font Size: 20px
Font Weight: 600
Color: #0F172A
Line Height: 28px (1.75rem)
Margin Bottom: 0 (inherits from parent)
```

**Card Description**
```
Class: text-sm text-slate-500 mt-1
Font Size: 14px
Color: #64748B
Margin Top: 4px
Font Weight: 400
```

**Card Content**
```
Class: px-6 py-4
Padding: 24px horizontal, 16px vertical
Background: Inherit (white)
```

### 2. Input Component

**Text Input (Description, Notes)**
```
Class: w-full h-9 px-3 py-2 text-sm border border-slate-200 rounded-md
       bg-white text-slate-900 placeholder:text-slate-400
       focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100
       transition-all duration-200
       disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed

Width: 100% (flex-1 in flex context)
Height: 36px (h-9 = 2.25rem)
Padding: 12px horizontal (px-3), 8px vertical (py-2)
Border: 1px solid #E2E8F0
Border Radius: 6px (rounded-md)
Background: #FFFFFF
Text Color: #0F172A
Font Size: 14px
```

**Input Focus State**
```
Border Color: #3B82F6 (blue-500) - 2px
Ring: 8px ring-blue-100 (rgba(191, 219, 254, 0.5))
Ring Offset: 0
Transition: all 200ms ease
```

**Input Placeholder**
```
Color: #A0AEC0 (text-slate-400)
Font Style: Normal
Opacity: 1
```

### 3. Select Dropdown

**Select Container**
```
Class: w-20 h-9 px-3 py-2 text-sm border border-slate-200 rounded-md
       bg-white text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-2
       focus:ring-blue-100 transition-all duration-200

Width: 80px (w-20) for progress, auto for project
Height: 36px (h-9)
Padding: 12px x 8px
Border: 1px solid #E2E8F0
Font Size: 14px
```

**Select Content (Dropdown Menu)**
```
Background: #FFFFFF
Border: 1px solid #E2E8F0
Border Radius: 6px
Box Shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1)
```

**Select Item - Default**
```
Padding: 8px 12px (px-3 py-2)
Color: #0F172A
Background: #FFFFFF
Font Size: 14px
```

**Select Item - Hover**
```
Background: #F1F5F9 (slate-100)
Color: #0F172A
```

**Select Item - Selected**
```
Background: #DBEAFE (blue-100)
Color: #1E40AF (blue-800)
Font Weight: 500
```

### 4. Buttons

#### Primary Button (Submit)

```
Class: px-6 py-2.5 h-10 bg-blue-600 text-white font-medium rounded-md
       hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2
       focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150
       disabled:opacity-50 disabled:cursor-not-allowed

Height: 40px (h-10)
Padding: 24px horizontal (px-6), 10px vertical (py-2.5)
Background: #2563EB (blue-600)
Text Color: #FFFFFF
Font Size: 14px
Font Weight: 500
Border Radius: 6px
```

**Primary Button Hover**
```
Background: #1D4ED8 (blue-700)
Cursor: pointer
Transition: all 150ms ease
```

**Primary Button Focus**
```
Ring: 2px solid #3B82F6
Ring Offset: 2px
Outline: none
```

#### Secondary Button (Add/Remove)

```
Class: px-4 py-2 h-9 bg-slate-100 text-slate-700 font-medium rounded-md
       border border-slate-200 hover:bg-slate-200 focus:outline-none
       focus:ring-2 focus:ring-slate-400 focus:ring-offset-2
       transition-all duration-200

Height: 36px (h-9)
Padding: 16px horizontal (px-4), 8px vertical (py-2)
Background: #F1F5F9 (slate-100)
Text Color: #334155 (slate-700)
Border: 1px solid #E2E8F0
Font Size: 14px
Font Weight: 500
```

#### Delete Button (Icon)

```
Class: h-9 w-9 p-0 bg-transparent text-red-600 hover:text-red-700
       hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500
       focus:ring-offset-2 opacity-0 group-hover:opacity-100
       transition-opacity duration-200

Height: 36px (h-9)
Width: 36px (w-9)
Padding: 0
Background: Transparent (default)
Text Color: #DC2626 (red-600)
Border Radius: 6px
```

**Delete Button Hover**
```
Text Color: #B91C1C (red-700)
Background: #FEE2E2 (red-50)
Opacity: 100 (from 0)
Transition: all 200ms ease
```

#### Rating Buttons (1-5)

**Unselected**
```
Class: flex-1 h-10 bg-white border border-slate-200 text-slate-700
       font-medium rounded-md hover:border-slate-300 hover:bg-slate-50
       transition-all duration-200

Height: 40px (h-10)
Flex: flex-1 (equal width)
Padding: Auto (button default)
Background: #FFFFFF
Text Color: #334155 (slate-700)
Border: 1px solid #E2E8F0
Font Size: 14px
Font Weight: 500
Gap between: gap-2
```

**Selected**
```
Background: #2563EB (blue-600)
Text Color: #FFFFFF
Border: 1px solid #2563EB
```

**Hover (unselected)**
```
Border Color: #CBD5E1 (slate-300)
Background: #F8FAFC (slate-50)
```

### 5. Textarea

```
Class: w-full min-h-24 px-3 py-2 text-sm border border-slate-200
       rounded-md bg-white text-slate-900 placeholder:text-slate-400
       focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100
       transition-all duration-200 resize-none

Width: 100%
Min Height: 96px (min-h-24)
Height: Auto-grows with content
Padding: 12px horizontal, 8px vertical
Border: 1px solid #E2E8F0
Border Radius: 6px
Background: #FFFFFF
Resize: Vertical only
Rows: 3 (default rows attribute)
```

### 6. Toggle/Switch

```
Class: w-12 h-6 rounded-full bg-slate-300 focus:outline-none
       focus:ring-2 focus:ring-green-500 focus:ring-offset-2
       transition-colors duration-200

Off State:
  Background: #CBD5E1 (slate-300)
  Circle: white, positioned left

On State:
  Background: #10B981 (green-500)
  Circle: white, positioned right
  
Height: 24px (h-6)
Width: 48px (w-12)
Circle: 20px diameter, 2px offset from edge
```

### 7. Drag Handle Icon

```
Icon: GripVertical (from lucide-react)
Size: 16px × 16px (w-4 h-4)
Color Default: #A0AEC0 (text-slate-400)
Color Hover: #475569 (text-slate-600)
Cursor: grab (default), grabbing (while dragging)
Transition: color 200ms ease
Flex Shrink: 0 (doesn't shrink in flex)
```

### 8. Logo Display

```
Width: 64px (w-16)
Height: 64px (h-16)
Border Radius: 8px (rounded-lg)
Object Fit: cover
Flex Shrink: 0 (doesn't shrink)
Aspect Ratio: 1:1 (square)
```

---

## Page Layout - Full Version

### Overall Structure

```
┌─────────────────────────────────────────────────────────────┐
│                   HEADER (bg-white, shadow)                 │
│  Daily Report                           📋 View History     │
└─────────────────────────────────────────────────────────────┘
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ bg-slate-50 (Page background)                           │ │
│  │ max-w-4xl (56rem / 896px)                               │ │
│  │ px-4 py-8 (16px, 32px padding)                          │ │
│  │ mx-auto (centered)                                       │ │
│  │                                                           │ │
│  │  ┌──────────────────────────────────────────────────┐   │ │
│  │  │ Card - Project Selection                         │   │ │
│  │  │ bg-white border-slate-200 rounded-lg shadow-sm   │   │ │
│  │  │ px-6 py-4                                        │   │ │
│  │  │ space-y-6 (gap to next card)                     │   │ │
│  │  │                                                  │   │ │
│  │  │  Flex: items-center gap-4                        │   │ │
│  │  │  [Logo 64x64] [Select dropdown flex-1]           │   │ │
│  │  └──────────────────────────────────────────────────┘   │ │
│  │                                                           │ │
│  │  ┌──────────────────────────────────────────────────┐   │ │
│  │  │ Card - Task ngày hôm nay                         │   │ │
│  │  │ px-6 py-4 (header), px-6 py-4 (content)          │   │ │
│  │  │ space-y-2 (between tasks)                        │   │ │
│  │  │                                                  │   │ │
│  │  │  ≡ [Input] [Select] [Date] [Delete]  (p-2)      │   │ │
│  │  │  ≡ [Input] [Select] [Date] [Delete]  (p-2)      │   │ │
│  │  │  ≡ [Input] [Select]             [Delete]  (p-2) │   │ │
│  │  │                                                  │   │ │
│  │  │  [+ Add button]                                 │   │ │
│  │  └──────────────────────────────────────────────────┘   │ │
│  │                                                           │ │
│  │  ┌──────────────────────────────────────────────────┐   │ │
│  │  │ Card - Task dự kiến ngày mai                     │   │ │
│  │  │ px-6 py-4                                        │   │ │
│  │  │                                                  │   │ │
│  │  │  ≡ [Input]                           [Delete]    │   │ │
│  │  │  ≡ [Input]                           [Delete]    │   │ │
│  │  │                                                  │   │ │
│  │  │  [+ Add button]                                 │   │ │
│  │  └──────────────────────────────────────────────────┘   │ │
│  │                                                           │ │
│  │  ┌──────────────────────────────────────────────────┐   │ │
│  │  │ Card - Self Evaluation                           │   │ │
│  │  │ grid grid-cols-2 gap-6                           │   │ │
│  │  │                                                  │   │ │
│  │  │  ┌──────────────────┐  ┌──────────────────┐     │   │ │
│  │  │  │ Chất lượng       │  │ Tinh thần        │     │   │ │
│  │  │  │ [1][2][3][4][5] │  │ [1][2][3][4][5] │     │   │ │
│  │  │  └──────────────────┘  └──────────────────┘     │   │ │
│  │  └──────────────────────────────────────────────────┘   │ │
│  │                                                           │ │
│  │  ┌──────────────────────────────────────────────────┐   │ │
│  │  │ Card - Ghi chú                                   │   │ │
│  │  │ px-6 py-4                                        │   │ │
│  │  │                                                  │   │ │
│  │  │  [Textarea min-h-24]                             │   │ │
│  │  │                                                  │   │ │
│  │  └──────────────────────────────────────────────────┘   │ │
│  │                                                           │ │
│  │  ┌──────────────────────────────────────────────────┐   │ │
│  │  │ Card - Submit Section                            │   │ │
│  │  │ flex flex-col sm:flex-row justify-between        │   │ │
│  │  │ items-start sm:items-center gap-4               │   │ │
│  │  │ px-6 py-4                                        │   │ │
│  │  │                                                  │   │ │
│  │  │  [Google Form Label + Switch]  [Submit Button]   │   │ │
│  │  └──────────────────────────────────────────────────┘   │ │
│  │                                                           │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### Key Layout CSS Classes

```css
/* Page container */
.page-container = max-w-4xl mx-auto px-4 py-8 bg-slate-50

/* Card container */
.card = bg-white border border-slate-200 rounded-lg shadow-sm

/* Card header */
.card-header = px-6 py-4 border-b border-slate-200

/* Card content */
.card-content = px-6 py-4

/* Task row */
.task-row = flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center p-2 hover:bg-slate-50 rounded-md transition-colors group cursor-move

/* Grid for Self Evaluation */
.evaluation-grid = grid grid-cols-1 md:grid-cols-2 gap-6

/* Form gaps */
.form-gap = space-y-6 (between cards)
.task-gap = space-y-2 (between tasks)
.item-gap = gap-2 sm:gap-3 (between task inputs)
```

---

## Interactive Elements

### Task Row Interaction

**Default State**
```
Background: #FFFFFF
Border: none
Padding: p-2 (8px)
```

**Hover State**
```
Background: #F8FAFC (hover:bg-slate-50)
Transition: all 200ms ease
Actions:
  - Delete button opacity: 0 → 100
  - Drag handle color: lighter → darker
  - Cursor: move (grab)
```

**Drag State**
```
Opacity: 0.8 (semi-transparent)
Z-index: 1000 (above other elements)
Cursor: grabbing
Shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1)
Transition: none (instant feedback)
```

**Drop Animation**
```
Duration: 200ms
Type: ease-in-out
Transition: all properties
```

### Form Focus States

**Text Input Focus**
```
Border: 2px solid #3B82F6 (focus:border-blue-500)
Ring: 2px #DBEAFE (focus:ring-2 focus:ring-blue-100)
Outline: none
Box Shadow: 0 0 0 3px rgba(191, 219, 254, 0.5)
```

**Select Focus**
```
Border: 2px solid #3B82F6
Ring: 2px #DBEAFE
Transition: all 200ms ease
```

---

## Button Specifications

### Button Dimensions Reference

| Button Type | Height | Width | Padding | Icon Size | Font Size | Use Case |
|---|---|---|---|---|---|---|
| Primary (Submit) | 40px (h-10) | auto | px-6 py-2.5 | - | 14px | Form submission |
| Secondary (Add) | 36px (h-9) | auto | px-4 py-2 | - | 14px | Add new items |
| Icon (Delete) | 36px (h-9) | 36px (w-9) | p-0 | 16px (w-4 h-4) | - | Remove items |
| Rating | 40px (h-10) | flex-1 | auto | - | 14px | Quality/Spirit rating |

### Button Icon Spacing

```
Icon + Text: gap-2 between icon and text
Icon Only: centered in button
Icon Size: 16px × 16px (w-4 h-4) or 20px × 20px (w-5 h-5)
```

### Button States Summary

| State | Background | Text Color | Border | Cursor |
|-------|---|---|---|---|
| **Primary Default** | #2563EB | white | none | pointer |
| **Primary Hover** | #1D4ED8 | white | none | pointer |
| **Primary Focus** | #1D4ED8 | white | 2px ring | pointer |
| **Primary Disabled** | #2563EB 50% | white 50% | none | not-allowed |
| **Secondary Default** | #F1F5F9 | #334155 | 1px slate-200 | pointer |
| **Secondary Hover** | #E2E8F0 | #334155 | 1px slate-300 | pointer |
| **Delete Default** | transparent | #DC2626 | none | pointer |
| **Delete Hover** | #FEE2E2 | #B91C1C | none | pointer |

---

## Input & Form Elements

### Input Sizing

| Property | Value | Tailwind | Details |
|---|---|---|---|
| Height | 36px | h-9 | Standard input height |
| Padding X | 12px | px-3 | Horizontal padding |
| Padding Y | 8px | py-2 | Vertical padding |
| Font Size | 14px | text-sm | Body text size |
| Border Radius | 6px | rounded-md | Slight rounding |
| Border Width | 1px | border | Thin border |

### Select Sizing

| Property | Value | Tailwind | Details |
|---|---|---|---|
| Height | 36px | h-9 | Same as input |
| Width (Progress) | 80px | w-20 | Progress select |
| Width (Project) | auto | flex-1 | Project select |
| Padding X | 12px | px-3 | Horizontal padding |
| Padding Y | 8px | py-2 | Vertical padding |

### Textarea Sizing

| Property | Value | Tailwind | Details |
|---|---|---|---|
| Width | 100% | w-full | Full width |
| Min Height | 96px | min-h-24 | Minimum height |
| Padding X | 12px | px-3 | Horizontal padding |
| Padding Y | 8px | py-2 | Vertical padding |
| Rows | 3 | rows-3 | Row attribute |
| Font Size | 14px | text-sm | Body text size |

---

## Icon Specifications

### Icons Used

| Icon | Component | Size | Color Default | Color Hover | Usage |
|---|---|---|---|---|---|
| GripVertical | Drag handle | 16×16 (w-4 h-4) | #A0AEC0 | #475569 | Reorder tasks |
| Trash2 | Delete | 16×16 (w-4 h-4) | #DC2626 | #B91C1C | Remove items |
| Plus | Add | 16×16 (w-4 h-4) | inherit | inherit | Add new items |
| HistoryIcon | Navigation | 20×20 (w-5 h-5) | inherit | inherit | View history |
| ChevronDown | Dropdown | auto | inherit | inherit | Select arrow |

### Icon Colors

```
Default Icon: text-slate-600 (for general icons)
Drag Handle: text-slate-400 (lighter, muted)
Delete Icon: text-red-600 (red, warning)
Primary Icon: inherit text color from button
Muted Icon: text-slate-300 (disabled state)
```

---

## Responsive Breakpoints

### Mobile-First Approach

#### Mobile (< 640px)

```
Task rows: flex-col (stack vertically)
Drag handle: mt-1 (shift down for vertical layout)
Select width: w-full (full width)
Date input: w-full (full width)
Gaps: gap-2 (smaller gaps)
Self Evaluation: grid-cols-1 (single column)
Layout: All elements stack vertically
Padding: px-4 (reduced from px-6)
```

#### Tablet (640px - 1024px, `sm:`)

```
Task rows: flex-row (horizontal, sm:flex-row)
Items: sm:items-center (vertical alignment)
Gaps: sm:gap-3 (slightly larger gaps)
Drag handle: sm:mt-0 (reset margin)
Select width: w-20 (progress select)
Self Evaluation: grid-cols-2 (two columns if md)
Padding: px-4 (maintained)
```

#### Desktop (> 1024px, `md:`, `lg:`)

```
Full layout as specified
Max width: max-w-4xl (56rem / 896px)
Padding: px-4 (page), px-6 py-4 (cards)
Self Evaluation: grid-cols-2 (two columns)
All elements at full size
No mobile optimizations active
```

### Responsive Classes Applied

```
Task Container:
  flex flex-col sm:flex-row
  gap-2 sm:gap-3
  items-start sm:items-center

Self Evaluation:
  grid grid-cols-1 md:grid-cols-2
  gap-6

Page Container:
  max-w-4xl
  px-4 py-8
  mx-auto

Project Selection:
  flex flex-col sm:flex-row
  items-start sm:items-center
  gap-4
```

---

## Complete Component Breakdown

### Level 1: Page Structure

```
<html className="bg-slate-50">
  <body>
    <Header />
    <main className="max-w-4xl mx-auto px-4 py-8 bg-slate-50">
      <Form>
        {/* All sections below */}
      </Form>
    </main>
  </body>
</html>
```

### Level 2: Form Sections (Card Components)

```
1. Project Selection Card
   - Logo image (64×64)
   - Select dropdown
   - Flex layout: items-center gap-4

2. Today's Tasks Card
   - Header + description
   - Task list (repeating rows)
   - Add button

3. Tomorrow's Tasks Card
   - Header + description
   - Task list (repeating rows)
   - Add button

4. Self Evaluation Card
   - Grid: 2 columns
   - Quality section (5 rating buttons)
   - Spirit section (5 rating buttons)

5. Notes Card
   - Textarea element
   - Optional indicator

6. Submit Card
   - Flex row: justify-between
   - Google Form toggle + label
   - Submit button
```

### Level 3: Repeating Components

```
Task Row (Today):
  ├─ Drag handle icon (GripVertical)
  ├─ Description input
  ├─ Progress select (0-100%)
  ├─ Expected date input (conditional)
  └─ Delete button (conditional, hidden on hover)

Task Row (Tomorrow):
  ├─ Drag handle icon (GripVertical)
  ├─ Description input
  └─ Delete button (conditional, hidden on hover)

Rating Button Group:
  ├─ Button 1
  ├─ Button 2
  ├─ Button 3
  ├─ Button 4
  └─ Button 5
```

### Level 4: Atomic Elements

```
Inputs:
  - Text input (description)
  - Textarea (notes)
  - Date input (expected date)

Selects:
  - Progress select (0, 10, 20, ..., 100)
  - Project select

Buttons:
  - Primary submit button
  - Secondary add button
  - Icon delete button
  - Rating button (×5)

Icons:
  - Drag handle (GripVertical)
  - Delete (Trash2)
  - Add (Plus)
  - History (HistoryIcon)

Displays:
  - Logo image
  - Toggle switch

Text:
  - Headings (h1, h2, h3)
  - Labels
  - Descriptions
  - Placeholders
```

---

## Colors Quick Reference

### Copy-Paste Color Palette

```css
/* Background Colors */
background: #F8FAFC;        /* Page bg (slate-50) */
background: #FFFFFF;       /* Card bg (white) */
background: #F1F5F9;       /* Hover state (slate-100) */
background: #F8FAFC;       /* Subtle hover (slate-50) */

/* Text Colors */
color: #0F172A;           /* Primary text (slate-900) */
color: #475569;           /* Secondary text (slate-600) */
color: #64748B;           /* Tertiary text (slate-500) */
color: #A0AEC0;           /* Muted text (slate-400) */

/* Interactive Colors */
background: #2563EB;      /* Primary button (blue-600) */
background: #1D4ED8;      /* Button hover (blue-700) */
color: #DC2626;           /* Delete action (red-600) */
background: #FEE2E2;      /* Delete hover (red-50) */

/* Border Colors */
border-color: #E2E8F0;    /* Default border (slate-200) */
border-color: #CBD5E1;    /* Hover border (slate-300) */

/* Ring/Focus Colors */
outline-color: #3B82F6;   /* Focus ring (blue-500) */
box-shadow: 0 0 0 2px #DBEAFE;  /* Focus ring color (blue-100) */
```

---

## Implementation Checklist

- [ ] Verify all colors match hex codes exactly
- [ ] Check all font sizes and weights
- [ ] Confirm padding/margin values in pixels
- [ ] Test button hover and focus states
- [ ] Verify input focus ring appearance
- [ ] Test task row drag handle display
- [ ] Confirm delete button hidden by default
- [ ] Test responsive breakpoints (mobile/tablet/desktop)
- [ ] Verify drag and drop functionality
- [ ] Test form submission
- [ ] Check accessibility (ARIA labels, keyboard nav)
- [ ] Cross-browser testing (Chrome, Firefox, Safari)
- [ ] Mobile device testing (iOS, Android)
- [ ] Performance profiling

---

## Notes for Developers

1. **Use CSS Variables:** Extend Tailwind config with custom color variables for consistency
2. **Reusable Patterns:** Create component mixins for repeated patterns (card, input, button)
3. **Responsive First:** Always use mobile-first approach (no prefix = mobile, `sm:` = 640px+)
4. **Test Thoroughly:** Test every state (default, hover, focus, disabled, error)
5. **Consistent Spacing:** Stick to the spacing scale (4px, 8px, 12px, 16px, 24px, 32px)
6. **Icon Consistency:** All icons should be same size/color unless specified otherwise
7. **Drag & Drop:** Implement with @dnd-kit/sortable for smooth, accessible sorting
8. **Accessibility:** Always include aria-labels, proper button types, and keyboard navigation
9. **Performance:** Use CSS transitions (200ms) for smooth animations
10. **Testing:** Every color, size, spacing should match this spec exactly

