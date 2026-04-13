<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManufacturerStoreRequest;
use App\Http\Requests\ManufacturerUpdateRequest;
use App\Models\Manufacturer;
use App\Services\FilePondUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ManufacturerController extends Controller
{
    public function __construct(protected FilePondUploadService $filePondUploads) {}

    public function index(): View
    {
        return view('manufacturers.index');
    }

    public function create(): View
    {
        return view('manufacturers.create');
    }

    public function store(ManufacturerStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['checkin_email'] = true;
        $validated['image'] = $this->filePondUploads->storeFromRequestField($request, 'image', 'manufacturers', 's3');

        Manufacturer::create($validated);

        return redirect()
            ->route('manufacturers.index')
            ->with('success', 'Manufacturer berhasil ditambahkan');
    }

    public function show(Manufacturer $manufacturer): View
    {
        $manufacturer->load(['creator', 'updater', 'deleter']);

        return view('manufacturers.show', compact('manufacturer'));
    }

    public function edit(Manufacturer $manufacturer): View
    {
        return view('manufacturers.edit', compact('manufacturer'));
    }

    public function update(ManufacturerUpdateRequest $request, Manufacturer $manufacturer): RedirectResponse
    {
        $validated = $request->validated();
        $validated['checkin_email'] = true;
        $uploadedImagePath = $this->filePondUploads->storeFromRequestField($request, 'image', 'manufacturers', 's3');

        if ($uploadedImagePath !== null) {
            if ($manufacturer->image) {
                Storage::disk('s3')->delete($manufacturer->image);
            }

            $validated['image'] = $uploadedImagePath;
        } else {
            $validated['image'] = $manufacturer->image;
        }

        $manufacturer->update($validated);

        return redirect()
            ->route('manufacturers.index')
            ->with('success', 'Manufacturer berhasil diperbarui');
    }

    public function destroy(Manufacturer $manufacturer): RedirectResponse
    {
        $manufacturer->delete();

        return redirect()
            ->route('manufacturers.index')
            ->with('success', 'Manufacturer berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $manufacturers = Manufacturer::query()
            ->select(['id', 'name', 'url', 'support_phone', 'support_email', 'image', 'created_at']);

        return datatables()->of($manufacturers)
            ->addColumn('logo', function (Manufacturer $manufacturer): string {
                if (! $manufacturer->imageUrl()) {
                    return '<div class="d-flex align-items-center justify-content-center rounded border bg-label-secondary text-muted" style="width: 44px; height: 44px;">
                        <i class="bx bx-image-alt"></i>
                    </div>';
                }

                return '<img src="'.e($manufacturer->imageUrl()).'" alt="'.e($manufacturer->name).'" class="rounded border bg-white" style="width: 44px; height: 44px; object-fit: cover;">';
            })
            ->addColumn('action', function (Manufacturer $manufacturer): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('inventory.manufacturers.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('manufacturers.show', $manufacturer).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('inventory.manufacturers.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('manufacturers.edit', $manufacturer).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('inventory.manufacturers.delete')) {
                    $actions .= '<form action="'.route('manufacturers.destroy', $manufacturer).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus manufacturer ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (Manufacturer $manufacturer): ?string => $manufacturer->created_at?->toIso8601String())
            ->rawColumns(['logo', 'action'])
            ->make(true);
    }
}
