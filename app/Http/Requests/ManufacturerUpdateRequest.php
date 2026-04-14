<?php

namespace App\Http\Requests;

use App\Models\Manufacturer;
use Illuminate\Validation\Rule;

class ManufacturerUpdateRequest extends ManufacturerStoreRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.manufacturers.edit');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        /** @var Manufacturer|null $manufacturer */
        $manufacturer = $this->route('manufacturer');

        return Manufacturer::validationRules($manufacturer);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var Manufacturer|null $manufacturer */
        $manufacturer = $this->route('manufacturer');

        return Manufacturer::validationMessages($manufacturer);
    }
}
