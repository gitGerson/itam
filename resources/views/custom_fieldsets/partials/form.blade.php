@php
    $selectedIds = collect($customFieldset?->customFields ?? [])->pluck('id')->toArray();
@endphp

<div class="row g-4">
    {{-- Kiri: Nama dan Catatan --}}
    <div class="col-md-5">
        <x-form.text
            name="name"
            label="Nama Fieldset"
            :value="$customFieldset->name ?? null"
            help="Nama unik untuk grup custom fields ini."
            required
        />

        <x-form.textarea
            name="notes"
            label="Catatan"
            :value="$customFieldset->notes ?? null"
            rows="5"
            placeholder="Deskripsi atau catatan internal tentang fieldset ini"
        />

        <x-form.switch
            name="repeatable"
            label="Repeatable"
            :value="$customFieldset->repeatable ?? false"
            help="Aktifkan jika fieldset ini dapat diisi berulang (lebih dari satu kali) pada satu record."
        />
    </div>

    {{-- Kanan: Pilih Custom Fields --}}
    <div class="col-md-7">
        <div class="mb-3">
            <label class="form-label fw-semibold">Custom Fields</label>
            <div class="text-muted small mb-2">Pilih field yang akan dimasukkan ke dalam fieldset ini.</div>

            @if($customFields->isEmpty())
                <div class="alert alert-warning py-2">
                    Belum ada custom field. <a href="{{ route('custom-fields.create') }}">Buat custom field</a> terlebih dahulu.
                </div>
            @else
                <div class="border rounded-3 p-3" style="max-height: 380px; overflow-y: auto;">
                    @foreach($customFields as $field)
                        <div class="form-check mb-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="custom_fields[]"
                                value="{{ $field->id }}"
                                id="cf_{{ $field->id }}"
                                {{ in_array($field->id, $selectedIds) ? 'checked' : '' }}
                            >
                            <label class="form-check-label d-flex align-items-center gap-2" for="cf_{{ $field->id }}">
                                <span>{{ $field->name }}</span>
                                <span class="badge bg-label-secondary small">
                                    {{ \App\Models\CustomField::elementOptions()[$field->element] ?? $field->element }}
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>

                @error('custom_fields')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('custom_fields.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror

                <div class="mt-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary js-check-all">Pilih Semua</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary js-uncheck-all">Hapus Pilihan</button>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.js-check-all')?.addEventListener('click', function () {
            document.querySelectorAll('input[name="custom_fields[]"]').forEach(cb => cb.checked = true);
        });
        document.querySelector('.js-uncheck-all')?.addEventListener('click', function () {
            document.querySelectorAll('input[name="custom_fields[]"]').forEach(cb => cb.checked = false);
        });
    });
</script>
