@php
    $existingImageUrl = $location?->imageUrl();
@endphp

<div class="row g-4">
    {{-- Kiri: Info utama dan alamat --}}
    <div class="col-md-8">
        <x-form.text
            name="name"
            label="Nama Lokasi"
            :value="$location->name ?? null"
            required
        />

        <div class="row">
            <div class="col-md-6">
                <x-form.select
                    name="company_id"
                    label="Company"
                    :options="$companies"
                    optionValue="id"
                    optionLabel="name"
                    :value="$location->company_id ?? null"
                    placeholder="Pilih company"
                />
            </div>
            <div class="col-md-6">
                <x-form.select
                    name="parent_id"
                    label="Lokasi Induk"
                    :options="$locations"
                    optionValue="id"
                    optionLabel="name"
                    :value="$location->parent_id ?? null"
                    placeholder="Tidak ada (lokasi utama)"
                />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <x-form.select
                    name="manager_id"
                    label="Manager"
                    :options="$managers"
                    optionValue="id"
                    optionLabel="name"
                    :value="$location->manager_id ?? null"
                    placeholder="Pilih manager"
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="phone"
                    label="Telepon"
                    :value="$location->phone ?? null"
                    placeholder="+62 21 1234567"
                />
            </div>
        </div>

        <x-form.textarea
            name="address"
            label="Alamat"
            :value="$location->address ?? null"
            rows="3"
            placeholder="Jl. Contoh No. 1"
        />

        <div class="row">
            <div class="col-md-4">
                <x-form.text
                    name="city"
                    label="Kota"
                    :value="$location->city ?? null"
                />
            </div>
            <div class="col-md-4">
                <x-form.text
                    name="state"
                    label="Provinsi"
                    :value="$location->state ?? null"
                />
            </div>
            <div class="col-md-4">
                <x-form.text
                    name="zip"
                    label="Kode Pos"
                    :value="$location->zip ?? null"
                />
            </div>
        </div>

        <x-form.text
            name="country"
            label="Negara"
            :value="$location->country ?? 'Indonesia'"
        />
    </div>

    {{-- Kanan: Gambar --}}
    <div class="col-md-4">
        @if(isset($showCurrentImage) && $showCurrentImage && $existingImageUrl)
            <div class="mb-3">
                <label class="form-label">Gambar Saat Ini</label>
                <div class="text-center">
                    <img
                        src="{{ $existingImageUrl }}"
                        alt="Gambar {{ $location->name }}"
                        class="img-fluid rounded border bg-white"
                        style="max-height: 180px;"
                    >
                </div>
            </div>
        @endif

        <x-form.file
            name="image"
            label="Gambar Lokasi"
            mode="filepond"
            accept="image/*"
            :acceptedFileTypes="['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp']"
            :existingFiles="$existingImageUrl ? [$existingImageUrl] : []"
            maxFileSize="5MB"
            help="Upload gambar atau foto lokasi. Maksimal 5MB."
        />
    </div>
</div>
