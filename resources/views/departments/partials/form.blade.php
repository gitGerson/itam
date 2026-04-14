<div class="row g-4">
    {{-- Kiri: Info utama --}}
    <div class="col-md-7">
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
    </div>

    {{-- Kanan: Catatan --}}
    <div class="col-md-5">
        <x-form.textarea
            name="notes"
            label="Catatan"
            :value="$department->notes ?? null"
            rows="7"
            placeholder="Deskripsi atau catatan internal tentang departemen ini"
        />
    </div>
</div>
