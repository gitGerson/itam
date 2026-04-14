@extends('layouts.sneat')

@section('title')
    Detail Manufacturer
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="mb-1">Detail Manufacturer</h5>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermission('settings.manufacturers.edit'))
                                <a href="{{ route('manufacturers.edit', $manufacturer) }}" class="btn btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="border rounded-3 p-3 h-100 bg-body-tertiary">
                                    <div class="mb-3">
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Logo Manufacturer</div>
                                        @if($manufacturer->imageUrl())
                                            <div class="text-center">
                                                <img
                                                    src="{{ $manufacturer->imageUrl() }}"
                                                    alt="Logo {{ $manufacturer->name }}"
                                                    class="img-fluid rounded border bg-white"
                                                    style="max-height: 260px;"
                                                >
                                            </div>
                                            <div class="small text-muted mt-3 text-break">{{ $manufacturer->image }}</div>
                                        @else
                                            <div class="border rounded-3 d-flex align-items-center justify-content-center text-muted bg-white"
                                                 style="min-height: 260px;">
                                                Logo belum tersedia
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Catatan</div>
                                        <div class="fw-semibold text-break">{{ $manufacturer->notes ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Manufacturer</div>
                                            <div class="fs-5 fw-bold">{{ $manufacturer->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Website</div>
                                            <div class="fw-semibold text-break">{{ $manufacturer->url ?: '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Support URL</div>
                                            <div class="fw-semibold text-break">{{ $manufacturer->support_url ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Warranty Lookup URL</div>
                                            <div class="fw-semibold text-break">{{ $manufacturer->warranty_lookup_url ?: '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Support Phone</div>
                                            <div class="fw-semibold">{{ $manufacturer->support_phone ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Support Email</div>
                                            <div class="fw-semibold text-break">{{ $manufacturer->support_email ?: '-' }}</div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Dibuat Oleh</div>
                                            <div class="fw-semibold">{{ $manufacturer->creator?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Diperbarui Oleh</div>
                                            <div class="fw-semibold">{{ $manufacturer->updater?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Waktu Dibuat</div>
                                            <div class="fw-semibold">{{ $manufacturer->created_at?->format('d M Y H:i') ?: '-' }}</div>
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
