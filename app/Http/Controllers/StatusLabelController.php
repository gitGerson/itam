<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusLabelStoreRequest;
use App\Http\Requests\StatusLabelUpdateRequest;
use App\Models\StatusLabel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusLabelController extends Controller
{
    public function index(): View
    {
        return view('status_labels.index');
    }

    public function create(): View
    {
        return view('status_labels.create');
    }

    public function store(StatusLabelStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        StatusLabel::create($this->normalizeFlags($request, $validated));

        return redirect()
            ->route('status-labels.index')
            ->with('success', 'Status label berhasil ditambahkan');
    }

    public function show(StatusLabel $statusLabel): View
    {
        $statusLabel->load(['creator', 'updater', 'deleter']);

        return view('status_labels.show', compact('statusLabel'));
    }

    public function edit(StatusLabel $statusLabel): View
    {
        return view('status_labels.edit', compact('statusLabel'));
    }

    public function update(StatusLabelUpdateRequest $request, StatusLabel $statusLabel): RedirectResponse
    {
        $validated = $request->validated();

        $statusLabel->update($this->normalizeFlags($request, $validated));

        return redirect()
            ->route('status-labels.index')
            ->with('success', 'Status label berhasil diperbarui');
    }

    public function destroy(StatusLabel $statusLabel): RedirectResponse
    {
        $statusLabel->delete();

        return redirect()
            ->route('status-labels.index')
            ->with('success', 'Status label berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $statusLabels = StatusLabel::query()
            ->with('creator:id,name')
            ->select([
                'id',
                'name',
                'color',
                'deployable',
                'pending',
                'archived',
                'show_in_nav',
                'default_label',
                'notes',
                'created_by',
                'created_at',
                'updated_at',
            ]);

        return datatables()->of($statusLabels)
            ->addColumn('color_preview', function (StatusLabel $statusLabel): string {
                if (! $statusLabel->color) {
                    return '<span class="text-muted">-</span>';
                }

                return '<div class="d-flex align-items-center gap-2">
                    <span class="rounded border" style="display:inline-block;width:20px;height:20px;background-color:'.e($statusLabel->color).';"></span>
                    <span>'.e($statusLabel->color).'</span>
                </div>';
            })
            ->addColumn('status_type', function (StatusLabel $statusLabel): string {
                $types = [];

                if ($statusLabel->deployable) {
                    $types[] = '<span class="badge bg-label-success">Deployable</span>';
                }

                if ($statusLabel->pending) {
                    $types[] = '<span class="badge bg-label-warning">Pending</span>';
                }

                if ($statusLabel->archived) {
                    $types[] = '<span class="badge bg-label-secondary">Archived</span>';
                }

                return $types === [] ? '<span class="text-muted">-</span>' : implode(' ', $types);
            })
            ->addColumn('show_in_nav_badge', fn (StatusLabel $statusLabel): string => $statusLabel->show_in_nav
                ? '<span class="badge bg-label-info">Ya</span>'
                : '<span class="text-muted">-</span>')
            ->addColumn('default_label_badge', fn (StatusLabel $statusLabel): string => $statusLabel->default_label
                ? '<span class="badge bg-label-primary">Ya</span>'
                : '<span class="text-muted">-</span>')
            ->addColumn('creator_name', fn (StatusLabel $statusLabel): string => e($statusLabel->creator?->name ?? '-'))
            ->addColumn('action', function (StatusLabel $statusLabel): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('settings.status_labels.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('status-labels.show', $statusLabel).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.status_labels.edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('status-labels.edit', $statusLabel).'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.status_labels.delete')) {
                    $actions .= '<form action="'.route('status-labels.destroy', $statusLabel).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus status label ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (StatusLabel $statusLabel): ?string => $statusLabel->created_at?->toIso8601String())
            ->editColumn('updated_at', fn (StatusLabel $statusLabel): ?string => $statusLabel->updated_at?->toIso8601String())
            ->rawColumns(['color_preview', 'status_type', 'show_in_nav_badge', 'default_label_badge', 'action'])
            ->make(true);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function normalizeFlags(Request $request, array $validated): array
    {
        $validated['deployable'] = $request->boolean('deployable');
        $validated['pending'] = $request->boolean('pending');
        $validated['archived'] = $request->boolean('archived');
        $validated['show_in_nav'] = $request->boolean('show_in_nav');
        $validated['default_label'] = $request->boolean('default_label');

        return $validated;
    }
}
