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
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*' => ['integer', 'exists:custom_fields,id'],
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
            'custom_fields.*.exists' => 'Salah satu custom field tidak valid.',
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
