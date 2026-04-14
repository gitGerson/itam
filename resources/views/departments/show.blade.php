@extends('layouts.sneat')

@section('title')
    Detail Departemen
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="mb-1">Detail Departemen</h5>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->hasPermission('settings.departments.edit'))
                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                    @endif
                    <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-4">
                    {{-- Info utama --}}
                    <div class="col-lg-8">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Departemen</div>
                                    <div class="fs-5 fw-bold">{{ $department->name }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Company</div>
                                    <div class="fw-semibold">{{ $department->company?->name ?: '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Lokasi</div>
                                    <div class="fw-semibold">
                                        @if($department->location)
                                            @if(auth()->user()->hasPermission('settings.locations.view'))
                                                <a href="{{ route('locations.show', $department->location) }}">{{ $department->location->name }}</a>
                                            @else
                                                {{ $department->location->name }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Manager</div>
                                    <div class="fw-semibold">{{ $department->manager?->name ?: '-' }}</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Dibuat Oleh</div>
                                    <div class="fw-semibold">{{ $department->creator?->name ?: '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Diperbarui Oleh</div>
                                    <div class="fw-semibold">{{ $department->updater?->name ?: '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Waktu Dibuat</div>
                                    <div class="fw-semibold">{{ $department->created_at?->format('d M Y H:i') ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="col-lg-4">
                        <div class="border rounded-3 p-3 h-100 bg-body-tertiary">
                            <div class="text-muted small text-uppercase fw-semibold mb-2">Catatan</div>
                            <div class="fw-semibold text-break">{{ $department->notes ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
