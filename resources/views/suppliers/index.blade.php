@extends('layouts.sneat')

@section('title')
    Daftar Supplier
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
                <h5 class="mb-0">Daftar Supplier</h5>
                @if(auth()->user()->hasPermission('settings.suppliers.create'))
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Supplier
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="suppliers-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Gambar</th>
                                <th>URL</th>
                                <th>Alamat</th>
                                <th>Alamat 2</th>
                                <th>Kota</th>
                                <th>Provinsi</th>
                                <th>Kode Pos</th>
                                <th>Negara</th>
                                <th>Telepon</th>
                                <th>Fax</th>
                                <th>Catatan</th>
                                <th>Dibuat Pada</th>
                                <th>Dibuat Oleh</th>
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
            $('#suppliers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('suppliers.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    {
                        data: 'logo',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'url',
                        name: 'url',
                        render: function(data) {
                            return data ? '<a href="' + data + '" target="_blank" rel="noopener">' + data + '</a>' : '-';
                        }
                    },
                    {
                        data: 'address',
                        name: 'address',
                        render: function(data) { return data || '-'; }
                    },
                    {
                        data: 'address2',
                        name: 'address2',
                        render: function(data) { return data || '-'; }
                    },
                    {
                        data: 'city',
                        name: 'city',
                        render: function(data) { return data || '-'; }
                    },
                    {
                        data: 'state',
                        name: 'state',
                        render: function(data) { return data || '-'; }
                    },
                    {
                        data: 'zip',
                        name: 'zip',
                        render: function(data) { return data || '-'; }
                    },
                    {
                        data: 'country',
                        name: 'country',
                        render: function(data) { return data || '-'; }
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        render: function(data) { return data || '-'; }
                    },
                    {
                        data: 'fax',
                        name: 'fax',
                        render: function(data) { return data || '-'; }
                    },
                    {
                        data: 'notes',
                        name: 'notes',
                        render: function(data) { return data || '-'; }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data ? moment(data).format('DD MMM YYYY HH:mm') : '-';
                        }
                    },
                    {
                        data: 'creator_name',
                        name: 'created_by',
                        orderable: false,
                        searchable: false
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
        });
    </script>
@endpush
