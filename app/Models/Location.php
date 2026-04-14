<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Location extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'company_id',
        'notes',
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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, array<int, string|Rule|\Closure>>
     */
    public static function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validationMessages(): array
    {
        return [
            'name.required' => 'Nama lokasi wajib diisi.',
            'company_id.exists' => 'Company tidak valid.',
            'parent_id.exists' => 'Lokasi induk tidak valid.',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
