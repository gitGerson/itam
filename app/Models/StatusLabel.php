<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class StatusLabel extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'deployable',
        'pending',
        'archived',
        'notes',
        'color',
        'show_in_nav',
        'default_label',
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
            'deployable' => 'boolean',
            'pending' => 'boolean',
            'archived' => 'boolean',
            'show_in_nav' => 'boolean',
            'default_label' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public static function validationRules(?self $statusLabel = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('status_labels', 'name')->ignore($statusLabel)],
            'deployable' => ['sometimes', 'boolean'],
            'pending' => ['sometimes', 'boolean'],
            'archived' => ['sometimes', 'boolean'],
            'show_in_nav' => ['sometimes', 'boolean'],
            'default_label' => ['sometimes', 'boolean'],
            'color' => ['nullable', 'string', 'max:10', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validationMessages(?self $statusLabel = null): array
    {
        return [
            'name.required' => 'Nama status label wajib diisi.',
            'name.unique' => 'Nama status label sudah digunakan.',
            'color.regex' => 'Format warna harus berupa kode hex yang valid.',
        ];
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
