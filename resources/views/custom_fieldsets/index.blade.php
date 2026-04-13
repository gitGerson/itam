@extends('layouts.sneat')

@section('title')
    Daftar Custom Fieldsets
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
                <h5 class="mb-0">Daftar Custom Fieldsets</h5>
                @if(auth()->user()->hasPermission('inventory.custom_fieldsets.create'))
                    <a href="{{ route('custom-fieldsets.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Fieldset
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="custom-fieldsets-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Fieldset</th>
                                <th>Catatan</th>
                                <th>Jumlah Field</th>
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
            $('#custom-fieldsets-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('custom-fieldsets.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    {
                        data: 'notes',
                        name: 'notes',
                        render: function(data) {
                            return data ? $('<span>').text(data).html() : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'field_count',
                        name: 'field_count',
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
