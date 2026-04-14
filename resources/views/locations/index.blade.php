@extends('layouts.sneat')

@section('title')
    Daftar Lokasi
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
                <h5 class="mb-0">Daftar Lokasi</h5>
                @if(auth()->user()->hasPermission('settings.locations.create'))
                    <a href="{{ route('locations.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Lokasi
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="locations-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Perusahaan</th>
                                <th>Lokasi</th>
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
            $('#locations-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('locations.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'company_name', name: 'company.name', orderable: false },
                    { data: 'name', name: 'name' },
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
                    { data: 'creator_name', name: 'created_by', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                dom: 'lBfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            });
        });
    </script>
@endpush
