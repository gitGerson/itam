<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryUpdateRequest extends CategoryStoreRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('inventory.categories.edit');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        /** @var Category|null $category */
        $category = $this->route('category');

        return Category::validationRules($category);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var Category|null $category */
        $category = $this->route('category');

        return Category::validationMessages($category);
    }

    protected function getRedirectUrl(): string
    {
        /** @var Category|null $category */
        $category = $this->route('category');

        return route('categories.index', [
            'modal' => 'edit',
            'category' => $category?->getKey(),
        ]);
    }
}
