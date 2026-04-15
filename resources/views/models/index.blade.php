@extends('layouts.sneat')

@section('title')
    Daftar Model
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
                <h5 class="mb-0">Daftar Model</h5>
                @if(auth()->user()->hasPermission('settings.models.create'))
                    <a href="{{ route('models.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Model
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="models-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Model Number</th>
                                <th>Gambar</th>
                                <th>Manufacturer</th>
                                <th>Kategori</th>
                                <th>Fieldset</th>
                                <th>EOL</th>
                                <th>Dibuat Oleh</th>
                                <th>Dibuat Pada</th>
                                <th>Diperbarui Pada</th>
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
            $('#models-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('models.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    {
                        data: 'model_number',
                        name: 'model_number',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'image_preview',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'manufacturer_name',
                        name: 'manufacturer.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'category_name',
                        name: 'category.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'fieldset_name',
                        name: 'fieldset.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'eol',
                        name: 'eol',
                        render: function(data) {
                            return data ? data + ' bulan' : '-';
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
        });
    </script>
@endpush
