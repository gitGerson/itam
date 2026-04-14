@extends('layouts.sneat')

@section('title')
    Detail Lokasi
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="mb-1">Detail Lokasi</h5>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->hasPermission('settings.locations.edit'))
                        <a href="{{ route('locations.edit', $location) }}" class="btn btn-primary">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                    @endif
                    <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-4">
                    {{-- Gambar --}}
                    <div class="col-lg-3">
                        <div class="border rounded-3 p-3 h-100 bg-body-tertiary text-center">
                            @if($location->imageUrl())
                                <img
                                    src="{{ $location->imageUrl() }}"
                                    alt="{{ $location->name }}"
                                    class="img-fluid rounded border bg-white"
                                    style="max-height: 220px;"
                                >
                            @else
                                <div class="border rounded-3 d-flex align-items-center justify-content-center text-muted bg-white" style="min-height: 180px;">
                                    <i class="bx bx-map-alt bx-lg"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Info utama --}}
                    <div class="col-lg-5">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Lokasi</div>
                                    <div class="fs-5 fw-bold">{{ $location->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Company</div>
                                    <div class="fw-semibold">{{ $location->company?->name ?: '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Lokasi Induk</div>
                                    <div class="fw-semibold">
                                        @if($location->parent)
                                            @if(auth()->user()->hasPermission('settings.locations.view'))
                                                <a href="{{ route('locations.show', $location->parent) }}">{{ $location->parent->name }}</a>
                                            @else
                                                {{ $location->parent->name }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Manager</div>
                                    <div class="fw-semibold">{{ $location->manager?->name ?: '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Telepon</div>
                                    <div class="fw-semibold">{{ $location->phone ?: '-' }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Alamat Lengkap</div>
                                    <div class="fw-semibold text-wrap">{{ $location->fullAddress() ?: '-' }}</div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Dibuat Oleh</div>
                                    <div class="fw-semibold small">{{ $location->creator?->name ?: '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Diperbarui Oleh</div>
                                    <div class="fw-semibold small">{{ $location->updater?->name ?: '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Waktu Dibuat</div>
                                    <div class="fw-semibold small">{{ $location->created_at?->format('d M Y H:i') ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-lokasi --}}
                    <div class="col-lg-4">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="text-muted small text-uppercase fw-semibold">Sub-Lokasi</div>
                                <span class="badge bg-label-primary">{{ $location->children->count() }}</span>
                            </div>
                            @if($location->children->isEmpty())
                                <div class="text-muted small">Tidak ada sub-lokasi.</div>
                            @else
                                <ul class="list-unstyled mb-0">
                                    @foreach($location->children as $child)
                                        <li class="py-1 border-bottom d-flex align-items-center gap-2">
                                            <i class="bx bx-subdirectory-right text-muted"></i>
                                            @if(auth()->user()->hasPermission('settings.locations.view'))
                                                <a href="{{ route('locations.show', $child) }}" class="fw-semibold small">{{ $child->name }}</a>
                                            @else
                                                <span class="fw-semibold small">{{ $child->name }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
