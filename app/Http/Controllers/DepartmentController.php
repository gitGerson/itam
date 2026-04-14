<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Models\Company;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
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
        Department::create($request->validated());

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
        $department->update($request->validated());

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
            ->with(['company:id,name', 'location:id,name', 'manager:id,name'])
            ->select(['id', 'name', 'company_id', 'location_id', 'manager_id', 'created_at']);

        return datatables()->of($departments)
            ->addColumn('company_name', fn (Department $d): string => e($d->company?->name ?? '-'))
            ->addColumn('location_name', fn (Department $d): string => e($d->location?->name ?? '-'))
            ->addColumn('manager_name', fn (Department $d): string => e($d->manager?->name ?? '-'))
            ->addColumn('action', function (Department $d): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('inventory.departments.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('departments.show', $d).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('inventory.departments.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('departments.edit', $d).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('inventory.departments.delete')) {
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
            ->rawColumns(['action'])
            ->make(true);
    }
}
