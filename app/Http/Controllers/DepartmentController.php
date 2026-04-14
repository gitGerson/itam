<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Models\Company;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use App\Services\FilePondUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(protected FilePondUploadService $filePondUploads) {}

    public function index(): View
    {
        return view('departments.index');
    }

    public function create(): View
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $locations = Location::orderBy('name')->get(['id', 'name']);
        $managers = User::orderBy('name')->get(['id', 'name']);

        return view('departments.create', compact('companies', 'locations', 'managers'));
    }

    public function store(DepartmentStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['image'] = $this->filePondUploads->storeFromRequestField($request, 'image', 'departments', 's3');

        Department::create($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Departemen berhasil ditambahkan');
    }

    public function show(Department $department): View
    {
        $department->load(['company', 'location', 'manager', 'creator', 'updater', 'deleter']);

        return view('departments.show', compact('department'));
    }

    public function edit(Department $department): View
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $locations = Location::orderBy('name')->get(['id', 'name']);
        $managers = User::orderBy('name')->get(['id', 'name']);

        return view('departments.edit', compact('department', 'companies', 'locations', 'managers'));
    }

    public function update(DepartmentUpdateRequest $request, Department $department): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedImagePath = $this->filePondUploads->storeFromRequestField($request, 'image', 'departments', 's3');

        if ($uploadedImagePath !== null) {
            if ($department->image) {
                Storage::disk('s3')->delete($department->image);
            }

            $validated['image'] = $uploadedImagePath;
        } else {
            $validated['image'] = $department->image;
        }

        $department->update($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Departemen berhasil diperbarui');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'Departemen berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $departments = Department::query()
            ->with(['company:id,name', 'location:id,name', 'manager:id,name', 'creator:id,name'])
            ->select(['id', 'name', 'company_id', 'location_id', 'manager_id', 'notes', 'image', 'created_by', 'created_at']);

        return datatables()->of($departments)
            ->addColumn('company_name', fn (Department $d): string => e($d->company?->name ?? '-'))
            ->addColumn('location_name', fn (Department $d): string => e($d->location?->name ?? '-'))
            ->addColumn('manager_name', fn (Department $d): string => e($d->manager?->name ?? '-'))
            ->addColumn('creator_name', fn (Department $d): string => e($d->creator?->name ?? '-'))
            ->addColumn('image_preview', function (Department $d): string {
                if (! $d->imageUrl()) {
                    return '<div class="d-flex align-items-center justify-content-center rounded border bg-label-secondary text-muted" style="width: 44px; height: 44px;">
                        <i class="bx bx-image-alt"></i>
                    </div>';
                }

                return '<img src="'.e($d->imageUrl()).'" alt="'.e($d->name).'" class="rounded border bg-white" style="width: 44px; height: 44px; object-fit: cover;">';
            })
            ->addColumn('action', function (Department $d): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('settings.departments.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('departments.show', $d).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.departments.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('departments.edit', $d).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.departments.delete')) {
                    $actions .= '<form action="'.route('departments.destroy', $d).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus departemen ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (Department $d): ?string => $d->created_at?->toIso8601String())
            ->rawColumns(['image_preview', 'action'])
            ->make(true);
    }
}
