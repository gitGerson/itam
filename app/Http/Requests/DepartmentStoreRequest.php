<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.departments.create');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        return Department::validationRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return Department::validationMessages();
    }
}
