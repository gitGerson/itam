<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends StoreCompanyRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('inventory.companies.edit');
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        /** @var Company|null $company */
        $company = $this->route('company');

        return Company::validationRules($company);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var Company|null $company */
        $company = $this->route('company');

        return Company::validationMessages($company);
    }

    protected function getRedirectUrl(): string
    {
        /** @var Company|null $company */
        $company = $this->route('company');

        return route('companies.index', [
            'modal' => 'edit',
            'company' => $company?->getKey(),
        ]);
    }
}
