<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetModelStoreRequest;
use App\Http\Requests\AssetModelUpdateRequest;
use App\Models\AssetModel;
use App\Models\Category;
use App\Models\CustomFieldset;
use App\Models\Manufacturer;
use App\Services\FilePondUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AssetModelController extends Controller
{
    public function __construct(protected FilePondUploadService $filePondUploads) {}

    public function index(): View
    {
        return view('models.index');
    }

    public function create(): View
    {
        return view('models.create', [
            'manufacturers' => Manufacturer::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'fieldsets' => CustomFieldset::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(AssetModelStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['image'] = $this->filePondUploads->storeFromRequestField($request, 'image', 'models', 's3');

        AssetModel::create($validated);

        return redirect()
            ->route('models.index')
            ->with('success', 'Model berhasil ditambahkan');
    }

    public function show(AssetModel $assetModel): View
    {
        $assetModel->load(['manufacturer', 'category', 'fieldset', 'creator', 'updater', 'deleter']);

        return view('models.show', compact('assetModel'));
    }

    public function edit(AssetModel $assetModel): View
    {
        return view('models.edit', [
            'assetModel' => $assetModel,
            'manufacturers' => Manufacturer::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'fieldsets' => CustomFieldset::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(AssetModelUpdateRequest $request, AssetModel $assetModel): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedImagePath = $this->filePondUploads->storeFromRequestField($request, 'image', 'models', 's3');

        if ($uploadedImagePath !== null) {
            if ($assetModel->image) {
                Storage::disk('s3')->delete($assetModel->image);
            }

            $validated['image'] = $uploadedImagePath;
        } else {
            $validated['image'] = $assetModel->image;
        }

        $assetModel->update($validated);

        return redirect()
            ->route('models.index')
            ->with('success', 'Model berhasil diperbarui');
    }

    public function destroy(AssetModel $assetModel): RedirectResponse
    {
        $assetModel->delete();

        return redirect()
            ->route('models.index')
            ->with('success', 'Model berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $models = AssetModel::query()
            ->with(['manufacturer:id,name', 'category:id,name', 'fieldset:id,name', 'creator:id,name'])
            ->select([
                'id',
                'name',
                'model_number',
                'manufacturer_id',
                'category_id',
                'fieldset_id',
                'eol',
                'image',
                'created_by',
                'created_at',
                'updated_at',
            ]);

        return datatables()->of($models)
            ->addColumn('image_preview', function (AssetModel $assetModel): string {
                if (! $assetModel->imageUrl()) {
                    return '<div class="d-flex align-items-center justify-content-center rounded border bg-label-secondary text-muted" style="width: 44px; height: 44px;">
                        <i class="bx bx-image-alt"></i>
                    </div>';
                }

                return '<img src="'.e($assetModel->imageUrl()).'" alt="'.e($assetModel->name).'" class="rounded border bg-white" style="width: 44px; height: 44px; object-fit: cover;">';
            })
            ->addColumn('manufacturer_name', fn (AssetModel $assetModel): string => e($assetModel->manufacturer?->name ?? '-'))
            ->addColumn('category_name', fn (AssetModel $assetModel): string => e($assetModel->category?->name ?? '-'))
            ->addColumn('fieldset_name', fn (AssetModel $assetModel): string => e($assetModel->fieldset?->name ?? '-'))
            ->addColumn('creator_name', fn (AssetModel $assetModel): string => e($assetModel->creator?->name ?? '-'))
            ->addColumn('action', function (AssetModel $assetModel): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('settings.models.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('models.show', $assetModel).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.models.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('models.edit', $assetModel).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.models.delete')) {
                    $actions .= '<form action="'.route('models.destroy', $assetModel).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus model ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (AssetModel $assetModel): ?string => $assetModel->created_at?->toIso8601String())
            ->editColumn('updated_at', fn (AssetModel $assetModel): ?string => $assetModel->updated_at?->toIso8601String())
            ->rawColumns(['image_preview', 'action'])
            ->make(true);
    }
}
