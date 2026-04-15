@extends('layouts.sneat')

@section('title')
    Edit Supplier
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Edit Supplier</h5>
                            <p class="mb-0 text-muted">Perbarui informasi supplier dan kontak pengadaannya.</p>
                        </div>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('suppliers.update', $supplier) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @include('suppliers.partials.form', [
                                'supplier' => $supplier,
                            ])

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary">Lihat Detail</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
