# Daily Logwork — Rules

## Laravel Conventions

### Models
- Always define $fillable — never use $guarded
- Always cast date/int/boolean types
- Relationships use return type hints (HasMany, BelongsTo)
- Static helpers return arrays or collections
- snake_case for DB columns

### Migrations
- Use foreignId()->constrained() syntax
- enum() for fixed sets
- nullable() for optional fields
- timestamps() automatically
- onDelete('cascade') for child tables
- Plural snake_case table names

### Livewire
- Public properties are reactive
- wire:model.live for instant updates
- wire:model.defer for batched updates
- wire:model.debounce-500ms for textareas
- Validate with $this->validate()
- Reindex arrays with array_values() after filter
- Use uniqid() for dynamic list IDs
- Always use wire:key on @foreach loops
- Return view from render() with data array
- Use $this->dispatch() for events
- Use $this->redirectRoute() for navigation

### Blade
- {{ }} escaped output — never use {{!! }}
- @foreach with wire:key
- @if/@endif for conditionals
- @error/@enderror for validation
- wire:submit="save" for forms
- wire:loading/wire:loading.remove for states

### Routing
- All routes in Route::middleware(['web'])
- Livewire page component syntax: Route::get('/path', Component::class)
- Always name routes: ->name('route.name')
- Named routes in links: route('route.name')

---

## Design System Rules

### Colors — Use Tailwind Classes Only
- Do NOT use inline hex values
- Use bg-slate-50, text-slate-900, border-slate-200, bg-blue-600, text-red-600
- Focus: border-blue-500, ring-blue-100

### Typography
- Page title: text-3xl font-bold (full) / text-2xl (compact)
- Card title: text-xl font-semibold (full) / text-lg (compact)
- Description: text-sm text-slate-500
- Body: text-sm (full) / text-xs (compact)

### Spacing
- Full version: space-y-6 between cards
- Compact version: space-y-3 between cards
- Card padding: px-6 py-4 (full), px-3 py-3 (compact)
- Task row: p-2 (full), p-1.5 (compact)

### Sizing
- Input: h-9 (full), h-8 (compact)
- Button: h-10 (full), h-8 (compact)
- Progress select: w-20 h-9
- Date input: w-32 h-9

### Transitions
- General: transition-all duration-200
- Buttons: transition-all duration-150
- Delete button: opacity-0 group-hover:opacity-100 transition-opacity duration-200

---

## Responsive Rules

- Mobile-first approach
- Task rows: flex-col on mobile, sm:flex-row on 640px+
- Self evaluation: grid-cols-1 mobile, md:grid-cols-2 on 768px+
- Compact version: always single column for self evaluation

---

## Business Rules

### Projects
- 10 fixed projects: JRR, Primas, Project A–H
- Logo path: /logos/{slug}.jpg (64x64px)
- Project name stored as string (not FK)

### Tasks
- Today tasks have progress (0-100 in 10% increments)
- Expected date only required when progress < 100
- Tomorrow tasks have no progress/date fields
- Min 1 task per type (enforced by UI)
- Cannot delete last task
- task_type enum: today / tomorrow

### Ratings
- Quality and spirit ratings: 1-5 scale
- 1=Rất kém, 2=Kém, 3=Bình thường, 4=Tốt, 5=Rất tốt
- Default: 3 (Bình thường)

### Submission
- submit_to_gform toggle: default true
- Reports stored with report_date = today
- History sorted by date DESC
- Can delete reports from history
