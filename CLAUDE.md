# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 12 inhouse application starter kit using the Sneat Bootstrap admin template, OpenLDAP authentication, and a custom Role-Based Access Control (RBAC) system. Key integrations: `directorytree/ldaprecord-laravel`, `yajra/laravel-datatables-oracle`, `laravolt/avatar`.

## Development Commands

```bash
# Start all services (Laravel, queue, Vite)
composer run dev

# Build assets for production
npm run build

# Run tests
php artisan test --compact
php artisan test --compact --filter=testName   # single test

# Format PHP (run after any PHP change)
vendor/bin/pint --dirty --format agent

# Seed roles and permissions
php artisan db:seed --class=RolePermissionSeeder

# Clear caches
php artisan route:clear && php artisan config:clear && php artisan view:clear
```

## LDAP

- **Provider**: `LdapRecord\Models\OpenLDAP\User` configured in `config/auth.php`
- **Attribute sync**: `cn`→`name`, `uid`→`username`, `mail`→`email`, `guid`→`guid`, `domain`→`domain`
- **Import**: `php artisan ldap:import users --filter="(uid=jptest)"`
- **Config**: `LDAP_HOST`, `LDAP_USERNAME`, `LDAP_PASSWORD`, `LDAP_BASE_DN`, `LDAP_LOGGING` in `.env`

## RBAC System

- **Middleware**: `CheckPermission` registered as `permission` alias in `bootstrap/app.php`
- **User methods**: `hasRole('slug')` and `hasPermission('module.action')`
- **Permission check in Blade**: `@if(auth()->user()->hasPermission('module.action'))`
- **Default test user**: `jptest` — super_admin with full access

### Permission Structure

```
users.*        view, create, edit, delete, restore, force_delete, logs
roles.*        view, create, edit, delete
permissions.*  view, create, edit, delete
dashboard.view
reports.view
settings.*     view, edit
```

### Adding New Permissions

1. Add to `RolePermissionSeeder.php`
2. Protect routes with `permission:module.action` middleware
3. Add `hasPermission()` checks to views and DataTables action columns
4. Run `php artisan db:seed --class=RolePermissionSeeder`

### Route Organization Pattern

```php
Route::middleware(['permission:feature.view'])->group(function () {
    Route::get('/feature', [FeatureController::class, 'index']);
    Route::get('/feature/data', [FeatureController::class, 'getData']);
});

Route::middleware(['permission:feature.create'])->group(function () {
    Route::get('/feature/create', [FeatureController::class, 'create']);
    Route::post('/feature', [FeatureController::class, 'store']);
});
```

## Audit Trail Pattern

- Models use boot events to automatically populate `created_by`, `updated_by`, `deleted_by`
- `UserLog::log()` static method for consistent activity logging across controllers
- Tracked actions: `CREATE_USER`, `UPDATE_USER`, `DELETE_USER`, `RESTORE_USER`, `FORCE_DELETE_USER`, `VIEW_USER`
- Foreign key constraints use `SET NULL` on user deletion

## CRUD Module Development Pattern

Every new CRUD module must follow this structure. See `guide.md` for full detail.

### Standard File Layout

```
app/Http/Controllers/{Module}Controller.php
app/Http/Requests/{Module}StoreRequest.php
app/Http/Requests/{Module}UpdateRequest.php
app/Models/{Module}.php
database/migrations/...create_{table}_table.php
database/factories/{Module}Factory.php
database/seeders/{Module}Seeder.php          (when needed)
resources/views/{module}/index.blade.php
resources/views/{module}/create.blade.php
resources/views/{module}/edit.blade.php
resources/views/{module}/show.blade.php
resources/views/{module}/trash.blade.php     (when soft deletes used)
```

Add `app/Services/{Module}/`, `app/Policies/{Module}Policy.php`, or `app/Http/Resources/` only when complexity justifies it.

### Module Checklist

1. Migration → Model → Factory → Seeder (if needed)
2. StoreRequest + UpdateRequest
3. Controller (thin — delegates to model/service)
4. Named routes grouped by permission capability
5. Register permissions in `RolePermissionSeeder`
6. Views: `index`, `create`, `edit`, `show`, `trash`
7. Feature tests
8. Run `vendor/bin/pint --dirty --format agent`

