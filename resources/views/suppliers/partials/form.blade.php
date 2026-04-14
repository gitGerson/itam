@php
    $showCurrentImage = $showCurrentImage ?? false;
    $existingImageUrl = $supplier?->imageUrl();
@endphp

<div class="row g-3">
    {{-- Kolom kiri: field teks --}}
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <x-form.text
                    name="name"
                    label="Nama Supplier"
                    :value="$supplier->name ?? null"
                    help="Nama supplier harus unik."
                    required
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="contact"
                    label="Contact Person"
                    :value="$supplier->contact ?? null"
                />
            </div>
            <div class="col-md-6">
                <x-form.input
                    name="email"
                    type="email"
                    label="Email"
                    :value="$supplier->email ?? null"
                    placeholder="supplier@example.com"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="phone"
                    label="Telepon"
                    :value="$supplier->phone ?? null"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="fax"
                    label="Fax"
                    :value="$supplier->fax ?? null"
                />
            </div>
            <div class="col-md-6">
                <x-form.input
                    name="url"
                    type="url"
                    label="Website"
                    :value="$supplier->url ?? null"
                    placeholder="https://example.com"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="address"
                    label="Alamat 1"
                    :value="$supplier->address ?? null"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="address2"
                    label="Alamat 2"
                    :value="$supplier->address2 ?? null"
                />
            </div>
            <div class="col-md-4">
                <x-form.text
                    name="city"
                    label="Kota"
                    :value="$supplier->city ?? null"
                />
            </div>
            <div class="col-md-4">
                <x-form.text
                    name="state"
                    label="Provinsi"
                    :value="$supplier->state ?? null"
                />
            </div>
            <div class="col-md-2">
                <x-form.text
                    name="zip"
                    label="Kode Pos"
                    :value="$supplier->zip ?? null"
                />
            </div>
            <div class="col-md-2">
                <x-form.text
                    name="country"
                    label="Negara"
                    :value="$supplier->country ?? null"
                    help="ISO 2 huruf, mis. ID."
                />
            </div>
        </div>
    </div>

    {{-- Kolom kanan: image upload + catatan --}}
    <div class="col-md-4">
        @if($showCurrentImage)
            <div class="mb-3 {{ $existingImageUrl ? '' : 'd-none' }}">
                <label class="form-label">Logo Saat Ini</label>
                <div class="d-flex align-items-start gap-2">
                    <img
                        src="{{ $existingImageUrl ?: '' }}"
                        alt="Current supplier logo"
                        class="rounded border bg-white flex-shrink-0"
                        style="width: 56px; height: 56px; object-fit: cover;"
                    >
                    <div class="small text-muted text-break lh-sm">
                        {{ $supplier?->image ?: '' }}
                    </div>
                </div>
            </div>
        @endif

        <x-form.file
            name="image"
            label="Logo Supplier"
            mode="filepond"
            accept="image/*"
            :acceptedFileTypes="['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp']"
            :existingFiles="$existingImageUrl ? [$existingImageUrl] : []"
            maxFileSize="5MB"
            help="Upload logo supplier. Maksimal 5MB."
        />

        <x-form.textarea
            name="notes"
            label="Catatan"
            :value="$supplier->notes ?? null"
            rows="5"
            placeholder="Tambahkan catatan internal bila diperlukan"
        />
    </div>
</div>
