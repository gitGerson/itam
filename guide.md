# CRUD Module Development Guide

This guide defines the generic structure that should be followed when building new CRUD modules.

It is intentionally project-agnostic.

## Goal

Build CRUD modules with:
- clear boundaries
- predictable file layout
- thin controllers
- reusable validation
- maintainable views
- scalable growth when a simple CRUD later becomes a workflow-heavy module

## Core Rule

A module is not just a table.

A module should group:
- routes
- permissions
- controller
- requests
- model
- views
- tests

around one business concept.

## Standard Module Shape

Every CRUD module should use this structure.

### Backend Files

- `app/Http/Controllers/{Module}Controller.php`
- `app/Http/Requests/{Module}StoreRequest.php`
- `app/Http/Requests/{Module}UpdateRequest.php`
- `app/Models/{Module}.php`
- `database/migrations/...create_{table}_table.php`
- `database/factories/{Module}Factory.php`
- `database/seeders/{Module}Seeder.php`

### Frontend Files

- `resources/views/{module}/index.blade.php`
- `resources/views/{module}/create.blade.php`
- `resources/views/{module}/edit.blade.php`
- `resources/views/{module}/show.blade.php`
- `resources/views/{module}/trash.blade.php`

### Optional Supporting Files

Add these only when complexity justifies them:

- `app/Services/{Module}/...`
- `app/Policies/{Module}Policy.php`
- `app/Http/Resources/...`
- `resources/js/modules/{module}.js`

## Controller Rules

Use one controller per module.

Controller responsibilities:
- render pages
- receive validated requests
- call model/service logic
- return redirects or responses

Controller responsibilities should not include:
- large business rules
- complex query composition
- integration logic
- reusable mutation logic
- formatting-heavy table transformation logic

If a controller starts becoming large, move logic into:
- Form Requests
- service classes
- query builders
- policies

## Request Rules

Every writable CRUD module should have:
- one store request
- one update request

Store request is for:
- required fields
- create-only uniqueness

Update request is for:
- same validation contract
- unique rules that ignore the current record

Rules:
- use array syntax
- use `$request->validated()`
- do not validate inline in controllers unless the case is trivial and one-off

## Model Rules

Every model should define:
- explicit `$fillable`
- explicit casts
- explicit relationships

Models may include:
- accessors for stable derived values
- scopes for reusable filters

Models should not include:
- HTML generation
- redirect logic
- request validation concerns

## Route Rules

Routes should be named and grouped by module capability.

Typical route set:
- `index`
- `create`
- `store`
- `show`
- `edit`
- `update`
- `destroy`
- `trash`
- `restore`
- `force-delete`

Typical capability groups:
- `view`
- `create`
- `edit`
- `delete`
- `restore`
- `force_delete`

Use permission middleware consistently.

## Permission Rules

Permissions should be module-based and capability-based.

Recommended pattern:
- `{domain}.{module}.view`
- `{domain}.{module}.create`
- `{domain}.{module}.edit`
- `{domain}.{module}.delete`
- `{domain}.{module}.restore`
- `{domain}.{module}.force_delete`

Only add special permissions when there is a real workflow need, such as:
- `sync`
- `approve`
- `export`
- `import`

## View Rules

Each module should have a consistent page set:
- listing page
- create form
- edit form
- detail page
- trash page

Use shared Blade components for repeated field patterns.

Do not duplicate:
- labels
- help text
- validation message markup
- repeated form wrappers

when a shared component already exists.

## Form Rules

Forms should be built around:
- Form Request validation
- named routes
- reusable field components

Use shared components for:
- text inputs
- textarea
- number inputs
- select
- checkbox/switch
- file upload
- date input

When a field pattern repeats in more than one module, make it reusable.

## Migration Rules

Use one clear concern per migration.

Every new CRUD table should define:
- primary key
- timestamps
- indexes for common lookups
- foreign keys where relations are real

Use soft deletes only when recovery is needed.

If the module supports recovery, prefer:
- `deleted_at`
- `deleted_by`

If the module supports full auditing, consider:
- `created_by`
- `updated_by`
- `deleted_by`

Do not put unrelated schema changes into the same migration.

## Listing Screen Rules

Use one of two listing styles intentionally.

### Server-rendered pagination

Use when:
- the dataset is moderate
- filters are simple
- export is not required

### DataTables or server-side table endpoint

Use when:
- the dataset is large
- searching and sorting must be server-driven
- export tools are required
- the module is operationally heavy

Do not default to DataTables for every module.

## Trash Pattern

If a module supports soft delete, it should also support:
- trash listing
- restore
- permanent delete

This pattern should be implemented consistently across modules.

## Service Extraction Rules

Create a service class when:
- a mutation affects multiple models
- the same mutation is reused
- transaction boundaries matter
- external APIs are involved
- the controller method stops being simple coordination

Do not create services for trivial one-line persistence unless there is a clear reuse case.

## Query Extraction Rules

