@extends('layouts.sneat')

@section('title')
    Daftar Status Label
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
                <h5 class="mb-0">Daftar Status Label</h5>
                @if(auth()->user()->hasPermission('settings.status_labels.create'))
                    <a href="{{ route('status-labels.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Status Label
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="status-labels-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Tipe Status</th>
                                <th>Warna Bagan</th>
                                <th>Tampilkan di Navigasi</th>
                                <th>Default Label</th>
                                <th>Catatan</th>
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
            $('#status-labels-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('status-labels.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    {
                        data: 'status_type',
                        name: 'status_type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'color_preview',
                        name: 'color',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'show_in_nav_badge',
                        name: 'show_in_nav',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'default_label_badge',
                        name: 'default_label',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notes',
                        name: 'notes',
                        render: function(data) { return data || '-'; }
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
