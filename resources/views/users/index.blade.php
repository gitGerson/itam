@extends('layouts.sneat')

@section('title')
    Daftar User
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar User</h5>
                <div>
                    @if(auth()->user()->hasPermission('users.logs'))
                        <a href="{{ route('users.logs') }}" class="btn btn-info me-2">
                            <i class="bx bx-history me-1"></i> Activity Logs
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('users.delete'))
                        <a href="{{ route('users.trash') }}" class="btn btn-secondary me-2">
                            <i class="bx bx-trash me-1"></i> Tempat Sampah
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('roles.view'))
                        <a href="{{ route('roles.index') }}" class="btn btn-warning me-2">
                            <i class="bx bx-shield me-1"></i> Kelola Role
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('users.create'))
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Tambah User
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">

                    <table id="users-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Dibuat</th>
                                <th>Dibuat Oleh</th>
                                <th>Diperbarui</th>
                                <th>Diperbarui Oleh</th>
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
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('users.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return moment(data).format('DD MMM YYYY HH:mm');
                        }
                    },
                    {
                        data: 'created_by_name',
                        name: 'created_by_name'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        render: function(data, type, row) {
                            return moment(data).format('DD MMM YYYY HH:mm');
                        }
                    },
                    {
                        data: 'updated_by_name',
                        name: 'updated_by_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
                ],
            });
        });
    </script>
@endpush
