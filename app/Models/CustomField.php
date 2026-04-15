<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class CustomField extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'element',
        'field_values',
        'help_text',
        'field_encrypted',
        'show_in_email',
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
            'field_encrypted' => 'boolean',
            'show_in_email' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function elementOptions(): array
    {
        return [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'select' => 'Select (Dropdown)',
            'radio' => 'Radio',
            'checkbox' => 'Checkbox',
            'date' => 'Date',
            'number' => 'Number',
        ];
    }

    /**
     * Elements that require field_values to be filled.
     *
     * @return list<string>
     */
    public static function elementsWithValues(): array
    {
        return ['select', 'radio', 'checkbox'];
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public static function validationRules(?self $customField = null): array
    {
        return [
            'name' => ['required', 'string', 'max:191', Rule::unique('custom_fields', 'name')->ignore($customField)->whereNull('deleted_at')],
            'element' => ['required', 'string', Rule::in(array_keys(self::elementOptions()))],
            'field_values' => ['nullable', 'string'],
            'help_text' => ['nullable', 'string'],
            'field_encrypted' => ['sometimes', 'boolean'],
            'show_in_email' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validationMessages(?self $customField = null): array
    {
        return [
            'name.required' => 'Nama field wajib diisi.',
            'name.unique' => 'Nama field sudah digunakan.',
            'element.required' => 'Tipe elemen wajib dipilih.',
            'element.in' => 'Tipe elemen tidak valid.',
        ];
    }

    public function hasValueList(): bool
    {
        return in_array($this->element, self::elementsWithValues(), true);
    }

    public function fieldValuesList(): array
    {
        if (! $this->field_values) {
            return [];
        }

        return array_filter(array_map('trim', explode("\n", $this->field_values)));
    }

    public function fieldsets(): BelongsToMany
    {
        return $this->belongsToMany(
            CustomFieldset::class,
            'custom_field_custom_fieldset',
            'custom_field_id',
            'custom_fieldset_id'
        )->withPivot('order');
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
