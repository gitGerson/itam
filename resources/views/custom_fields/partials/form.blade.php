<div class="row g-4">
    {{-- Kiri: Nama, Tipe, Nilai --}}
    <div class="col-md-8">
        <x-form.text
            name="name"
            label="Nama Field"
            :value="$customField->name ?? null"
            help="Nama unik untuk mengidentifikasi custom field ini."
            required
        />

        <x-form.select
            name="element"
            label="Tipe Elemen"
            :options="\App\Models\CustomField::elementOptions()"
            :value="$customField->element ?? 'text'"
            placeholder="Pilih tipe elemen"
            required
        />

        <div id="field-values-wrapper" class="{{ in_array($customField->element ?? 'text', \App\Models\CustomField::elementsWithValues()) ? '' : 'd-none' }}">
            <x-form.textarea
                name="field_values"
                label="Pilihan Nilai"
                :value="$customField->field_values ?? null"
                rows="5"
                placeholder="Satu pilihan per baris&#10;Contoh:&#10;Pilihan A&#10;Pilihan B&#10;Pilihan C"
                help="Isi satu pilihan nilai per baris. Digunakan untuk Select, Radio, dan Checkbox."
            />
        </div>

        <x-form.textarea
            name="help_text"
            label="Teks Bantuan"
            :value="$customField->help_text ?? null"
            rows="3"
            placeholder="Teks petunjuk yang ditampilkan di bawah field saat pengisian"
        />
    </div>

    {{-- Kanan: Opsi --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Opsi Field</label>
        <div class="d-flex flex-column gap-3 mt-1">
            <x-form.switch
                name="field_encrypted"
                label="Enkripsi Nilai"
                :checked="$customField->field_encrypted ?? false"
                help="Nilai field akan dienkripsi saat disimpan ke database."
            />
            <x-form.switch
                name="show_in_email"
                label="Tampilkan di Email"
                :checked="$customField->show_in_email ?? false"
                help="Nilai field akan disertakan dalam notifikasi email."
            />
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const elementSelect = document.getElementById('element');
        const fieldValuesWrapper = document.getElementById('field-values-wrapper');
        const elementsWithValues = @json(\App\Models\CustomField::elementsWithValues());

        function toggleFieldValues() {
            if (elementsWithValues.includes(elementSelect.value)) {
                fieldValuesWrapper.classList.remove('d-none');
            } else {
                fieldValuesWrapper.classList.add('d-none');
            }
        }

        if (elementSelect) {
            elementSelect.addEventListener('change', toggleFieldValues);
        }
    });
</script>