Create a dedicated query class or query method when:
- the listing query becomes large
- filtering logic is reused
- joins or aggregates become non-trivial
- the same query feeds multiple screens or exports

Keep Blade templates free of query logic.

## Testing Rules

Each module should have focused feature tests for:
- listing access
- create success
- create validation failure
- update success
- update validation failure
- delete
- restore
- force delete

Add extra tests for:
- permissions
- workflows
- special business rules

Use factories for test data.

## Growth Path

Every module should start simple, but the structure must allow growth.

Recommended evolution path:

1. model + migration
2. requests + controller
3. views + routes + permissions
4. tests
5. extract services/queries if complexity grows

Do not over-engineer at the start.
Do not leave large modules under-structured once they grow.

## Naming Rules

Use:
- singular model class names
- plural route/resource names
- descriptive request names
- descriptive service names

Examples:
- `Product`
- `products.index`
- `ProductStoreRequest`
- `SyncProductCatalogService`

Avoid:
- generic names like `DataController`
- names that collide with framework concepts
- abbreviations unless they are standard in the business domain

## Module Checklist

When building a new CRUD module, follow this checklist:

1. Create the migration.
2. Create the model.
3. Create the factory.
4. Create the seeder if needed.
5. Create store and update Form Requests.
6. Create the controller.
7. Register named routes.
8. Register permissions.
9. Add menu/navigation entry if needed.
10. Build `index`, `create`, `edit`, `show`, and `trash` views.
11. Add feature tests.
12. Run formatting.
13. Run the minimal relevant tests.

## Final Standard

The structure to follow is:
- thin controller
- Form Request validation
- explicit model
- named routes
- capability-based permissions
- one module directory in views
- optional service/query extraction when complexity grows
- consistent trash and recovery behavior when soft deletes are used

If a new CRUD module does not fit this structure, redesign it before implementation.

## Form Components Guide

This section documents the reusable Blade form component approach that should be followed when building forms.

### Goal

Use the `x-form.*` component layer as the default way to build forms.

Benefits:
- consistent labels, help text, and validation output
- shared old-input handling
- shared styling and invalid state handling
- centralized JS-driven enhancements for date range picker, rich editor, and FilePond

### Component List

- `x-form.group`
- `x-form.error`
- `x-form.input`
- `x-form.text`
- `x-form.number`
- `x-form.textarea`
- `x-form.select`
- `x-form.checkbox`
- `x-form.switch`
- `x-form.date`
- `x-form.file`
- `x-form.rich-editor`

### General Rules

- Prefer `x-form.*` components over raw form markup for new forms.
- Keep validation keys aligned with the component `name` prop.
- Let components handle `old()` values unless there is a clear reason not to.
- Use `groupClass` for wrapper spacing/layout and `inputClass` for control-level styling.
- Keep labels, help text, and errors inside the component instead of duplicating markup around it.
- Use named routes and backend-generated URLs when passing upload endpoints or editor endpoints.

### Shared Patterns

#### `name`

The `name` prop is the source of truth for:
- input name
- generated id when `id` is omitted
- validation error lookup
- old input resolution

Example:

```blade
<x-form.text name="title" label="Title" />
```

#### `old`

Most value-based components support `old` and default it to `true`.

Use default behavior for normal form pages:

```blade
<x-form.text name="title" label="Title" :value="$record->title" />
```

Disable old-value override only when a fixed value is required:

```blade
<x-form.text name="token_preview" label="Token" :value="$token" :old="false" readonly />
```

#### Error Handling

Components automatically add invalid classes and render the field error via `x-form.error`.

For file arrays, `x-form.file` should support both:
- `field`
- `field.*`

### Base Components

#### `x-form.group`

Wrapper for:
- label
- required marker
- slotted input
- error output
- help text

Use it only when building a new custom field component, not for normal app form fields.

#### `x-form.error`

Dedicated error renderer for a field name.

Use this only inside custom form components.

### Text-Like Inputs

#### `x-form.input`

Base input component.

Typical props:
- `name`
- `label`
- `type`
- `value`
- `required`
- `help`
- `placeholder`
- `id`
- `inputClass`
- `groupClass`
- `old`

Example:

```blade
<x-form.input
    name="slug"
    label="Slug"
    type="text"
    placeholder="auto-generated-slug"
/>
```

#### `x-form.text`

Shorthand around `x-form.input` for text fields.

Use for:
- names
- titles
- slugs
- short descriptions
- simple readonly values

#### `x-form.number`

Shorthand around `x-form.input` with numeric semantics.

Example:

```blade
<x-form.number
    name="sort_order"
    label="Sort Order"
    min="0"
    :value="$record->sort_order"
/>
```

#### `x-form.textarea`

Use for longer text values such as:
- descriptions
- notes
- comments

Example:

```blade
<x-form.textarea
    name="description"
    label="Description"
    rows="4"
    :value="$record->description"
/>
```

### Selection Components

#### `x-form.select`

Supports:
- associative arrays
- flat list arrays
- object/array lists
- custom option slot content

