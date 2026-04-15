@php
    $existingImageUrl = $assetModel?->imageUrl();
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <x-form.text
                    name="name"
                    label="Nama Model"
                    :value="$assetModel->name ?? null"
                    help="Nama model harus unik."
                    inputClass="form-control-sm"
                    required
                />
            </div>
            <div class="col-md-6">
                <x-form.text
                    name="model_number"
                    label="Model Number"
                    :value="$assetModel->model_number ?? null"
                    placeholder="Contoh: 21KC0001ID"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-md-3">
                <x-form.select
                    name="manufacturer_id"
                    label="Manufacturer"
                    :options="$manufacturers"
                    optionValue="id"
                    optionLabel="name"
                    :value="$assetModel->manufacturer_id ?? null"
                    placeholder="Pilih manufacturer"
                    inputClass="form-select-sm"
                />
            </div>
            <div class="col-md-3">
                <x-form.select
                    name="category_id"
                    label="Kategori"
                    :options="$categories"
                    optionValue="id"
                    optionLabel="name"
                    :value="$assetModel->category_id ?? null"
                    placeholder="Pilih kategori"
                    inputClass="form-select-sm"
                />
            </div>
            <div class="col-md-3">
                <x-form.select
                    name="fieldset_id"
                    label="Fieldset"
                    :options="$fieldsets"
                    optionValue="id"
                    optionLabel="name"
                    :value="$assetModel->fieldset_id ?? null"
                    placeholder="Pilih fieldset"
                    inputClass="form-select-sm"
                />
            </div>
            <div class="col-md-3">
                <x-form.number
                    name="eol"
                    label="End Of Life (EOL)"
                    :value="$assetModel->eol ?? null"
                    min="0"
                    placeholder="dalam bulan"
                    inputClass="form-control-sm"
                />
            </div>
            <div class="col-12">
                <x-form.textarea
                    name="notes"
                    label="Catatan"
                    :value="$assetModel->notes ?? null"
                    rows="4"
                    placeholder="Tambahkan catatan internal bila diperlukan"
                    inputClass="form-control-sm"
                />
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <x-form.file
            name="image"
            label="Gambar Model"
            mode="filepond"
            accept="image/*"
            :acceptedFileTypes="['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp']"
            :existingFiles="$existingImageUrl ? [$existingImageUrl] : []"
            maxFileSize="5MB"
            help="Upload gambar model. Maksimal 5MB."
        />
    </div>
</div>
