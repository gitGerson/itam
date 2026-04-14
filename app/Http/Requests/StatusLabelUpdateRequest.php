<?php

namespace App\Http\Requests;

use App\Models\StatusLabel;
use Illuminate\Validation\Rule;

class StatusLabelUpdateRequest extends StatusLabelStoreRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('settings.status_labels.edit');
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        /** @var StatusLabel|null $statusLabel */
        $statusLabel = $this->route('statusLabel');

        return StatusLabel::validationRules($statusLabel);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        /** @var StatusLabel|null $statusLabel */
        $statusLabel = $this->route('statusLabel');

        return StatusLabel::validationMessages($statusLabel);
    }
}
