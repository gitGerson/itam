@extends('layouts.sneat')

@section('title')
    Daftar Custom Fields
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
                <h5 class="mb-0">Daftar Custom Fields</h5>
                @if(auth()->user()->hasPermission('inventory.custom_fields.create'))
                    <a href="{{ route('custom-fields.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Custom Field
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="custom-fields-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Field</th>
                                <th>Tipe Elemen</th>
                                <th>Format</th>
                                <th>Opsi</th>
                                <th>Dibuat</th>
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
            $('#custom-fields-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('custom-fields.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'element', name: 'element' },
                    {
                        data: 'format',
                        name: 'format',
                        render: function(data) {
                            return data ? '<code>' + $('<span>').text(data).html() + '</code>' : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'badges',
                        name: 'badges',
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
