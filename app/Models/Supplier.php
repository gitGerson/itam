<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Services\FilePondUploadService;
use Closure;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Supplier extends Model
{
    use Auditable;

    /** @use HasFactory<SupplierFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'address2',
        'city',
        'state',
        'country',
        'phone',
        'fax',
        'email',
        'contact',
        'notes',
        'zip',
        'url',
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
    public static function validationRules(?self $supplier = null): array
    {
        $filePondUploads = app(FilePondUploadService::class);

        return [
            'name' => ['required', 'string', 'max:191', Rule::unique('suppliers', 'name')->ignore($supplier)],
            'address' => ['nullable', 'string', 'max:250'],
            'address2' => ['nullable', 'string', 'max:250'],
            'city' => ['nullable', 'string', 'max:191'],
            'state' => ['nullable', 'string', 'max:191'],
            'country' => ['nullable', 'string', 'size:2'],
            'phone' => ['nullable', 'string', 'max:35'],
            'fax' => ['nullable', 'string', 'max:35'],
            'email' => ['nullable', 'email', 'max:150'],
            'contact' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:191'],
            'zip' => ['nullable', 'string', 'max:10'],
            'url' => ['nullable', 'url', 'max:250'],
            'image' => [
                'nullable',
                function (string $attribute, mixed $value, Closure $fail) use ($supplier, $filePondUploads): void {
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

                    if (is_string($value) && $supplier !== null && $value === $supplier->image) {
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
    public static function validationMessages(?self $supplier = null): array
    {
        return [
            'name.required' => 'Nama supplier wajib diisi.',
            'name.unique' => 'Nama supplier sudah digunakan.',
            'country.size' => 'Kode negara harus terdiri dari 2 karakter.',
            'email.email' => 'Format email tidak valid.',
            'url.url' => 'Format website tidak valid.',
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
