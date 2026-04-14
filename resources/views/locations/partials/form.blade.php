<div class="row g-3">
    <div class="col-md-6">
        <x-form.text
            name="name"
            label="Nama Lokasi"
            :value="$location->name ?? null"
            required
        />
    </div>
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
    <div class="col-12">
        <x-form.textarea
            name="notes"
            label="Catatan"
            :value="$location->notes ?? null"
            rows="4"
            placeholder="Catatan internal tentang lokasi ini"
        />
    </div>
</div>
