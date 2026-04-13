@extends('layouts.sneat')

@section('title')
    Detail Supplier
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="mb-1">Detail Supplier</h5>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermission('inventory.suppliers.edit'))
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="border rounded-3 p-3 h-100 bg-body-tertiary">
                                    <div class="mb-3">
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Logo Supplier</div>
                                        @if($supplier->imageUrl())
                                            <div class="text-center">
                                                <img
                                                    src="{{ $supplier->imageUrl() }}"
                                                    alt="Logo {{ $supplier->name }}"
                                                    class="img-fluid rounded border bg-white"
                                                    style="max-height: 260px;"
                                                >
                                            </div>
                                            <div class="small text-muted mt-3 text-break">{{ $supplier->image }}</div>
                                        @else
                                            <div class="border rounded-3 d-flex align-items-center justify-content-center text-muted bg-white"
                                                 style="min-height: 260px;">
                                                Logo belum tersedia
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Catatan</div>
                                        <div class="fw-semibold text-break">{{ $supplier->notes ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Supplier</div>
                                            <div class="fs-5 fw-bold">{{ $supplier->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Contact Person</div>
                                            <div class="fw-semibold">{{ $supplier->contact ?: '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Email</div>
                                            <div class="fw-semibold text-break">{{ $supplier->email ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Website</div>
                                            <div class="fw-semibold text-break">{{ $supplier->url ?: '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Telepon</div>
                                            <div class="fw-semibold">{{ $supplier->phone ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Fax</div>
                                            <div class="fw-semibold">{{ $supplier->fax ?: '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Alamat 1</div>
                                            <div class="fw-semibold text-break">{{ $supplier->address ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Alamat 2</div>
                                            <div class="fw-semibold text-break">{{ $supplier->address2 ?: '-' }}</div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Kota</div>
                                            <div class="fw-semibold">{{ $supplier->city ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Provinsi</div>
                                            <div class="fw-semibold">{{ $supplier->state ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Kode Pos</div>
                                            <div class="fw-semibold">{{ $supplier->zip ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Negara</div>
                                            <div class="fw-semibold">{{ $supplier->country ?: '-' }}</div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Dibuat Oleh</div>
                                            <div class="fw-semibold">{{ $supplier->creator?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Diperbarui Oleh</div>
                                            <div class="fw-semibold">{{ $supplier->updater?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Waktu Dibuat</div>
                                            <div class="fw-semibold">{{ $supplier->created_at?->format('d M Y H:i') ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
