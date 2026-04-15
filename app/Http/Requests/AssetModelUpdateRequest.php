<?php

namespace App\Http\Requests;

use App\Models\AssetModel;
use Illuminate\Validation\Rule;

class AssetModelUpdateRequest extends AssetModelStoreRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.models.edit');
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public function rules(): array
    {
        /** @var AssetModel|null $assetModel */
        $assetModel = $this->route('asset_model');

        return AssetModel::validationRules($assetModel);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var AssetModel|null $assetModel */
        $assetModel = $this->route('asset_model');

        return AssetModel::validationMessages($assetModel);
    }
}
