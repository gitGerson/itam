<?php

namespace App\Http\Requests;

use App\Models\CustomFieldset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CustomFieldsetStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.custom_fieldsets.create');
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return CustomFieldset::validationRules();
    }

    protected function prepareForValidation(): void
    {
        $fieldsetFields = collect($this->input('fieldset_fields', []))
            ->filter(function (mixed $field): bool {
                if (! is_array($field)) {
                    return false;
                }

                if (($field['source'] ?? 'new') === 'existing') {
                    return filled($field['custom_field_id'] ?? null);
                }

                return collect([
                    $field['name'] ?? null,
                    $field['element'] ?? null,
                    $field['format'] ?? null,
                    $field['field_values'] ?? null,
                    $field['help_text'] ?? null,
                ])->contains(fn (mixed $value): bool => filled($value));
            })
            ->map(function (array $field, int|string $index): array {
                $source = ($field['source'] ?? 'new') === 'existing' ? 'existing' : 'new';

                if ($source === 'existing') {
                    return [
                        'source' => 'existing',
                        'custom_field_id' => (int) ($field['custom_field_id'] ?? 0),
                    ];
                }

                return [
                    'source' => 'new',
                    'name' => trim((string) ($field['name'] ?? '')),
                    'element' => $field['element'] ?? null,
                    'format' => filled($field['format'] ?? null) ? trim((string) $field['format']) : null,
                    'field_values' => filled($field['field_values'] ?? null) ? trim((string) $field['field_values']) : null,
                    'help_text' => filled($field['help_text'] ?? null) ? trim((string) $field['help_text']) : null,
                    'field_encrypted' => $this->boolean("fieldset_fields.$index.field_encrypted"),
                    'show_in_email' => $this->boolean("fieldset_fields.$index.show_in_email"),
                ];
            })
            ->values()
            ->all();

        $this->merge([
            'fieldset_fields' => $fieldsetFields,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return CustomFieldset::validationMessages();
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $fieldsetFields = collect($this->input('fieldset_fields', []));

                if ($fieldsetFields->isEmpty()) {
                    return;
                }

                $hasNewField = $fieldsetFields->contains(fn (array $field): bool => ($field['source'] ?? 'new') === 'new');

                if ($hasNewField && ! auth()->user()?->hasPermission('settings.custom_fields.create')) {
                    $validator->errors()->add('fieldset_fields', 'Anda tidak memiliki izin untuk membuat custom field baru.');
                }

                foreach ($fieldsetFields as $index => $field) {
                    $source = $field['source'] ?? 'new';

                    if ($source === 'existing' && empty($field['custom_field_id'])) {
                        $validator->errors()->add("fieldset_fields.$index.custom_field_id", 'Custom field wajib dipilih.');
                    }

                    if ($source === 'new') {
                        if (! filled($field['name'] ?? null)) {
                            $validator->errors()->add("fieldset_fields.$index.name", 'Nama custom field baru wajib diisi.');
                        }

                        if (! filled($field['element'] ?? null)) {
                            $validator->errors()->add("fieldset_fields.$index.element", 'Tipe elemen custom field baru wajib dipilih.');
                        }
                    }
                }
            },
        ];
    }
}
