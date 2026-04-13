<?php

namespace App\Http\Requests;

use App\Models\CustomField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomFieldStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('inventory.custom_fields.create');
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return CustomField::validationRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return CustomField::validationMessages();
    }
}