### Controller Rules

- Thin controllers: render pages, receive validated input, call model/service, return redirect/response
- Move complex query composition, multi-model mutations, or external integrations to service classes
- Use `$request->validated()` always — never inline validate in controllers except trivial one-offs

### Model Rules

- Explicit `$fillable`, typed casts (use `casts()` method not `$casts` property), explicit relationships
- Scopes for reusable filters, accessors for stable derived values
- No HTML generation, redirect logic, or validation in models

### Migration Rules

- One concern per migration
- New CRUD tables: primary key, timestamps, indexes for common lookups, foreign keys
- Use soft deletes only when recovery is needed; if so, add `deleted_by` + `created_by`/`updated_by` for audit

### Listing Screen

- **Server-rendered pagination**: moderate dataset, simple filters, no export needed
- **DataTables** (`yajra/laravel-datatables-oracle`): large dataset, server-side sort/search, export required
- Do not default to DataTables for every module

## Form Components (`x-form.*`)

Use the `x-form.*` component layer as the default for all new forms. These handle labels, `old()` input, validation errors, invalid classes, and help text consistently.

### Available Components

| Component | Use for |
|---|---|
| `x-form.text` | Names, titles, slugs, short text |
| `x-form.number` | Numeric fields |
| `x-form.textarea` | Descriptions, notes, comments |
| `x-form.select` | Dropdowns — supports arrays, object lists, or slot |
| `x-form.checkbox` | Simple boolean |
| `x-form.switch` | Boolean as toggle UI |
| `x-form.date` | Dates — native or daterangepicker mode |
| `x-form.file` | Files — native or FilePond mode |
| `x-form.rich-editor` | TipTap rich text editor |
| `x-form.group` | Wrapper for custom field components only |
| `x-form.error` | Error renderer inside custom components only |

### Key Rules

- The `name` prop drives: HTML name, generated id, error lookup, `old()` resolution
- Components handle `old()` by default — pass `:old="false"` only for fixed display values
- Use `groupClass` for wrapper spacing, `inputClass` for control-level styling
- Do not duplicate label/error/help markup — let the component handle it
- Normalize booleans with `$request->boolean('field')` — do not rely on hidden fallback values

### Select Examples

```blade
{{-- Associative array --}}
<x-form.select name="status" label="Status" :options="['draft'=>'Draft','published'=>'Published']" placeholder="Choose" />

{{-- Object list --}}
<x-form.select name="category_id" label="Category" :options="$categories" optionValue="id" optionLabel="name" />

{{-- Slot --}}
<x-form.select name="category_id" label="Category">
    @foreach ($categories as $c)
        <option value="{{ $c->id }}">{{ $c->name }}</option>
    @endforeach
</x-form.select>
```

### FilePond Example

```blade
<x-form.file
    name="attachment"
    label="Attachment"
    mode="filepond"
    accept="image/*,.pdf"
    :acceptedFileTypes="['image/png','image/jpeg','application/pdf']"
    maxFileSize="5MB"
/>
```

Default FilePond endpoints: `uploads.process`, `uploads.revert`, `uploads.load`. Backend validation must accept both a normal upload and a stored temp path.

### Date Picker Example

```blade
{{-- Single date --}}
<x-form.date name="event_date" label="Event Date" mode="daterangepicker" :single="true" />

{{-- Range --}}
<x-form.date name="window" label="Booking Window" mode="daterangepicker" :single="false" value="2026-04-10 - 2026-04-20" />
```

### Extend vs. Rebuild

Create or extend a reusable component when the same field pattern appears across more than one module, needs shared JS behavior, or has repeated validation/error/help markup. Do not create a new component for a truly one-off field.

Enhancement JS lives in `resources/js/modules/{module}.js` (e.g. `filepond.js`, `rich-editor.js`, `date-range-picker.js`).

## Frontend

- **Template**: Sneat Bootstrap admin at `public/sneat/`
- **Build**: Vite (`resources/sass/app.scss`, `resources/js/app.js`)
- **Layouts**: `resources/views/layouts/sneat.blade.php` (admin), `layouts/auth.blade.php` (login)
- If a frontend change isn't visible, the user needs to run `npm run build` or `composer run dev`
