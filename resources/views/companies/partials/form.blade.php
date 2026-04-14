@php
    $fieldPrefix = $fieldPrefix ?? 'company';
    $showCurrentImage = $showCurrentImage ?? false;
    $existingImageUrl = $company?->imageUrl();
@endphp

<div class="row g-3">
    {{-- Kolom kiri: field teks --}}
    <div class="col-md-8">
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
            <div class="col-12">
                <x-form.textarea
                    name="notes"
                    :id="$fieldPrefix.'_notes'"
                    label="Catatan"
                    :value="$company->notes ?? null"
                    rows="4"
                    placeholder="Tambahkan catatan internal jika diperlukan"
                />
            </div>
        </div>
    </div>

    {{-- Kolom kanan: image upload --}}
    <div class="col-md-4">
        @if($showCurrentImage)
            <div id="{{ $fieldPrefix }}_current_image_wrapper" class="mb-3 {{ $existingImageUrl ? '' : 'd-none' }}">
                <label class="form-label">Logo Saat Ini</label>
                <div class="d-flex align-items-start gap-2">
                    <img
                        id="{{ $fieldPrefix }}_current_image_preview"
                        src="{{ $existingImageUrl ?: '' }}"
                        alt="Current company logo"
                        class="rounded border bg-white flex-shrink-0"
                        style="width: 56px; height: 56px; object-fit: cover;"
                    >
                    <div class="small text-muted text-break lh-sm" id="{{ $fieldPrefix }}_current_image_path">
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
            help="Upload logo company. Maksimal 5MB."
        />
    </div>
</div>
