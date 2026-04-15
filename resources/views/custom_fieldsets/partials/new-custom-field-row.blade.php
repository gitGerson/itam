@php
    $field = $field ?? [];
    $source = $field['source'] ?? 'new';
    $isExisting = $source === 'existing';
    $showFieldValues = in_array($field['element'] ?? 'text', \App\Models\CustomField::elementsWithValues(), true);
@endphp

<div class="js-fieldset-field-row pb-3 border-bottom" data-row>
    <input type="hidden" name="fieldset_fields[{{ $index }}][source]" value="{{ $source }}" data-source-input>
    <input type="hidden" name="fieldset_fields[{{ $index }}][custom_field_id]" value="{{ $field['custom_field_id'] ?? '' }}" data-custom-field-id-input>

    <div class="d-flex flex-wrap flex-xl-nowrap gap-2 align-items-end">
        <div class="flex-fill" style="min-width: 180px;">
            <label class="form-label d-flex align-items-center gap-2" for="fieldset_fields_{{ $index }}_name">
                <span>Nama Field</span>
                <span class="badge bg-label-{{ $isExisting ? 'info' : 'success' }} small js-field-status-label">
                    {{ $isExisting ? 'Existing' : 'New' }}
                </span>
            </label>
            <div class="position-relative">
                <input
                    type="text"
                    class="form-control form-control-sm js-field-name-input @error("fieldset_fields.$index.name") is-invalid @enderror"
                    id="fieldset_fields_{{ $index }}_name"
                    name="fieldset_fields[{{ $index }}][name]"
                    value="{{ $field['name'] ?? '' }}"
                    data-field-key="name"
                    autocomplete="off"
                >
                <div class="list-group position-absolute start-0 end-0 mt-1 shadow-sm d-none js-field-name-suggestions bg-white" style="z-index: 10; max-height: 180px; overflow-y: auto; background-color: #fff;"></div>
            </div>
            @error("fieldset_fields.$index.name")
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error("fieldset_fields.$index.custom_field_id")
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex-fill" style="min-width: 200px;">
            <label class="form-label" for="fieldset_fields_{{ $index }}_help_text">Teks Bantuan</label>
            <input
                type="text"
                class="form-control form-control-sm js-field-input js-field-lockable @error("fieldset_fields.$index.help_text") is-invalid @enderror"
                id="fieldset_fields_{{ $index }}_help_text"
                name="fieldset_fields[{{ $index }}][help_text]"
                value="{{ $field['help_text'] ?? '' }}"
                data-field-key="help_text"
            >
            @error("fieldset_fields.$index.help_text")
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex-shrink-0" style="width: 150px;">
            <label class="form-label" for="fieldset_fields_{{ $index }}_element">Tipe Elemen</label>
            <select
                class="form-select form-select-sm js-field-input js-field-lockable js-field-element @error("fieldset_fields.$index.element") is-invalid @enderror"
                id="fieldset_fields_{{ $index }}_element"
                name="fieldset_fields[{{ $index }}][element]"
                data-field-key="element"
            >
                @foreach(\App\Models\CustomField::elementOptions() as $value => $label)
                    <option value="{{ $value }}" @selected(($field['element'] ?? 'text') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error("fieldset_fields.$index.element")
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex-shrink-0" style="width: 120px;">
            <div class="form-check form-switch mt-4">
                <input
                    class="form-check-input js-field-input js-field-lockable"
                    type="checkbox"
                    role="switch"
                    id="fieldset_fields_{{ $index }}_field_encrypted"
                    name="fieldset_fields[{{ $index }}][field_encrypted]"
                    value="1"
                    data-field-key="field_encrypted"
                    @checked(! empty($field['field_encrypted']))
                >
                <label class="form-check-label small" for="fieldset_fields_{{ $index }}_field_encrypted">Encrypted</label>
            </div>
        </div>

        <div class="flex-shrink-0" style="width: 110px;">
            <div class="form-check form-switch mt-4">
                <input
                    class="form-check-input js-field-input js-field-lockable"
                    type="checkbox"
                    role="switch"
                    id="fieldset_fields_{{ $index }}_show_in_email"
                    name="fieldset_fields[{{ $index }}][show_in_email]"
                    value="1"
                    data-field-key="show_in_email"
                    @checked(! empty($field['show_in_email']))
                >
                <label class="form-check-label small" for="fieldset_fields_{{ $index }}_show_in_email">In Email</label>
            </div>
        </div>

        <div class="flex-shrink-0 d-flex align-items-center pt-4">
            <button
                type="button"
                class="btn btn-sm btn-outline-danger px-2 js-remove-field-row"
                aria-label="Hapus row"
                title="Hapus row"
            >
                <i class="bx bx-trash"></i>
            </button>
        </div>
    </div>

    <div class="@class(['mt-2', 'd-none' => ! $showFieldValues])" data-field-values-wrapper>
        <label class="form-label" for="fieldset_fields_{{ $index }}_field_values">Pilihan Nilai</label>
        <textarea
            class="form-control form-control-sm js-field-input js-field-lockable @error("fieldset_fields.$index.field_values") is-invalid @enderror"
            id="fieldset_fields_{{ $index }}_field_values"
            name="fieldset_fields[{{ $index }}][field_values]"
            data-field-key="field_values"
            rows="2"
        >{{ $field['field_values'] ?? '' }}</textarea>
        @error("fieldset_fields.$index.field_values")
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
