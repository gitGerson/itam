<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Services\FilePondUploadService;
use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        'parent_id',
        'manager_id',
        'address',
        'city',
        'state',
        'country',
        'zip',
        'phone',
        'image',
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
     * @return array<string, array<int, string|Rule|Closure>>
     */
    public static function validationRules(?self $location = null): array
    {
        $filePondUploads = app(FilePondUploadService::class);

        return [
            'name' => ['required', 'string', 'max:191'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:locations,id',
                function (string $attribute, mixed $value, Closure $fail) use ($location): void {
                    if ($value && $location && (int) $value === $location->id) {
                        $fail('Lokasi tidak bisa menjadi parent dari dirinya sendiri.');
                    }
                },
            ],
            'manager_id' => ['nullable', 'integer', 'exists:users,id'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'image' => [
                'nullable',
                function (string $attribute, mixed $value, Closure $fail) use ($location, $filePondUploads): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    if ($value instanceof UploadedFile) {
                        validator(
                            ['upload' => $value],
                            ['upload' => ['file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120']]
                        )->validate();

                        return;
                    }

                    if (is_string($value) && $location !== null && ($value === $location->image || $value === $location->imageUrl())) {
                        return;
                    }

                    ($filePondUploads->tempPathValidationRule())($attribute, $value, $fail);
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validationMessages(?self $location = null): array
    {
        return [
            'name.required' => 'Nama lokasi wajib diisi.',
            'company_id.exists' => 'Company tidak valid.',
            'parent_id.exists' => 'Lokasi induk tidak valid.',
            'manager_id.exists' => 'Manager tidak valid.',
        ];
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Storage::disk('s3')->url($this->image);
    }

    public function fullAddress(): string
    {
        return collect([$this->address, $this->city, $this->state, $this->zip, $this->country])
            ->filter()
            ->implode(', ');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
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
