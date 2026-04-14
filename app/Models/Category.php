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

class Category extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category_type',
        'checkin_email',
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
            'checkin_email' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function categoryTypeOptions(): array
    {
        return [
            'asset' => 'Asset',
            'accessory' => 'Accessory',
            'component' => 'Component',
            'consumable' => 'Consumable',
        ];
    }

    /**
     * @return array<string, array<int, string|Rule|Closure>>
     */
    public static function validationRules(?self $category = null): array
    {
        $filePondUploads = app(FilePondUploadService::class);

        return [
            'name' => ['required', 'string', 'max:191'],
            'category_type' => ['required', 'string', Rule::in(array_keys(self::categoryTypeOptions()))],
            'image' => [
                'nullable',
                function (string $attribute, mixed $value, Closure $fail) use ($category, $filePondUploads): void {
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

                    if (is_string($value) && $category !== null && ($value === $category->image || $value === $category->imageUrl())) {
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
    public static function validationMessages(?self $category = null): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'category_type.required' => 'Tipe kategori wajib dipilih.',
            'category_type.in' => 'Tipe kategori tidak valid.',
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
