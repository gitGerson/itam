@php
    $existingImageUrl = $department?->imageUrl();
@endphp

<div class="row g-4">
    {{-- Kiri: Info utama --}}
    <div class="col-md-8">
        <x-form.text
            name="name"
            label="Nama Departemen"
            :value="$department->name ?? null"
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
                    :value="$department->company_id ?? null"
                    placeholder="Pilih company"
                />
            </div>
            <div class="col-md-6">
                <x-form.select
                    name="location_id"
                    label="Lokasi"
                    :options="$locations"
                    optionValue="id"
                    optionLabel="name"
                    :value="$department->location_id ?? null"
                    placeholder="Pilih lokasi"
                />
            </div>
        </div>

        <x-form.select
            name="manager_id"
            label="Manager"
            :options="$managers"
            optionValue="id"
            optionLabel="name"
            :value="$department->manager_id ?? null"
            placeholder="Pilih manager"
        />

        <x-form.textarea
            name="notes"
            label="Catatan"
            :value="$department->notes ?? null"
            rows="4"
            placeholder="Deskripsi atau catatan internal tentang departemen ini"
        />
    </div>

    {{-- Kanan: Gambar --}}
    <div class="col-md-4">
        <x-form.file
            name="image"
            label="Gambar"
            mode="filepond"
            accept="image/*"
            :acceptedFileTypes="['image/png', 'image/jpeg', 'image/gif', 'image/webp']"
            maxFileSize="5MB"
            :existingFiles="$existingImageUrl ? [$existingImageUrl] : []"
        />
    </div>
</div>
