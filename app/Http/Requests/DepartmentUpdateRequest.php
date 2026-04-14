<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Validation\Rule;

class DepartmentUpdateRequest extends DepartmentStoreRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.departments.edit');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        /** @var Department|null $department */
        $department = $this->route('department');

        return Department::validationRules($department);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var Department|null $department */
        $department = $this->route('department');

        return Department::validationMessages($department);
    }
}
