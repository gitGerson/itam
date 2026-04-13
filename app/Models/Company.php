<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Services\FilePondUploadService;
use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Company extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'fax',
        'email',
        'phone',
        'image',
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
     * @return array<string, array<int, string|Rule>>
     */
    public static function validationRules(?self $company = null): array
    {
        $filePondUploads = app(FilePondUploadService::class);

        return [
            'name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('companies', 'name')->ignore($company),
            ],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'fax' => ['nullable', 'string', 'max:20'],
            'image' => [
                'nullable',
                function (string $attribute, mixed $value, Closure $fail) use ($company, $filePondUploads): void {
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

                    if (is_string($value) && $company !== null && $value === $company->image) {
                        return;
                    }

                    ($filePondUploads->tempPathValidationRule())($attribute, $value, $fail);
                },
            ],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validationMessages(?self $company = null): array
    {
        return [
            'name.required' => 'Nama company wajib diisi.',
            'name.unique' => 'Nama company sudah digunakan.',
            'email.email' => 'Format email tidak valid.',
        ];
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Storage::disk('s3')->url($this->image);
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