Example:

```blade
<x-form.select
    name="status"
    label="Status"
    :options="[
        'draft' => 'Draft',
        'review' => 'Review',
        'published' => 'Published',
    ]"
    placeholder="Choose status"
/>
```

Object-list example:

```blade
<x-form.select
    name="role_template_id"
    label="Role Template"
    :options="$roleTemplates"
    optionValue="id"
    optionLabel="display_name"
    :value="$user->role_template_id"
/>
```

Slot example:

```blade
<x-form.select name="category_id" label="Category">
    <option value="">Choose category</option>
    @foreach ($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
    @endforeach
</x-form.select>
```

### Boolean Components

#### `x-form.checkbox`

Use for simple boolean fields.

#### `x-form.switch`

Use when the UI should present the boolean as a switch.

Example:

```blade
<x-form.switch
    name="is_active"
    label="Active"
    :checked="$record->is_active"
/>
```

#### Boolean Submission Rule

Checkbox and switch components should not rely on hidden fallback values automatically.

Normalize booleans with:
- `$request->boolean('field')`
- validated request data plus explicit normalization where needed

### Date Component

#### `x-form.date`

Supports:
- native browser date input
- daterangepicker mode

Native example:

```blade
<x-form.date
    name="publish_date"
    label="Publish Date"
    :value="$record->publish_date?->format('Y-m-d')"
/>
```

Single date picker example:

```blade
<x-form.date
    name="event_date"
    label="Event Date"
    mode="daterangepicker"
    :single="true"
    value="2026-04-10"
/>
```

Range example:

```blade
<x-form.date
    name="booking_window"
    label="Booking Window"
    mode="daterangepicker"
    :single="false"
    value="2026-04-10 - 2026-04-20"
/>
```

Implementation notes:
- Date Range Picker assets are loaded by the component.
- JS initialization should live in `resources/js/modules/date-range-picker.js`.
- If frontend changes do not appear, rebuild frontend assets.

### File Component

#### `x-form.file`

Supports:
- native file input
- FilePond upload mode

Native example:

```blade
<x-form.file
    name="attachment"
    label="Attachment"
    accept="image/*,.pdf"
/>
```

FilePond example:

```blade
<x-form.file
    name="attachment_filepond"
    label="Attachment"
    mode="filepond"
    accept="image/*,.pdf"
    :acceptedFileTypes="['image/png', 'image/jpeg', 'application/pdf']"
    maxFileSize="5MB"
/>
```

Default FilePond endpoints when present:
- `uploads.process`
- `uploads.revert`
- `uploads.load`

Implementation notes:
- JS initialization should live in `resources/js/modules/filepond.js`.
- Existing images can use FilePond preview and poster support.
- For array uploads, use `multiple` and a field name such as `attachments`.
- Backend validation must support either a normal upload or a stored temp path.

### Rich Editor

#### `x-form.rich-editor`

TipTap-based rich editor component.

Example:

```blade
<x-form.rich-editor
    name="body"
    label="Body"
    :value="$record->body"
    placeholder="Write content"
    minHeight="220px"
/>
```

Implementation notes:
- JS initialization should live in `resources/js/modules/rich-editor.js`.
- The component stores content in a hidden input with the same `name`.
- `imageUploadUrl` should be treated as integration-specific configuration.

### Recommended Usage Pattern

Prefer forms structured like this:

```blade
<form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
    @csrf

    <x-form.text name="title" label="Title" required />

    <x-form.select
        name="status"
        label="Status"
        :options="$statusOptions"
        placeholder="Choose status"
        required
    />

    <x-form.textarea
        name="summary"
        label="Summary"
        rows="4"
    />

    <x-form.rich-editor
        name="body"
        label="Body"
    />

    <x-form.file
        name="cover_image"
        label="Cover Image"
        mode="filepond"
        accept="image/*"
        :acceptedFileTypes="['image/png', 'image/jpeg', 'image/webp']"
    />

    <x-form.switch
        name="is_active"
        label="Active"
        :checked="true"
    />

    <button type="submit" class="btn btn-primary">Save</button>
</form>
```

### Consistency Rules

- Do not duplicate label/error/help markup when a form component already covers the case.
- Extend `x-form.input` or `x-form.group` when creating a new custom field type.
- Keep component props predictable and aligned with existing names like `inputClass`, `groupClass`, `help`, `old`, and `required`.
- Put enhancement JS in `resources/js/modules/{module}.js`.
- Use component-driven asset loading patterns consistently.
- Add or update tests when changing non-trivial component behavior.

### When To Extend Instead Of Rebuild

Create or extend a reusable component when:
- the same field pattern appears in more than one form
- the field needs shared JS behavior
- the field has repeated validation/error/help markup
- the field has a stable prop contract that multiple modules can share

Do not create a new component when:
- the pattern is truly one-off
- the markup is simpler than the abstraction
- the new field would duplicate an existing component with only cosmetic differences
