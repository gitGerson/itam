<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Models\Supplier;
use App\Services\FilePondUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(protected FilePondUploadService $filePondUploads) {}

    public function index(): View
    {
        return view('suppliers.index');
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(SupplierStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['image'] = $this->filePondUploads->storeFromRequestField($request, 'image', 'suppliers', 's3');

        Supplier::create($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['creator', 'updater', 'deleter']);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedImagePath = $this->filePondUploads->storeFromRequestField($request, 'image', 'suppliers', 's3');

        if ($uploadedImagePath !== null) {
            if ($supplier->image) {
                Storage::disk('s3')->delete($supplier->image);
            }

            $validated['image'] = $uploadedImagePath;
        } else {
            $validated['image'] = $supplier->image;
        }

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $suppliers = Supplier::query()
            ->select(['id', 'name', 'contact', 'email', 'phone', 'country', 'image', 'created_at']);

        return datatables()->of($suppliers)
            ->addColumn('logo', function (Supplier $supplier): string {
                if (! $supplier->imageUrl()) {
                    return '<div class="d-flex align-items-center justify-content-center rounded border bg-label-secondary text-muted" style="width: 44px; height: 44px;">
                        <i class="bx bx-image-alt"></i>
                    </div>';
                }

                return '<img src="'.e($supplier->imageUrl()).'" alt="'.e($supplier->name).'" class="rounded border bg-white" style="width: 44px; height: 44px; object-fit: cover;">';
            })
            ->addColumn('action', function (Supplier $supplier): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('settings.suppliers.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('suppliers.show', $supplier).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.suppliers.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('suppliers.edit', $supplier).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.suppliers.delete')) {
                    $actions .= '<form action="'.route('suppliers.destroy', $supplier).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus supplier ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (Supplier $supplier): ?string => $supplier->created_at?->toIso8601String())
            ->rawColumns(['logo', 'action'])
            ->make(true);
    }
}
