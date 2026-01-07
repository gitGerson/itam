@extends('layouts.sneat')

@section('title')
    Sync Product ESB
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
                <h5 class="mb-0">Sync Product ESB</h5>
            </div>
            <div class="card-body">
                <form id="sync-esb-form" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label for="statusActive" class="form-label">Status Active</label>
                        <select id="statusActive" name="statusActive" class="form-select">
                            <option value="Yes" selected>Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="page" class="form-label">Page</label>
                        <input type="number" class="form-control" id="page" name="page" min="1" value="1">
                    </div>
                    <div class="col-md-7 d-flex align-items-end gap-2">
                        @if(auth()->user()->hasPermission('master.products_esb.sync'))
                            <button type="button" class="btn btn-primary" data-sync="single"
                                data-url="{{ route('products-esb.sync-page') }}">
                                <i class="bx bx-refresh me-1"></i> Sync Page
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-sync="all"
                                data-url="{{ route('products-esb.sync-all') }}">
                                <i class="bx bx-cloud-download me-1"></i> Sync All
                            </button>
                        @else
                            <span class="text-muted">Anda tidak memiliki akses untuk sync.</span>
                        @endif
                    </div>
                </form>

                <div id="sync-result" class="alert d-none mt-3" role="alert"></div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Product ESB</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="products-esb-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product ID</th>
                                <th>Code</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Requestable</th>
                                <th>Purchasable</th>
                                <th>Saleable</th>
                                <th>Last Synced</th>
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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('sync-esb-form');
            const buttons = form ? form.querySelectorAll('[data-sync]') : [];
            const result = document.getElementById('sync-result');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if (!form || !buttons.length) {
                return;
            }

            buttons.forEach((button) => {
                button.addEventListener('click', async function() {
                    const statusActive = document.getElementById('statusActive').value;
                    const page = document.getElementById('page').value;
                    const url = button.getAttribute('data-url');
                    const syncMode = button.getAttribute('data-sync');

                    result.classList.add('d-none');
                    result.classList.remove('alert-success', 'alert-danger');

                    button.disabled = true;
                    const originalButtonHtml = button.innerHTML;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Syncing...';

                    const payload = {
                        statusActive: statusActive,
                    };

                    if (syncMode === 'single') {
                        payload.page = page;
                    }

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (!response.ok || data.status !== 'success') {
                            throw new Error(data.message || 'Sync failed');
                        }

                        result.classList.add('alert-success');
                        result.textContent = data.message;

                        if (window.productsEsbTable) {
                            window.productsEsbTable.ajax.reload(null, false);
                        }
                    } catch (error) {
                        result.classList.add('alert-danger');
                        result.textContent = error.message || 'Sync failed';
                    } finally {
                        result.classList.remove('d-none');
                        button.disabled = false;
                        button.innerHTML = originalButtonHtml;
                    }
                });
            });
        });
    </script>

    <script>
        $(function() {
            window.productsEsbTable = $('#products-esb-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('products-esb.data') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'product_id', name: 'product_id' },
                    { data: 'product_code', name: 'product_code' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'category_info', name: 'category_name', orderable: false, searchable: false },
                    { data: 'requestable_badge', name: 'requestable', orderable: false, searchable: false },
                    { data: 'purchasable_badge', name: 'purchasable', orderable: false, searchable: false },
                    { data: 'saleable_badge', name: 'saleable', orderable: false, searchable: false },
                    { data: 'last_synced_at', name: 'last_synced_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                dom: 'lBfrtip',
                buttons: [
                    { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },
                    { extend: 'csv', exportOptions: { columns: ':not(:last-child)' } },
                    { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
                    { extend: 'pdf', exportOptions: { columns: ':not(:last-child)' } },
                    { extend: 'print', exportOptions: { columns: ':not(:last-child)' } },
                    'colvis'
                ],
            });
        });
    </script>
@endpush
