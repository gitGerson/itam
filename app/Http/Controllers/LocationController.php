<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationStoreRequest;
use App\Http\Requests\LocationUpdateRequest;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use App\Services\FilePondUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(protected FilePondUploadService $filePondUploads) {}

    public function index(): View
    {
        return view('locations.index');
    }

    public function create(): View
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $locations = Location::orderBy('name')->get(['id', 'name']);
        $managers = User::orderBy('name')->get(['id', 'name']);

        return view('locations.create', compact('companies', 'locations', 'managers'));
    }

    public function store(LocationStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['image'] = $this->filePondUploads->storeFromRequestField($request, 'image', 'locations', 's3');

        Location::create($validated);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan');
    }

    public function show(Location $location): View
    {
        $location->load(['company', 'parent', 'manager', 'children', 'creator', 'updater', 'deleter']);

        return view('locations.show', compact('location'));
    }

    public function edit(Location $location): View
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $locations = Location::where('id', '!=', $location->id)->orderBy('name')->get(['id', 'name']);
        $managers = User::orderBy('name')->get(['id', 'name']);

        return view('locations.edit', compact('location', 'companies', 'locations', 'managers'));
    }

    public function update(LocationUpdateRequest $request, Location $location): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedImagePath = $this->filePondUploads->storeFromRequestField($request, 'image', 'locations', 's3');

        if ($uploadedImagePath !== null) {
            if ($location->image) {
                Storage::disk('s3')->delete($location->image);
            }

            $validated['image'] = $uploadedImagePath;
        } else {
            $validated['image'] = $location->image;
        }

        $location->update($validated);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil diperbarui');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $locations = Location::query()
            ->with(['company:id,name', 'parent:id,name'])
            ->select(['id', 'name', 'company_id', 'parent_id', 'city', 'country', 'phone', 'image', 'created_at']);

        return datatables()->of($locations)
            ->addColumn('logo', function (Location $location): string {
                if (! $location->imageUrl()) {
                    return '<div class="d-flex align-items-center justify-content-center rounded border bg-label-secondary text-muted" style="width: 44px; height: 44px;">
                        <i class="bx bx-map-alt"></i>
                    </div>';
                }

                return '<img src="'.e($location->imageUrl()).'" alt="'.e($location->name).'" class="rounded border bg-white" style="width: 44px; height: 44px; object-fit: cover;">';
            })
            ->addColumn('company_name', fn (Location $location): string => e($location->company?->name ?? '-'))
            ->addColumn('parent_name', fn (Location $location): string => e($location->parent?->name ?? '-'))
            ->addColumn('action', function (Location $location): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('inventory.locations.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('locations.show', $location).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('inventory.locations.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('locations.edit', $location).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('inventory.locations.delete')) {
                    $actions .= '<form action="'.route('locations.destroy', $location).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus lokasi ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (Location $location): ?string => $location->created_at?->toIso8601String())
            ->rawColumns(['logo', 'action'])
            ->make(true);
    }
}
