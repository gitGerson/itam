@php
    $fieldPrefix = $fieldPrefix ?? 'company';
    $showCurrentImage = $showCurrentImage ?? false;
    $existingImageUrl = $company?->imageUrl();
@endphp

<div class="row">
    <div class="col-md-6">
        <x-form.text
            name="name"
            :id="$fieldPrefix.'_name'"
            label="Nama Company"
            :value="$company->name ?? null"
            help="Nama company harus unik."
            required
        />
    </div>
    <div class="col-md-6">
        <x-form.input
            name="email"
            :id="$fieldPrefix.'_email'"
            type="email"
            label="Email"
            :value="$company->email ?? null"
            placeholder="company@example.com"
        />
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <x-form.text
            name="phone"
            :id="$fieldPrefix.'_phone'"
            label="Telepon"
            :value="$company->phone ?? null"
            placeholder="08xxxxxxxxxx"
        />
    </div>
    <div class="col-md-6">
        <x-form.text
            name="fax"
            :id="$fieldPrefix.'_fax'"
            label="Fax"
            :value="$company->fax ?? null"
        />
    </div>
</div>

@if($showCurrentImage)
    <div id="{{ $fieldPrefix }}_current_image_wrapper" class="mb-3 {{ $existingImageUrl ? '' : 'd-none' }}">
        <label class="form-label">Logo Saat Ini</label>
        <div class="d-flex align-items-start gap-3">
            <img
                id="{{ $fieldPrefix }}_current_image_preview"
                src="{{ $existingImageUrl ?: '' }}"
                alt="Current company logo"
                class="rounded border bg-white"
                style="width: 72px; height: 72px; object-fit: cover;"
            >
            <div class="small text-muted text-break" id="{{ $fieldPrefix }}_current_image_path">
                {{ $company?->image ?: '' }}
            </div>
        </div>
    </div>
@endif

<x-form.file
    name="image"
    :id="$fieldPrefix.'_image'"
    label="Logo Company"
    mode="filepond"
    accept="image/*"
    :acceptedFileTypes="['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp']"
    :existingFiles="$existingImageUrl ? [$existingImageUrl] : []"
    maxFileSize="5MB"
    help="Upload logo company dengan format gambar. Maksimal 5MB. Pada halaman edit, logo saat ini akan ditampilkan."
/>

<x-form.textarea
    name="notes"
    :id="$fieldPrefix.'_notes'"
    label="Catatan"
    :value="$company->notes ?? null"
    rows="4"
    placeholder="Tambahkan catatan internal jika diperlukan"
/>
