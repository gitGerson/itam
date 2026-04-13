<?php

namespace App\Http\Requests;

use App\Models\CustomFieldset;
use Illuminate\Validation\Rule;

class CustomFieldsetUpdateRequest extends CustomFieldsetStoreRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('inventory.custom_fieldsets.edit');
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        /** @var CustomFieldset|null $customFieldset */
        $customFieldset = $this->route('custom_fieldset');

        return CustomFieldset::validationRules($customFieldset);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var CustomFieldset|null $customFieldset */
        $customFieldset = $this->route('custom_fieldset');

        return CustomFieldset::validationMessages($customFieldset);
    }
}
