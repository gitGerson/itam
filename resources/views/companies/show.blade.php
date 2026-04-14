@extends('layouts.sneat')

@section('title')
    Detail Company
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="mb-1">Detail Company</h5>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermission('settings.companies.edit'))
                                <a href="{{ route('companies.edit', $company) }}" class="btn btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="border rounded-3 p-3 h-100 bg-body-tertiary">
                                    <div class="mb-3">
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Logo Company</div>
                                        @if($company->imageUrl())
                                            <div class="text-center">
                                                <img
                                                    src="{{ $company->imageUrl() }}"
                                                    alt="Logo {{ $company->name }}"
                                                    class="img-fluid rounded border bg-white"
                                                    style="max-height: 260px;"
                                                >
                                            </div>
                                            <div class="small text-muted mt-3 text-break">{{ $company->image }}</div>
                                        @else
                                            <div class="border rounded-3 d-flex align-items-center justify-content-center text-muted bg-white"
                                                 style="min-height: 260px;">
                                                Logo belum tersedia
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Catatan</div>
                                        <div class="fw-semibold text-break">{{ $company->notes ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Company</div>
                                            <div class="fs-5 fw-bold">{{ $company->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Email</div>
                                            <div class="fw-semibold text-break">{{ $company->email ?: '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Telepon</div>
                                            <div class="fw-semibold">{{ $company->phone ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Fax</div>
                                            <div class="fw-semibold">{{ $company->fax ?: '-' }}</div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Dibuat Oleh</div>
                                            <div class="fw-semibold">{{ $company->creator?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Diperbarui Oleh</div>
                                            <div class="fw-semibold">{{ $company->updater?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Waktu Dibuat</div>
                                            <div class="fw-semibold">{{ $company->created_at?->format('d M Y H:i') ?: '-' }}</div>
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
