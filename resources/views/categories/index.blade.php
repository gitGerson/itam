@extends('layouts.sneat')

@section('title')
    Daftar Kategori
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Kategori</h5>
                @if(auth()->user()->hasPermission('settings.categories.create'))
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                        <i class="bx bx-plus me-1"></i> Tambah Kategori
                    </button>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="categories-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Gambar</th>
                                <th>Tipe</th>
                                <th>Catatan</th>
                                <th>Dibuat oleh</th>
                                <th>Dibuat pada</th>
                                <th>Diperbaharui pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasPermission('settings.categories.create'))
        <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="createCategoryModalLabel">Tambah Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @include('categories.partials.form', [
                                'category' => null,
                                'fieldPrefix' => 'create_category',
                                'showCurrentImage' => false,
                            ])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if(auth()->user()->hasPermission('settings.categories.edit'))
        <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="editCategoryForm" action="{{ $editingCategory ? route('categories.update', $editingCategory) : '#' }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCategoryModalLabel">Edit Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @include('categories.partials.form', [
                                'category' => $editingCategory,
                                'fieldPrefix' => 'edit_category',
                                'showCurrentImage' => true,
                            ])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#categories-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('categories.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    {
                        data: 'image_preview',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'category_type', name: 'category_type' },
                    {
                        data: 'notes',
                        name: 'notes',
                        render: function(data) {
                            return data || '-';
                        }
                        },
                    {
                        data: 'creator_name',
                        name: 'created_by',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data ? moment(data).format('DD MMM YYYY HH:mm') : '-';
                        }
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        render: function(data) {
                            return data ? moment(data).format('DD MMM YYYY HH:mm') : '-';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: 'lBfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            });

            const editModalElement = document.getElementById('editCategoryModal');
            const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
            const editForm = document.getElementById('editCategoryForm');

            function resetEditImageField() {
                const imageInput = document.getElementById('edit_category_image');
                const currentImageWrapper = document.getElementById('edit_category_current_image_wrapper');
                const currentImagePreview = document.getElementById('edit_category_current_image_preview');
                const currentImagePath = document.getElementById('edit_category_current_image_path');

                if (window.FilePond && imageInput) {
                    const pond = window.FilePond.find(imageInput);
                    if (pond) {
                        pond.removeFiles();
                    }
                }

                document.querySelectorAll('input[type="hidden"][name="image"]').forEach((element) => element.remove());

                if (currentImageWrapper) {
                    currentImageWrapper.classList.add('d-none');
                }

                if (currentImagePreview) {
                    currentImagePreview.src = '';
                }

                if (currentImagePath) {
                    currentImagePath.textContent = '';
                }
            }

            $(document).on('click', '.js-edit-category', function() {
                if (!editModal || !editForm) {
                    return;
                }

                const category = $(this).data('category');
                if (!category) {
                    return;
                }

                editForm.action = category.update_url;
                document.getElementById('edit_category_name').value = category.name || '';
                document.getElementById('edit_category_category_type').value = category.category_type || 'asset';
                document.getElementById('edit_category_notes').value = category.notes || '';

                resetEditImageField();

                const currentImageWrapper = document.getElementById('edit_category_current_image_wrapper');
                const currentImagePreview = document.getElementById('edit_category_current_image_preview');
                const currentImagePath = document.getElementById('edit_category_current_image_path');

                if (category.image_url && currentImageWrapper && currentImagePreview && currentImagePath) {
                    currentImageWrapper.classList.remove('d-none');
                    currentImagePreview.src = category.image_url;
                    currentImagePath.textContent = category.image_path || '';
                }

                editModal.show();
            });

            if (editModalElement) {
                editModalElement.addEventListener('hidden.bs.modal', resetEditImageField);
            }
        });
    </script>
    @if ($modalState === 'create')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = document.getElementById('createCategoryModal');
                if (modalEl) {
                    new bootstrap.Modal(modalEl).show();
                }
            });
        </script>
    @endif
    @if ($modalState === 'edit' && $editingCategory)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = document.getElementById('editCategoryModal');
                if (modalEl) {
                    new bootstrap.Modal(modalEl).show();
                }
            });
        </script>
    @endif
@endpush
