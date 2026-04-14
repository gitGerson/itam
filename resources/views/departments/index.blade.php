@extends('layouts.sneat')

@section('title')
    Daftar Departemen
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
                <h5 class="mb-0">Daftar Departemen</h5>
                @if(auth()->user()->hasPermission('settings.departments.create'))
                    <a href="{{ route('departments.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Departemen
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="departments-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Perusahaan</th>
                                <th>Nama Departemen</th>
                                <th>Gambar</th>
                                <th>Manager</th>
                                <th>Pengguna</th>
                                <th>Lokasi</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#departments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('departments.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'company_name', name: 'company.name', orderable: false },
                    { data: 'name', name: 'name' },
                    { data: 'image_preview', name: 'image', orderable: false, searchable: false },
                    { data: 'manager_name', name: 'manager_id', orderable: false },
                    { data: 'creator_name', name: 'created_by', orderable: false, searchable: false },
                    { data: 'location_name', name: 'location.name', orderable: false },
                    {
                        data: 'notes',
                        name: 'notes',
                        orderable: false,
                        render: function(data) { return data || '-'; }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                dom: 'lBfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            });
        });
    </script>
@endpush
