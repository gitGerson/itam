<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('inventory.categories.create');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        return Category::validationRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return Category::validationMessages();
    }

    protected function getRedirectUrl(): string
    {
        return route('categories.index', ['modal' => 'create']);
    }
}
