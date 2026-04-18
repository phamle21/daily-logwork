---
name: filament-development
description: "Dùng cho các task liên quan đến Filament 5 resources, forms, tables, actions. Kích hoạt khi user nhắc đến Filament, tạo resource, form, table, action, hoặc custom filament components."
license: MIT
metadata:
  author: project
---

# Filament Development

## Project Context

- **Laravel**: 13
- **Filament**: 5
- **TailwindCSS**: Dùng cho styling

## Basic Usage

### Tạo Resource

```bash
php artisan make:filament-resource Post
```

### Resource Structure

- Location: `app/Filament/Resources/`
- Policies: `app/Policies/`
- Relation managers trong cùng thư mục

### Form Components

Dùng Filament form components:
- `Forms\Components\TextInput`
- `Forms\Components\Select`
- `Forms\Components\RichEditor`
- `Forms\Components\FileUpload`
- `Forms\Components\DateTimePicker`

### Table Columns

Dùng Filament table columns:
- `Tables\Columns\TextColumn`
- `Tables\Columns\ImageColumn`
- `Tables\Columns\BadgeColumn`
- `Tables\Columns\ToggleColumn`

### Actions

- `Actions\CreateAction`
- `Actions\EditAction`
- `Actions\DeleteAction`
- `Actions\ForceDeleteAction`
- `Actions\RestoreAction`

## Livewire Integration

- Filament 5 dùng Livewire mặc định
- Dùng `->action()` cho inline actions
- Dùng `->mutation()` cho form mutations

## Best Practices

- Validate với Form Request classes
- Dùng policies cho authorization
- Table pagination mặc định: 10/25/50/100
- Search trên các columns thường dùng
- Use `canAccess()` để kiểm tra permissions

## Conventions

- Form labels: Title Case
- Button labels: Sentence case
- Resource plural name cho navigation
- Schema documentation trong code