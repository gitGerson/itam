@php
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
                    inputClass="form-control-sm"
                    required
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="contact"
                    label="Contact Person"
                    :value="$supplier->contact ?? null"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-6">
                <x-form.input
                    name="email"
                    type="email"
                    label="Email"
                    :value="$supplier->email ?? null"
                    placeholder="supplier@example.com"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="phone"
                    label="Telepon"
                    :value="$supplier->phone ?? null"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="fax"
                    label="Fax"
                    :value="$supplier->fax ?? null"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-6">
                <x-form.input
                    name="url"
                    type="url"
                    label="Website"
                    :value="$supplier->url ?? null"
                    placeholder="https://example.com"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="address"
                    label="Alamat 1"
                    :value="$supplier->address ?? null"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="address2"
                    label="Alamat 2"
                    :value="$supplier->address2 ?? null"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-3">
                <x-form.text
                    name="city"
                    label="Kota"
                    :value="$supplier->city ?? null"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-3">
                <x-form.text
                    name="state"
                    label="Provinsi"
                    :value="$supplier->state ?? null"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-3">
                <x-form.text
                    name="zip"
                    label="Kode Pos"
                    :value="$supplier->zip ?? null"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-3">
                <x-form.text
                    name="country"
                    label="Negara"
                    :value="$supplier->country ?? null"
                    help="ISO 2 huruf, mis. ID."
                    inputClass="form-control-sm"
                />
            </div>
        </div>
    </div>

    {{-- Kolom kanan: image upload + catatan --}}
    <div class="col-md-4">
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
    </div>

    <div class="col-12">
        <x-form.textarea
            name="notes"
            label="Catatan"
            :value="$supplier->notes ?? null"
            rows="5"
            placeholder="Tambahkan catatan internal bila diperlukan"
            inputClass="form-control-sm"
        />
    </div>
</div>
