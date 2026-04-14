<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationStoreRequest;
use App\Http\Requests\LocationUpdateRequest;
use App\Models\Company;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        return view('locations.index');
    }

    public function create(): View
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('locations.create', compact('companies'));
    }

    public function store(LocationStoreRequest $request): RedirectResponse
    {
        Location::create($request->validated());

        return redirect()
            ->route('locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan');
    }

    public function show(Location $location): View
    {
        $location->load(['company', 'creator', 'updater', 'deleter']);

        return view('locations.show', compact('location'));
    }

    public function edit(Location $location): View
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('locations.edit', compact('location', 'companies'));
    }

    public function update(LocationUpdateRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

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
            ->with(['company:id,name', 'creator:id,name'])
            ->select(['id', 'name', 'company_id', 'notes', 'created_by', 'created_at']);

        return datatables()->of($locations)
            ->addColumn('company_name', fn (Location $location): string => e($location->company?->name ?? '-'))
            ->addColumn('creator_name', fn (Location $location): string => e($location->creator?->name ?? '-'))
            ->addColumn('action', function (Location $location): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('settings.locations.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('locations.show', $location).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.locations.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('locations.edit', $location).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.locations.delete')) {
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
            ->rawColumns(['action'])
            ->make(true);
    }
}
