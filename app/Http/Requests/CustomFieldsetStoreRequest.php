<?php

namespace App\Http\Requests;

use App\Models\CustomFieldset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return CustomFieldset::validationMessages();
    }
}
