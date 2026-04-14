@extends('layouts.sneat')

@section('title')
    Daftar Manufacturer
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
                <h5 class="mb-0">Daftar Manufacturer</h5>
                @if(auth()->user()->hasPermission('settings.manufacturers.create'))
                    <a href="{{ route('manufacturers.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Manufacturer
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="manufacturers-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Logo</th>
                                <th>Nama</th>
                                <th>Website</th>
                                <th>Support Phone</th>
                                <th>Support Email</th>
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
            $('#manufacturers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('manufacturers.data') }}',
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
                        data: 'url',
                        name: 'url',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'support_phone',
                        name: 'support_phone',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'support_email',
                        name: 'support_email',
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
        });
    </script>
@endpush
