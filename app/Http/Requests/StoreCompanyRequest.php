<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.companies.create');
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return Company::validationRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return Company::validationMessages();
    }

    protected function getRedirectUrl(): string
    {
        return route('companies.index', ['modal' => 'create']);
    }
}
