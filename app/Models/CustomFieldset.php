<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class CustomFieldset extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'notes',
        'repeatable',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'repeatable' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public static function validationRules(?self $customFieldset = null): array
    {
        return [
            'name' => ['required', 'string', 'max:191', Rule::unique('custom_fieldsets', 'name')->ignore($customFieldset)->whereNull('deleted_at')],
            'notes' => ['nullable', 'string'],
            'repeatable' => ['sometimes', 'boolean'],
            'fieldset_fields' => ['nullable', 'array'],
            'fieldset_fields.*' => ['array:source,custom_field_id,name,element,format,field_values,help_text,field_encrypted,show_in_email'],
            'fieldset_fields.*.source' => ['required', 'string', Rule::in(['existing', 'new'])],
            'fieldset_fields.*.custom_field_id' => ['nullable', 'integer', 'exists:custom_fields,id'],
            'fieldset_fields.*.name' => ['nullable', 'string', 'max:191', 'distinct', Rule::unique('custom_fields', 'name')->whereNull('deleted_at')],
            'fieldset_fields.*.element' => ['nullable', 'string', Rule::in(array_keys(CustomField::elementOptions()))],
            'fieldset_fields.*.format' => ['nullable', 'string', 'max:255'],
            'fieldset_fields.*.field_values' => ['nullable', 'string'],
            'fieldset_fields.*.help_text' => ['nullable', 'string'],
            'fieldset_fields.*.field_encrypted' => ['sometimes', 'boolean'],
            'fieldset_fields.*.show_in_email' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validationMessages(?self $customFieldset = null): array
    {
        return [
            'name.required' => 'Nama fieldset wajib diisi.',
            'name.unique' => 'Nama fieldset sudah digunakan.',
            'fieldset_fields.*.custom_field_id.exists' => 'Salah satu custom field tidak valid.',
            'fieldset_fields.*.name.unique' => 'Nama custom field baru sudah digunakan.',
            'fieldset_fields.*.name.distinct' => 'Nama custom field baru tidak boleh duplikat.',
            'fieldset_fields.*.element.in' => 'Tipe elemen custom field tidak valid.',
        ];
    }

    public function customFields(): BelongsToMany
    {
        return $this->belongsToMany(
            CustomField::class,
            'custom_field_custom_fieldset',
            'custom_fieldset_id',
            'custom_field_id'
        )->withPivot('order')->orderByPivot('order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
