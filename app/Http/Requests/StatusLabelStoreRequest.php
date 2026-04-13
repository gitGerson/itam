<?php

namespace App\Http\Requests;

use App\Models\StatusLabel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatusLabelStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('inventory.status_labels.create');
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return StatusLabel::validationRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return StatusLabel::validationMessages();
    }
}
