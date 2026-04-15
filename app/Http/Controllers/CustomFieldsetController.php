<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomFieldsetStoreRequest;
use App\Http\Requests\CustomFieldsetUpdateRequest;
use App\Models\CustomField;
use App\Models\CustomFieldset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomFieldsetController extends Controller
{
    public function index(): View
    {
        return view('custom_fieldsets.index');
    }

    public function create(): View
    {
        $customFields = CustomField::orderBy('name')->get([
            'id',
            'name',
            'element',
            'format',
            'field_values',
            'help_text',
            'field_encrypted',
            'show_in_email',
        ]);

        return view('custom_fieldsets.create', compact('customFields'));
    }

    public function store(CustomFieldsetStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $fieldsetData = Arr::except($validated, ['fieldset_fields']);
        $fieldsetData['repeatable'] = $request->boolean('repeatable');

        DB::transaction(function () use ($fieldsetData, $validated): void {
            $customFieldset = CustomFieldset::create($fieldsetData);

            $this->syncCustomFields($customFieldset, $validated);
        });

        return redirect()
            ->route('custom-fieldsets.index')
            ->with('success', 'Custom fieldset berhasil ditambahkan');
    }

    public function show(CustomFieldset $customFieldset): View
    {
        $customFieldset->load(['customFields', 'creator', 'updater', 'deleter']);

        return view('custom_fieldsets.show', compact('customFieldset'));
    }

    public function edit(CustomFieldset $customFieldset): View
    {
        $customFields = CustomField::orderBy('name')->get([
            'id',
            'name',
            'element',
            'format',
            'field_values',
            'help_text',
            'field_encrypted',
            'show_in_email',
        ]);
        $customFieldset->load('customFields');

        return view('custom_fieldsets.edit', compact('customFieldset', 'customFields'));
    }

    public function update(CustomFieldsetUpdateRequest $request, CustomFieldset $customFieldset): RedirectResponse
    {
        $validated = $request->validated();
        $fieldsetData = Arr::except($validated, ['fieldset_fields']);
        $fieldsetData['repeatable'] = $request->boolean('repeatable');

        DB::transaction(function () use ($customFieldset, $fieldsetData, $validated): void {
            $customFieldset->update($fieldsetData);

            $this->syncCustomFields($customFieldset, $validated);
        });

        return redirect()
            ->route('custom-fieldsets.index')
            ->with('success', 'Custom fieldset berhasil diperbarui');
    }

    public function destroy(CustomFieldset $customFieldset): RedirectResponse
    {
        $customFieldset->delete();

        return redirect()
            ->route('custom-fieldsets.index')
            ->with('success', 'Custom fieldset berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $fieldsets = CustomFieldset::query()
            ->withCount('customFields')
            ->select(['id', 'name', 'notes', 'repeatable', 'created_at']);

        return datatables()->of($fieldsets)
            ->addColumn('repeatable_badge', fn (CustomFieldset $fs): string => $fs->repeatable
                ? '<span class="badge bg-label-success">Ya</span>'
                : '<span class="text-muted">-</span>')
            ->addColumn('field_count', fn (CustomFieldset $fs): string => '<span class="badge bg-label-primary">'.$fs->custom_fields_count.' field</span>')
            ->addColumn('action', function (CustomFieldset $fs): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('settings.custom_fieldsets.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('custom-fieldsets.show', $fs).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.custom_fieldsets.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('custom-fieldsets.edit', $fs).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.custom_fieldsets.delete')) {
                    $actions .= '<form action="'.route('custom-fieldsets.destroy', $fs).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus fieldset ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (CustomFieldset $fs): ?string => $fs->created_at?->toIso8601String())
            ->rawColumns(['repeatable_badge', 'field_count', 'action'])
            ->make(true);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function syncCustomFields(CustomFieldset $customFieldset, array $validated): void
    {
        $sync = collect($validated['fieldset_fields'] ?? [])
            ->map(function (array $field): int {
                if (($field['source'] ?? 'new') === 'existing') {
                    return (int) $field['custom_field_id'];
                }

                return CustomField::create([
                    'name' => $field['name'],
                    'element' => $field['element'],
                    'format' => $field['format'] ?? null,
                    'field_values' => $field['field_values'] ?? null,
                    'help_text' => $field['help_text'] ?? null,
                    'field_encrypted' => (bool) ($field['field_encrypted'] ?? false),
                    'show_in_email' => (bool) ($field['show_in_email'] ?? false),
                ])->id;
            })
            ->values()
            ->mapWithKeys(fn (int $id, int $index): array => [$id => ['order' => $index]])
            ->all();

        $customFieldset->customFields()->sync($sync);
    }
}
