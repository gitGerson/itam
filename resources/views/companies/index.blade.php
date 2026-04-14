@extends('layouts.sneat')

@section('title')
    Daftar Company
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
                <h5 class="mb-0">Daftar Company</h5>
                @if(auth()->user()->hasPermission('settings.companies.create'))
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
                        <i class="bx bx-plus me-1"></i> Tambah Company
                    </button>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="companies-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Logo</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Fax</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasPermission('settings.companies.create'))
        <div class="modal fade" id="createCompanyModal" tabindex="-1" aria-labelledby="createCompanyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('companies.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="createCompanyModalLabel">Tambah Company</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @include('companies.partials.form', [
                                'company' => null,
                                'fieldPrefix' => 'create_company',
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

    @if(auth()->user()->hasPermission('settings.companies.edit'))
        <div class="modal fade" id="editCompanyModal" tabindex="-1" aria-labelledby="editCompanyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="editCompanyForm" action="{{ $editingCompany ? route('companies.update', $editingCompany) : '#' }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCompanyModalLabel">Edit Company</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @include('companies.partials.form', [
                                'company' => $editingCompany,
                                'fieldPrefix' => 'edit_company',
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
            $('#companies-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('companies.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    {
                        data: 'logo',
                        name: 'logo',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'name', name: 'name' },
                    {
                        data: 'email',
                        name: 'email',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'fax',
                        name: 'fax',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
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

            const editModalElement = document.getElementById('editCompanyModal');
            const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
            const editForm = document.getElementById('editCompanyForm');

            function resetEditImageField() {
                const imageInput = document.getElementById('edit_company_image');
                const currentImageWrapper = document.getElementById('edit_company_current_image_wrapper');
                const currentImagePreview = document.getElementById('edit_company_current_image_preview');
                const currentImagePath = document.getElementById('edit_company_current_image_path');

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

            $(document).on('click', '.js-edit-company', function() {
                if (!editModal || !editForm) {
                    return;
                }

                const company = $(this).data('company');
                if (!company) {
                    return;
                }

                editForm.action = company.update_url;
                document.getElementById('edit_company_name').value = company.name || '';
                document.getElementById('edit_company_email').value = company.email || '';
                document.getElementById('edit_company_phone').value = company.phone || '';
                document.getElementById('edit_company_fax').value = company.fax || '';
                document.getElementById('edit_company_notes').value = company.notes || '';

                resetEditImageField();

                const currentImageWrapper = document.getElementById('edit_company_current_image_wrapper');
                const currentImagePreview = document.getElementById('edit_company_current_image_preview');
                const currentImagePath = document.getElementById('edit_company_current_image_path');

                if (company.image_url && currentImageWrapper && currentImagePreview && currentImagePath) {
                    currentImageWrapper.classList.remove('d-none');
                    currentImagePreview.src = company.image_url;
                    currentImagePath.textContent = company.image_path || '';
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
                const modalEl = document.getElementById('createCompanyModal');
                if (modalEl) {
                    new bootstrap.Modal(modalEl).show();
                }
            });
        </script>
    @endif
    @if ($modalState === 'edit' && $editingCompany)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = document.getElementById('editCompanyModal');
                if (modalEl) {
                    new bootstrap.Modal(modalEl).show();
                }
            });
        </script>
    @endif
@endpush
