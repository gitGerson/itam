<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\FilePondUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(protected FilePondUploadService $filePondUploads) {}

    public function index(Request $request): View
    {
        $modalState = $request->query('modal');
        $editingCategory = null;

        if ($modalState === 'edit' && $request->filled('category')) {
            $editingCategory = Category::find($request->integer('category'));
        }

        return view('categories.index', compact('modalState', 'editingCategory'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['checkin_email'] = true;
        $validated['image'] = $this->filePondUploads->storeFromRequestField($request, 'image', 'categories', 's3');

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function show(Category $category): View
    {
        $category->load(['creator', 'updater', 'deleter']);

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();
        $validated['checkin_email'] = true;
        $uploadedImagePath = $this->filePondUploads->storeFromRequestField($request, 'image', 'categories', 's3');

        if ($uploadedImagePath !== null) {
            if ($category->image) {
                Storage::disk('s3')->delete($category->image);
            }

            $validated['image'] = $uploadedImagePath;
        } else {
            $validated['image'] = $category->image;
        }

        $category->update($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }

    public function getData(): JsonResponse
    {
        $categories = Category::query()
            ->select([
                'id',
                'name',
                'category_type',
                'image',
                'created_at',
            ]);

        return datatables()->of($categories)
            ->addColumn('logo', function (Category $category): string {
                if (! $category->imageUrl()) {
                    return '<div class="d-flex align-items-center justify-content-center rounded border bg-label-secondary text-muted" style="width: 44px; height: 44px;">
                        <i class="bx bx-image-alt"></i>
                    </div>';
                }

                return '<img src="'.e($category->imageUrl()).'" alt="'.e($category->name).'" class="rounded border bg-white" style="width: 44px; height: 44px; object-fit: cover;">';
            })
            ->editColumn('category_type', fn (Category $category): string => e(Str::headline($category->category_type)))
            ->addColumn('action', function (Category $category): string {
                $actions = '<div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">';

                if (auth()->user()->hasPermission('settings.categories.view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('categories.show', $category).'">
                        <i class="bx bx-show me-1"></i> Lihat
                    </a>';
                }

                if (auth()->user()->hasPermission('settings.categories.edit')) {
                    $categoryPayload = e(json_encode([
                        'id' => $category->id,
                        'name' => $category->name,
                        'category_type' => $category->category_type,
                        'notes' => $category->notes,
                        'image_url' => $category->imageUrl(),
                        'image_path' => $category->image,
                        'update_url' => route('categories.update', $category),
                    ]));

                    $actions .= '<button type="button" class="dropdown-item js-edit-category" data-category="'.$categoryPayload.'">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </button>';
                }

                if (auth()->user()->hasPermission('settings.categories.delete')) {
                    $actions .= '<form action="'.route('categories.destroy', $category).'" method="POST" style="display: inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="dropdown-item" onclick="return confirm(\'Yakin ingin menghapus kategori ini?\')">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>';
                }

                return $actions.'</div></div>';
            })
            ->editColumn('created_at', fn (Category $category): ?string => $category->created_at?->toIso8601String())
            ->rawColumns(['logo', 'action'])
            ->make(true);
    }
}
