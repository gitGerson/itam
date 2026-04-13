@php
    $fieldPrefix = $fieldPrefix ?? 'category';
    $showCurrentImage = $showCurrentImage ?? false;
    $existingImageUrl = $category?->imageUrl();
@endphp

<div class="row">
    <div class="col-md-6">
        <x-form.text
            name="name"
            :id="$fieldPrefix.'_name'"
            label="Nama Kategori"
            :value="$category->name ?? null"
            help="Gunakan nama kategori yang jelas untuk modul inventory."
            required
        />
    </div>
    <div class="col-md-6">
        <x-form.select
            name="category_type"
            :id="$fieldPrefix.'_category_type'"
            label="Tipe Kategori"
            :options="\App\Models\Category::categoryTypeOptions()"
            :value="$category->category_type ?? 'asset'"
            required
        />
    </div>
</div>

@if($showCurrentImage)
    <div id="{{ $fieldPrefix }}_current_image_wrapper" class="mb-3 {{ $existingImageUrl ? '' : 'd-none' }}">
        <label class="form-label">Gambar Saat Ini</label>
        <div class="d-flex align-items-start gap-3">
            <img
                id="{{ $fieldPrefix }}_current_image_preview"
                src="{{ $existingImageUrl ?: '' }}"
                alt="Current category image"
                class="rounded border bg-white"
                style="width: 72px; height: 72px; object-fit: cover;"
            >
            <div class="small text-muted text-break" id="{{ $fieldPrefix }}_current_image_path">
                {{ $category?->image ?: '' }}
            </div>
        </div>
    </div>
@endif

<x-form.file
    name="image"
    :id="$fieldPrefix.'_image'"
    label="Gambar Kategori"
    mode="filepond"
    accept="image/*"
    :acceptedFileTypes="['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp']"
    :existingFiles="$existingImageUrl ? [$existingImageUrl] : []"
    maxFileSize="5MB"
    help="Upload gambar kategori. Maksimal 5MB."
/>

<x-form.textarea
    name="notes"
    :id="$fieldPrefix.'_notes'"
    label="Catatan"
    :value="$category->notes ?? null"
    rows="4"
    placeholder="Tambahkan catatan internal bila diperlukan"
/>
