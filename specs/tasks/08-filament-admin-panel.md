# Task 8: Filament Admin Panel Resources & Pages

## Mục tiêu
Xây dựng Admin Panel với Filament: Resources (CRUD), Custom Pages, Widgets, và setup Roles/Permissions.

## Công việc cần làm

### 1. DailyLog Resource (Admin/Manager xem tất cả)
**File**: `app/Filament/Resources/DailyLogResource.php`

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Logwork\Models\DailyLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DailyLogResource extends Resource
{
    protected static ?string $model = DailyLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Logworks';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->default(now()),

                Forms\Components\Textarea::make('summary')
                    ->columnSpanFull()
                    ->maxLength(1000),

                Forms\Components\Textarea::make('tomorrow_plan')
                    ->columnSpanFull()
                    ->maxLength(1000),

                Forms\Components\Toggle::make('is_submit_chat')
                    ->label('Submit to Google Form')
                    ->default(false),

                Forms\Components\Section::make('Tasks')
                    ->schema([
                        Forms\Components\Repeater::make('tasks')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Forms\Components\Toggle::make('progress_percent')
                                    ->label('Progress %')
                                    ->default(100)
                                    ->inline(false),

                                Forms\Components\TextInput::make('estimated_time')
                                    ->label('Estimated (min)')
                                    ->numeric()
                                    ->default(60)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->orderable('order_index'),
                    ]),

                Forms\Components\Toggle::make('auto_submit_enabled')
                    ->label('Auto Submit Enabled'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->getStateUsing(fn ($record) => $record->tasks->count()),

                Tables\Columns\TextColumn::make('total_time')
                    ->label('Total Time (min)')
                    ->getStateUsing(fn ($record) => $record->tasks->sum('estimated_time')),

                Tables\Columns\IconColumn::make('is_submit_chat')
                    ->label('Submitted')
                    ->boolean(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_submit_chat')
                    ->label('Submitted'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DailyLogResource\Pages\ListDailyLogs::route('/'),
            'create' => \App\Filament\Resources\DailyLogResource\Pages\CreateDailyLog::route('/create'),
            'edit' => \App\Filament\Resources\DailyLogResource\Pages\EditDailyLog::route('/{record}/edit'),
            'view' => \App\Filament\Resources\DailyLogResource\Pages\ViewDailyLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin ?? false; // Only admin can create from admin panel
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user?->isAdmin) {
            return parent::getEloquentQuery();
        }

        // Non-admin users chỉ xem log của mình
        return parent::getEloquentQuery()->where('user_id', $user?->id);
    }
}
```

**Note**: Tạo related Page classes (Filament automatically tạo nếu chưa có):
- `app/Filament/Resources/DailyLogResource/Pages/ListDailyLogs.php`
- `app/Filament/Resources/DailyLogResource/Pages/CreateDailyLog.php`
- `app/Filament/Resources/DailyLogResource/Pages/EditDailyLog.php`
- `app/Filament/Resources/DailyLogResource/Pages/ViewDailyLog.php`

Có thể dùng command:
```bash
php artisan make:filament-resource DailyLog --generate
```

### 2. Submission Resource
**File**: `app/Filament/Resources/SubmissionResource.php`

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Logwork\Models\Submission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationLabel = 'Submissions';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('daily_log_id')
                    ->relationship('dailyLog', 'date')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ])
                    ->required(),

                Forms\Components\DateTimePicker::make('scheduled_at'),
                Forms\Components\DateTimePicker::make('submitted_at'),

                Forms\Components\Textarea::make('error_message')
                    ->columnSpanFull(),

                Forms\Components\KeyValue::make('response_data')
                    ->keyLabel('Field')
                    ->valueLabel('Value'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dailyLog.date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dailyLog.user.name')
                    ->label('User')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('retry_count')
                    ->label('Retries'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereHas('dailyLog', function ($q) use ($data) {
                            $q->when($data['from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                              ->when($data['until'], fn($q, $date) => $q->whereDate('date', '<=', $date));
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SubmissionResource\Pages\ListSubmissions::route('/'),
            'view' => \App\Filament\Resources\SubmissionResource\Pages\ViewSubmission::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Chỉ view, không create manually
    }
}
```

### 3. GlobalSetting Resource
**File**: `app/Filament/Resources/GlobalSettingResource.php`

```php
<?php

namespace App\Filament\Resources;

use App\Models\GlobalSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GlobalSettingResource extends Resource
{
    protected static ?string $model = GlobalSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationLabel = 'Global Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Setting Key')
                    ->required()
                    ->unique()
                    ->helperText('Unique key để identify setting'),

                Forms\Components\KeyValue::make('value')
                    ->label('Value')
                    ->helperText('JSON value, có thể là string/array/object')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull()
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->limit(50)
                    ->json(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\GlobalSettingResource\Pages\ListGlobalSettings::route('/'),
            'create' => \App\Filament\Resources\GlobalSettingResource\Pages\CreateGlobalSetting::route('/create'),
            'edit' => \App\Filament\Resources\GlobalSettingResource\Pages\EditGlobalSetting::route('/{record}/edit'),
        ];
    }
}
```

### 4. Custom Page: Global Settings Form (User-friendly)
**File**: `app/Filament/Pages/Admin/GlobalSettingsPage.php`

```php
<?php

namespace App\Filament\Pages\Admin;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Domain\Logwork\Services\SettingService;

class GlobalSettingsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationLabel = 'System Settings';

    protected static ?string $title = 'Cấu hình Hệ thống';

    protected static ?string $slug = 'admin/settings';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.admin.global-settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Load current settings
        $this->data = [
            'google_form_url' => SettingService::getGlobal('google_form_url', ''),
            'default_submit_time' => SettingService::getGlobal('default_submit_time', '17:00:00'),
            'ai_api_key' => SettingService::getGlobal('ai_api_key', ''),
            'allow_manual_edit' => SettingService::getGlobal('allow_manual_edit', true),
            'submit_retry_attempts' => SettingService::getGlobal('submit_retry_attempts', 3),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Google Form Integration')
                    ->description('Cấu hình Google Form endpoint')
                    ->schema([
                        Forms\Components\TextInput::make('google_form_url')
                            ->label('Google Form URL')
                            ->url()
                            ->required()
                            ->helperText('URL của Google Form (phải là formResponse endpoint)'),
                    ]),

                Forms\Components\Section::make('Submission Settings')
                    ->schema([
                        Forms\Components\TimePicker::make('default_submit_time')
                            ->label('Default Submit Time')
                            ->default('17:00'),

                        Forms\Components\Toggle::make('allow_manual_edit')
                            ->label('Allow Manual Edit')
                            ->default(true)
                            ->helperText('Cho phép user chỉnh sửa logwork sau khi submit?'),

                        Forms\Components\TextInput::make('submit_retry_attempts')
                            ->label('Max Retry Attempts')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->default(3),
                    ]),

                Forms\Components\Section::make('AI Settings (Future)')
                    ->schema([
                        Forms\Components\TextInput::make('ai_api_key')
                            ->label('AI API Key')
                            ->password()
                            ->revealable()
                            ->helperText('OpenAI/Anthropic API key (optional)'),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        SettingService::setGlobal('google_form_url', $data['google_form_url'], 'Google Form submission URL');
        SettingService::setGlobal('default_submit_time', $data['default_submit_time'], 'Default time to auto-submit');
        SettingService::setGlobal('allow_manual_edit', $data['allow_manual_edit'], 'Allow editing after submission');
        SettingService::setGlobal('submit_retry_attempts', $data['submit_retry_attempts'], 'Max retry attempts for submission');
        SettingService::setGlobal('ai_api_key', $data['ai_api_key'], 'AI API Key for task parsing');

        $this->redirect(GlobalSettingsPage::getUrl());
    }
}
```

**File**: `resources/views/filament/pages/admin/global-settings.blade.php`

```blade
<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit" color="primary">
                Save Settings
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
```

### 5. Widget: Today's Submission Status
**File**: `app/Filament/Widgets/TodaySubmissionStatus.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Domain\Logwork\Models\DailyLog;
use App\Domain\Logwork\Models\Submission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodaySubmissionStatus extends BaseWidget
{
    protected function getStats(): array
    {
        $today = today();

        $totalUsers = \App\Models\User::count();
        $submittedToday = DailyLog::where('date', $today)->count();
        $pendingSubmissions = Submission::where('status', 'pending')->count();
        $failedSubmissions = Submission::where('status', 'failed')->count();

        $completionRate = $totalUsers > 0
            ? round(($submittedToday / $totalUsers) * 100, 1)
            : 0;

        return [
            Stat::make('Total Users', $totalUsers),
            Stat::make('Submitted Today', $submittedToday)
                ->description("$completionRate% completion rate")
                ->color($completionRate >= 80 ? 'success' : 'warning'),

            Stat::make('Pending Submissions', $pendingSubmissions)
                ->color($pendingSubmissions > 0 ? 'warning' : 'success'),

            Stat::make('Failed Submissions', $failedSubmissions)
                ->color($failedSubmissions > 0 ? 'danger' : 'success'),
        ];
    }
}
```

### 6. Register Navigation & Pages
**File**: `app/Filament/Providers/FilamentServiceProvider.php` (nếu chưa có)

```php
<?php

namespace App\Filament\Providers;

use App\Filament\Pages\Admin\GlobalSettingsPage;
use App\Filament\Resources\DailyLogResource;
use App\Filament\Resources\SubmissionResource;
use App\Filament\Resources\GlobalSettingResource;
use App\Filament\Widgets\TodaySubmissionStatus;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function () {
            // Có thể customize navigation ở đây
        });
    }
}
```

### 7. Cấu hình Filament Shield (optional - role/permission)
Nếu muốn chi tiết hơn ngoài role check:

```bash
# Install Shield
composer require bezhansalleh/filament-shield -W

# Publish config
php artisan vendor:publish --tag="filament-shield-config"

# Tạo roles/permissions
php artisan filament:shield:generate --communities
```

## Files cần tạo (hoặc generate bằng artisan)
```bash
# Generate Resources với Pages
php artisan make:filament-resource DailyLog --generate
php artisan make:filament-resource Submission --generate
php artisan make:filament-resource GlobalSetting --generate

# Create Pages
php artisan make:filament-page Admin/GlobalSettingsPage --type=page
php artisan make:filament-widget TodaySubmissionStatus --type=stats-overview

# Create Middleware
php artisan make:middleware EnsureRole
```

## Kiểm tra
```bash
# Access admin panel
php artisan serve
http://localhost:8000/admin

# Login với admin user đã tạo
# Check navigation items
# Test CRUD operations
```

## Notes
- **Model binding**: Resource phải match với Model class đã tạo
- **Authorization**: `canCreate()`, `canEdit()`, `canDelete()` override
- **Soft deletes**: Nếu dùng SoftDeletes, add `use SoftDeletingScope` trong `getEloquentQuery`
- **Relationships**: Repeater field cần `->relationship()` và `orderable()`
- **Navigation sort**: Thấp hơn = cao hơn trong menu
- **Widgets**: Thêm vào `getPages()` hoặc `getWidgets()` trong Dashboard

---

**Status**: ⏳ Pending  
**Priority**: Medium  
**Dependencies**: Task 3 (Models), Task 1 (Filament install)  
**Estimated time**: 35 phút
