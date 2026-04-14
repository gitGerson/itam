@php
    $showCurrentImage = $showCurrentImage ?? false;
    $existingImageUrl = $manufacturer?->imageUrl();
@endphp

<div class="row g-3">
    {{-- Kolom kiri: field teks --}}
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <x-form.text
                    name="name"
                    label="Nama Manufacturer"
                    :value="$manufacturer->name ?? null"
                    help="Nama manufacturer harus unik."
                    required
                />
            </div>
            <div class="col-md-6">
                <x-form.input
                    name="url"
                    type="url"
                    label="Website"
                    :value="$manufacturer->url ?? null"
                    placeholder="https://example.com"
                />
            </div>
            <div class="col-md-6">
                <x-form.input
                    name="support_url"
                    type="url"
                    label="Support URL"
                    :value="$manufacturer->support_url ?? null"
                    placeholder="https://support.example.com"
                />
            </div>
            <div class="col-md-6">
                <x-form.input
                    name="warranty_lookup_url"
                    type="url"
                    label="Warranty Lookup URL"
                    :value="$manufacturer->warranty_lookup_url ?? null"
                    placeholder="https://warranty.example.com"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="support_phone"
                    label="Support Phone"
                    :value="$manufacturer->support_phone ?? null"
                />
            </div>
            <div class="col-md-6">
                <x-form.input
                    name="support_email"
                    type="email"
                    label="Support Email"
                    :value="$manufacturer->support_email ?? null"
                    placeholder="support@example.com"
                />
            </div>
            <div class="col-12">
                <x-form.textarea
                    name="notes"
                    label="Catatan"
                    :value="$manufacturer->notes ?? null"
                    rows="4"
                    placeholder="Tambahkan catatan internal bila diperlukan"
                />
            </div>
        </div>
    </div>

    {{-- Kolom kanan: image upload --}}
    <div class="col-md-4">
        @if($showCurrentImage)
            <div class="mb-3 {{ $existingImageUrl ? '' : 'd-none' }}">
                <label class="form-label">Logo Saat Ini</label>
                <div class="d-flex align-items-start gap-2">
                    <img
                        src="{{ $existingImageUrl ?: '' }}"
                        alt="Current manufacturer logo"
                        class="rounded border bg-white flex-shrink-0"
                        style="width: 56px; height: 56px; object-fit: cover;"
                    >
                    <div class="small text-muted text-break lh-sm">
                        {{ $manufacturer?->image ?: '' }}
                    </div>
                </div>
            </div>
        @endif

        <x-form.file
            name="image"
            label="Logo Manufacturer"
            mode="filepond"
            accept="image/*"
            :acceptedFileTypes="['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp']"
            :existingFiles="$existingImageUrl ? [$existingImageUrl] : []"
            maxFileSize="5MB"
            help="Upload logo manufacturer. Maksimal 5MB."
        />
    </div>
</div>
