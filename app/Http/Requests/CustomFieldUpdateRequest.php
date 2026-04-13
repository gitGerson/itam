<?php

namespace App\Http\Requests;

use App\Models\CustomField;
use Illuminate\Validation\Rule;

class CustomFieldUpdateRequest extends CustomFieldStoreRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('inventory.custom_fields.edit');
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        /** @var CustomField|null $customField */
        $customField = $this->route('custom_field');

        return CustomField::validationRules($customField);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var CustomField|null $customField */
        $customField = $this->route('custom_field');

        return CustomField::validationMessages($customField);
    }
}
