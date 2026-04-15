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

class AssetModel extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'models';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'model_number',
        'manufacturer_id',
        'category_id',
        'fieldset_id',
        'eol',
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
            'manufacturer_id' => 'integer',
            'category_id' => 'integer',
            'fieldset_id' => 'integer',
            'eol' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, array<int, string|Rule|Closure>>
     */
    public static function validationRules(?self $assetModel = null): array
    {
        $filePondUploads = app(FilePondUploadService::class);

        return [
            'name' => ['required', 'string', 'max:191', Rule::unique('models', 'name')->ignore($assetModel)],
            'model_number' => ['nullable', 'string', 'max:191'],
            'manufacturer_id' => ['nullable', 'integer', 'exists:manufacturers,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'fieldset_id' => ['nullable', 'integer', 'exists:custom_fieldsets,id'],
            'eol' => ['nullable', 'integer', 'min:0'],
            'image' => [
                'nullable',
                function (string $attribute, mixed $value, Closure $fail) use ($assetModel, $filePondUploads): void {
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

                    if (is_string($value) && $assetModel !== null && ($value === $assetModel->image || $value === $assetModel->imageUrl())) {
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
    public static function validationMessages(?self $assetModel = null): array
    {
        return [
            'name.required' => 'Nama model wajib diisi.',
            'name.unique' => 'Nama model sudah digunakan.',
            'manufacturer_id.exists' => 'Manufacturer tidak valid.',
            'category_id.exists' => 'Kategori tidak valid.',
            'fieldset_id.exists' => 'Fieldset tidak valid.',
            'eol.integer' => 'EOL harus berupa angka.',
            'eol.min' => 'EOL tidak boleh kurang dari 0.',
        ];
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Storage::disk('s3')->url($this->image);
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function fieldset(): BelongsTo
    {
        return $this->belongsTo(CustomFieldset::class, 'fieldset_id');
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
