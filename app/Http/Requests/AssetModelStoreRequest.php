<?php

namespace App\Http\Requests;

use App\Models\AssetModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetModelStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.models.create');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        return AssetModel::validationRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return AssetModel::validationMessages();
    }
}
