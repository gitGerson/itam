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

class Manufacturer extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'url',
        'support_url',
        'warranty_lookup_url',
        'support_phone',
        'support_email',
        'image',
        'notes',
        'checkin_email',
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
            'checkin_email' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, array<int, string|Rule|Closure>>
     */
    public static function validationRules(?self $manufacturer = null): array
    {
        $filePondUploads = app(FilePondUploadService::class);

        return [
            'name' => ['required', 'string', 'max:191', Rule::unique('manufacturers', 'name')->ignore($manufacturer)],
            'url' => ['nullable', 'url', 'max:191'],
            'support_url' => ['nullable', 'url', 'max:191'],
            'warranty_lookup_url' => ['nullable', 'url', 'max:191'],
            'support_phone' => ['nullable', 'string', 'max:191'],
            'support_email' => ['nullable', 'email', 'max:191'],
            'image' => [
                'nullable',
                function (string $attribute, mixed $value, Closure $fail) use ($manufacturer, $filePondUploads): void {
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

                    if (is_string($value) && $manufacturer !== null && $value === $manufacturer->image) {
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
    public static function validationMessages(?self $manufacturer = null): array
    {
        return [
            'name.required' => 'Nama manufacturer wajib diisi.',
            'name.unique' => 'Nama manufacturer sudah digunakan.',
            'url.url' => 'Format website tidak valid.',
            'support_url.url' => 'Format support URL tidak valid.',
            'warranty_lookup_url.url' => 'Format warranty lookup URL tidak valid.',
            'support_email.email' => 'Format support email tidak valid.',
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
