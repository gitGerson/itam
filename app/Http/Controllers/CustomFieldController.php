<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomFieldStoreRequest;
use App\Http\Requests\CustomFieldUpdateRequest;
use App\Models\CustomField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomFieldController extends Controller
{
    public function index(): View
    {
        return view('custom_fields.index');
    }

    public function create(): View
    {
        return view('custom_fields.create');
    }

    public function store(CustomFieldStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['field_encrypted'] = $request->boolean('field_encrypted');
        $validated['show_in_email'] = $request->boolean('show_in_email');

        CustomField::create($validated);

        return redirect()
            ->route('custom-fields.index')
            ->with('success', 'Custom field berhasil ditambahkan');
    }

    public function show(CustomField $customField): View
    {
        $customField->load(['creator', 'updater', 'deleter']);

        return view('custom_fields.show', compact('customField'));
    }

    public function edit(CustomField $customField): View
    {
        return view('custom_fields.edit', compact('customField'));
    }

    public function update(CustomFieldUpdateRequest $request, CustomField $customField): RedirectResponse
    {
        $validated = $request->validated();
        $validated['field_encrypted'] = $request->boolean('field_encrypted');
        $validated['show_in_email'] = $request->boolean('show_in_email');

        $customField->update($validated);

        return redirect()
            ->route('custom-fields.index')
            ->with('success', 'Custom field berhasil diperbarui');
    }

    public function destroy(CustomField $customField): RedirectResponse
    {
        $customField->delete();

        return redirect()
            ->route('custom-fields.index')
            ->with('success', 'Custom field berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $customFields = CustomField::query()
            ->select([
                'id',
                'name',
                'element',
                'field_encrypted',
                'show_in_email',
                'created_at',
            ]);

        return datatables()->of($customFields)
            ->editColumn('element', fn (CustomField $cf): string => e(CustomField::elementOptions()[$cf->element] ?? Str::headline($cf->element)))
            ->addColumn('badges', function (CustomField $cf): string {
                $badges = [];

                if ($cf->field_encrypted) {
                    $badges[] = '<span class="badge bg-label-warning">Encrypted</span>';
                }

                if ($cf->show_in_email) {
                    $badges[] = '<span class="badge bg-label-info">In Email</span>';
                }

                return $badges !== [] ? implode(' ', $badges) : '<span class="text-muted">-</span>';
            })
            ->addColumn('action', function (CustomField $cf): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('settings.custom_fields.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('custom-fields.show', $cf).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.custom_fields.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('custom-fields.edit', $cf).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.custom_fields.delete')) {
                    $actions .= '<form action="'.route('custom-fields.destroy', $cf).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus custom field ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (CustomField $cf): ?string => $cf->created_at?->toIso8601String())
            ->rawColumns(['badges', 'action'])
            ->make(true);
    }
}
