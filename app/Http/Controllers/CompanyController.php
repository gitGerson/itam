<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\FilePondUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(protected FilePondUploadService $filePondUploads) {}

    public function index(Request $request): View
    {
        $modalState = $request->query('modal');
        $editingCompany = null;

        if ($modalState === 'edit' && $request->filled('company')) {
            $editingCompany = Company::find($request->integer('company'));
        }

        return view('companies.index', compact('modalState', 'editingCompany'));
    }

    public function create(): View
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['image'] = $this->filePondUploads->storeFromRequestField($request, 'image', 'companies', 's3');

        Company::create($validated);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company berhasil ditambahkan');
    }

    public function show(Company $company): View
    {
        $company->load(['creator', 'updater', 'deleter']);

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        return view('companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedImagePath = $this->filePondUploads->storeFromRequestField($request, 'image', 'companies', 's3');

        if ($uploadedImagePath !== null) {
            if ($company->image) {
                Storage::disk('s3')->delete($company->image);
            }

            $validated['image'] = $uploadedImagePath;
        } else {
            $validated['image'] = $company->image;
        }

        $company->update($validated);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company berhasil diperbarui');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $companies = Company::query()
            ->select(['id', 'name', 'email', 'phone', 'fax', 'image', 'created_at']);

        return datatables()->of($companies)
            ->addColumn('logo', function (Company $company): string {
                if (! $company->imageUrl()) {
                    return '<div class="d-flex align-items-center justify-content-center rounded border bg-label-secondary text-muted" style="width: 44px; height: 44px;">
                        <i class="bx bx-image-alt"></i>
                    </div>';
                }

                return '<img src="'.e($company->imageUrl()).'" alt="'.e($company->name).'" class="rounded border bg-white" style="width: 44px; height: 44px; object-fit: cover;">';
            })
            ->addColumn('action', function (Company $company): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('inventory.companies.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('companies.show', $company).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('inventory.companies.edit')) {
                    $companyPayload = e(json_encode([
                        'id' => $company->id,
                        'name' => $company->name,
                        'email' => $company->email,
                        'phone' => $company->phone,
                        'fax' => $company->fax,
                        'notes' => $company->notes,
                        'image_url' => $company->imageUrl(),
                        'image_path' => $company->image,
                        'update_url' => route('companies.update', $company),
                    ]));

                    $actions .= '<button type="button" class="dropdown-item js-edit-company" data-company="'.$companyPayload.'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </button>';
                }

                if (auth()->user()->hasPermission('inventory.companies.delete')) {
                    $actions .= '<form action="'.route('companies.destroy', $company).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus company ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (Company $company): ?string => $company->created_at?->toIso8601String())
            ->rawColumns(['logo', 'action'])
            ->make(true);
    }
}
