<?php

namespace App\Http\Requests;

use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('inventory.suppliers.create');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        return Supplier::validationRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return Supplier::validationMessages();
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('country')) {
            $this->merge([
                'country' => strtoupper((string) $this->input('country')),
            ]);
        }
    }
}
