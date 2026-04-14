<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Validation\Rule;

class LocationUpdateRequest extends LocationStoreRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.locations.edit');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        /** @var Location|null $location */
        $location = $this->route('location');

        return Location::validationRules($location);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var Location|null $location */
        $location = $this->route('location');

        return Location::validationMessages($location);
    }
}
