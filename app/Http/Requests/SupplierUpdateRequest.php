<?php

namespace App\Http\Requests;

use App\Models\Supplier;
use Illuminate\Validation\Rule;

class SupplierUpdateRequest extends SupplierStoreRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.suppliers.edit');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        /** @var Supplier|null $supplier */
        $supplier = $this->route('supplier');

        return Supplier::validationRules($supplier);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var Supplier|null $supplier */
        $supplier = $this->route('supplier');

        return Supplier::validationMessages($supplier);
    }
}
