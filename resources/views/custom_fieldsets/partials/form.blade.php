@php
    $existingFieldRows = collect($customFieldset?->customFields ?? [])->map(function ($field) {
        return [
            'source' => 'existing',
            'custom_field_id' => $field->id,
            'name' => $field->name,
            'element' => $field->element,
            'format' => $field->format,
            'field_values' => $field->field_values,
            'help_text' => $field->help_text,
            'field_encrypted' => $field->field_encrypted,
            'show_in_email' => $field->show_in_email,
        ];
    })->all();
    $fieldsetFields = old('fieldset_fields', $existingFieldRows);
    $canCreateInlineCustomFields = auth()->user()?->hasPermission('settings.custom_fields.create');
    $customFieldCatalog = $customFields->mapWithKeys(function ($field) {
        return [$field->id => [
            'id' => $field->id,
            'name' => $field->name,
            'element' => $field->element,
            'format' => $field->format,
            'field_values' => $field->field_values,
            'help_text' => $field->help_text,
            'field_encrypted' => (bool) $field->field_encrypted,
            'show_in_email' => (bool) $field->show_in_email,
        ]];
    });
@endphp

<div class="row g-4">
    <div class="col-12">
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

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label fw-semibold mb-0">Custom Fields</label>
            <button type="button" class="btn btn-sm btn-primary js-add-field-row" aria-label="Tambah row" title="Tambah row">+</button>
        </div>
        <div class="text-muted small mb-2">Satu repeater untuk field baru maupun existing. Ketik nama field untuk melihat saran custom field yang sudah ada.</div>

        @if(! $canCreateInlineCustomFields)
            <div class="alert alert-warning py-2 mb-3">
                Anda tidak memiliki izin untuk membuat custom field baru. Tambahkan field existing saja.
            </div>
        @endif

        <div id="fieldset-fields-container" class="border rounded-3 p-3 d-flex flex-column gap-3" data-next-index="{{ count($fieldsetFields) }}">
            @forelse($fieldsetFields as $index => $field)
                @include('custom_fieldsets.partials.new-custom-field-row', ['index' => $index, 'field' => $field, 'customFields' => $customFields])
            @empty
                <div class="text-muted small js-field-empty-state">
                    Belum ada custom field pada fieldset ini. Klik "Tambah Row" untuk menambahkan baris.
                </div>
            @endforelse
        </div>

        <template id="fieldset-field-template">
            @include('custom_fieldsets.partials.new-custom-field-row', ['index' => '__INDEX__', 'field' => ['source' => 'new'], 'customFields' => $customFields])
        </template>

        @error('fieldset_fields')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const elementsWithValues = @json(\App\Models\CustomField::elementsWithValues());
        const canCreateInlineCustomFields = @json($canCreateInlineCustomFields);
        const customFieldCatalog = @json($customFieldCatalog);
        const customFieldOptions = Object.values(customFieldCatalog);
        const customFieldNameCatalog = customFieldOptions.reduce(function (carry, field) {
            carry[(field.name || '').trim().toLowerCase()] = field;
            return carry;
        }, {});
        const fieldsContainer = document.getElementById('fieldset-fields-container');
        const fieldTemplate = document.getElementById('fieldset-field-template');

        function toggleFieldValues(row) {
            const elementSelect = row.querySelector('.js-field-element');
            const fieldValuesWrapper = row.querySelector('[data-field-values-wrapper]');

            if (!elementSelect || !fieldValuesWrapper) {
                return;
            }

            fieldValuesWrapper.classList.toggle('d-none', !elementsWithValues.includes(elementSelect.value));
        }

        function populateExistingField(row, customFieldId) {
            const customField = customFieldCatalog[customFieldId];

            if (!customField) {
                row.querySelector('[data-custom-field-id-input]').value = '';
                return;
            }

            row.querySelector('[data-custom-field-id-input]').value = customField.id;

            row.querySelectorAll('.js-field-input').forEach(function (input) {
                const fieldKey = input.dataset.fieldKey;

                if (!fieldKey) {
                    return;
                }

                if (input instanceof HTMLInputElement && input.type === 'checkbox') {
                    input.checked = Boolean(customField[fieldKey]);
                } else {
                    input.value = customField[fieldKey] ?? '';
                }
            });

            toggleFieldValues(row);
        }

        function updateFieldStatusLabel(row, source) {
            const statusLabel = row.querySelector('.js-field-status-label');

            if (!statusLabel) {
                return;
            }

            statusLabel.textContent = source === 'existing' ? 'Existing' : 'New';
            statusLabel.className = 'badge small js-field-status-label ' + (source === 'existing' ? 'bg-label-info' : 'bg-label-success');
        }

        function setFieldRowMode(row, source) {
            const sourceInput = row.querySelector('[data-source-input]');
            const customFieldIdInput = row.querySelector('[data-custom-field-id-input]');
            const lockableInputs = row.querySelectorAll('.js-field-lockable');
            const isExisting = source === 'existing';

            if (sourceInput) {
                sourceInput.value = source;
            }

            if (!isExisting && customFieldIdInput) {
                customFieldIdInput.value = '';
            }

            lockableInputs.forEach(function (input) {
                input.disabled = isExisting;
            });

            updateFieldStatusLabel(row, source);
            toggleFieldValues(row);
        }

        function syncRowFromName(row) {
            const nameInput = row.querySelector('.js-field-name-input');

            if (!(nameInput instanceof HTMLInputElement)) {
                return;
            }

            const matchedField = customFieldNameCatalog[nameInput.value.trim().toLowerCase()];

            if (matchedField) {
                populateExistingField(row, matchedField.id);
                setFieldRowMode(row, 'existing');
                nameInput.value = matchedField.name;
                return;
            }

            setFieldRowMode(row, 'new');
        }

        function closeSuggestions(row) {
            const suggestions = row.querySelector('.js-field-name-suggestions');

            if (!suggestions) {
                return;
            }

            suggestions.classList.add('d-none');
            suggestions.innerHTML = '';
            suggestions.dataset.activeIndex = '-1';
        }

        function getSuggestionButtons(row) {
            return Array.from(row.querySelectorAll('.js-field-suggestion'));
        }

        function setActiveSuggestion(row, nextIndex) {
            const suggestions = row.querySelector('.js-field-name-suggestions');
            const buttons = getSuggestionButtons(row);

            if (!suggestions || buttons.length === 0) {
                return;
            }

            const safeIndex = Math.max(0, Math.min(nextIndex, buttons.length - 1));

            buttons.forEach(function (button, index) {
                button.classList.toggle('active', index === safeIndex);
            });

            suggestions.dataset.activeIndex = String(safeIndex);
            buttons[safeIndex].scrollIntoView({ block: 'nearest' });
        }

        function applyExistingFieldSelection(row, field) {
            const nameInput = row.querySelector('.js-field-name-input');

            if (nameInput instanceof HTMLInputElement) {
                nameInput.value = field.name;
            }

            populateExistingField(row, field.id);
            setFieldRowMode(row, 'existing');
            closeSuggestions(row);
        }

        function renderSuggestions(row, keyword) {
            const suggestions = row.querySelector('.js-field-name-suggestions');

            if (!suggestions) {
                return;
            }

            const normalizedKeyword = keyword.trim().toLowerCase();
            const matches = customFieldOptions
                .filter(function (field) {
                    if (normalizedKeyword.length === 0) {
                        return true;
                    }

                    return field.name.toLowerCase().includes(normalizedKeyword);
                })
                .slice(0, 6);

            if (matches.length === 0) {
                closeSuggestions(row);
                return;
            }

            suggestions.innerHTML = matches.map(function (field) {
                return '<button type="button" class="list-group-item list-group-item-action py-2 px-3 js-field-suggestion bg-white text-start small" data-field-id="' + field.id + '">' +
                    '<span class="fw-semibold">' + field.name + '</span>' +
                    '<span class="text-muted"> - ' + (field.element || '') + '</span>' +
                '</button>';
            }).join('');
            suggestions.classList.remove('d-none');
            suggestions.dataset.activeIndex = '0';
            setActiveSuggestion(row, 0);
        }

        function updateEmptyState() {
            if (!fieldsContainer) {
                return;
            }

            const hasRows = fieldsContainer.querySelector('[data-row]') !== null;
            const emptyState = fieldsContainer.querySelector('.js-field-empty-state');

            if (emptyState) {
                emptyState.classList.toggle('d-none', hasRows);
            }
        }

        document.querySelector('.js-add-field-row')?.addEventListener('click', function () {
            if (!fieldsContainer || !fieldTemplate) {
                return;
            }

            const nextIndex = Number(fieldsContainer.dataset.nextIndex || '0');
            const html = fieldTemplate.innerHTML.replaceAll('__INDEX__', nextIndex);

            fieldsContainer.insertAdjacentHTML('beforeend', html);
            fieldsContainer.dataset.nextIndex = String(nextIndex + 1);
            updateEmptyState();

            const rows = fieldsContainer.querySelectorAll('[data-row]');
            const lastRow = rows[rows.length - 1];

            if (lastRow) {
                setFieldRowMode(lastRow, 'new');
            }
        });

        fieldsContainer?.addEventListener('click', function (event) {
            const target = event.target;

            if (!(target instanceof HTMLElement)) {
                return;
            }

            const removeButton = target.closest('.js-remove-field-row');

            if (removeButton) {
                removeButton.closest('[data-row]')?.remove();
                updateEmptyState();
                return;
            }

            const suggestionButton = target.closest('.js-field-suggestion');

            if (suggestionButton instanceof HTMLElement) {
                const row = suggestionButton.closest('[data-row]');
                const fieldId = suggestionButton.dataset.fieldId;
                const matchedField = fieldId ? customFieldCatalog[fieldId] : null;

                if (row && matchedField) {
                    applyExistingFieldSelection(row, matchedField);
                }
            }
        });

        fieldsContainer?.addEventListener('input', function (event) {
            const target = event.target;

            if (!(target instanceof HTMLElement)) {
                return;
            }

            const row = target.closest('[data-row]');

            if (!row) {
                return;
            }

            if (target.classList.contains('js-field-name-input')) {
                syncRowFromName(row);
                renderSuggestions(row, target.value);
                return;
            }
        });

        fieldsContainer?.addEventListener('keydown', function (event) {
            const target = event.target;

            if (!(target instanceof HTMLInputElement) || !target.classList.contains('js-field-name-input')) {
                return;
            }

            const row = target.closest('[data-row]');
            const suggestions = row?.querySelector('.js-field-name-suggestions');

            if (!row || !suggestions || suggestions.classList.contains('d-none')) {
                return;
            }

            const buttons = getSuggestionButtons(row);
            const activeIndex = Number(suggestions.dataset.activeIndex ?? '-1');

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                setActiveSuggestion(row, activeIndex + 1);
                return;
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                setActiveSuggestion(row, activeIndex <= 0 ? 0 : activeIndex - 1);
                return;
            }

            if (event.key === 'Enter') {
                if (activeIndex >= 0 && buttons[activeIndex]) {
                    event.preventDefault();
                    const fieldId = buttons[activeIndex].dataset.fieldId;
                    const matchedField = fieldId ? customFieldCatalog[fieldId] : null;

                    if (matchedField) {
                        applyExistingFieldSelection(row, matchedField);
                    }
                }

                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closeSuggestions(row);
            }
        });

        fieldsContainer?.addEventListener('change', function (event) {
            const target = event.target;

            if (!(target instanceof HTMLElement)) {
                return;
            }

            const row = target.closest('[data-row]');

            if (!row) {
                return;
            }

            if (target.classList.contains('js-field-element')) {
                toggleFieldValues(row);
            }
        });

        fieldsContainer?.addEventListener('focusin', function (event) {
            const target = event.target;

            if (!(target instanceof HTMLInputElement) || !target.classList.contains('js-field-name-input')) {
                return;
            }

            renderSuggestions(target.closest('[data-row]'), target.value);
        });

        fieldsContainer?.addEventListener('focusout', function (event) {
            const target = event.target;

            if (!(target instanceof HTMLInputElement) || !target.classList.contains('js-field-name-input')) {
                return;
            }

            const row = target.closest('[data-row]');

            if (!row) {
                return;
            }

            window.setTimeout(function () {
                const activeElement = document.activeElement;

                if (activeElement instanceof HTMLElement && row.contains(activeElement)) {
                    return;
                }

                closeSuggestions(row);
            }, 100);
        });

        document.addEventListener('click', function (event) {
            if (!(event.target instanceof HTMLElement)) {
                return;
            }

            if (event.target.closest('[data-row]')) {
                return;
            }

            fieldsContainer?.querySelectorAll('[data-row]').forEach(closeSuggestions);
        });

        fieldsContainer?.querySelectorAll('[data-row]').forEach(function (row) {
            const source = row.querySelector('[data-source-input]')?.value === 'existing' ? 'existing' : 'new';

            if (source === 'existing') {
                const customFieldId = row.querySelector('[data-custom-field-id-input]')?.value;

                if (customFieldId) {
                    populateExistingField(row, customFieldId);
                }
            }

            setFieldRowMode(row, source);
        });

        updateEmptyState();
    });
</script>
