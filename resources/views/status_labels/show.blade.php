@extends('layouts.sneat')

@section('title')
    Detail Status Label
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="mb-1">Detail Status Label</h5>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermission('settings.status_labels.edit'))
                                <a href="{{ route('status-labels.edit', $statusLabel) }}" class="btn btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('status-labels.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="border rounded-3 p-3 h-100 bg-body-tertiary">
                                    <div class="mb-3">
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Color Preview</div>
                                        @if($statusLabel->color)
                                            <div class="border rounded-3 d-flex align-items-center justify-content-center bg-white"
                                                 style="min-height: 180px; background-color: {{ $statusLabel->color }} !important;">
                                                <span class="badge bg-dark">{{ $statusLabel->color }}</span>
                                            </div>
                                        @else
                                            <div class="border rounded-3 d-flex align-items-center justify-content-center text-muted bg-white"
                                                 style="min-height: 180px;">
                                                Warna belum diatur
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Catatan</div>
                                        <div class="fw-semibold text-break">{{ $statusLabel->notes ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Status Label</div>
                                            <div class="fs-5 fw-bold">{{ $statusLabel->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Warna</div>
                                            <div class="fw-semibold">{{ $statusLabel->color ?: '-' }}</div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Deployable</div>
                                            <div class="fw-semibold">{{ $statusLabel->deployable ? 'Ya' : 'Tidak' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Pending</div>
                                            <div class="fw-semibold">{{ $statusLabel->pending ? 'Ya' : 'Tidak' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Archived</div>
                                            <div class="fw-semibold">{{ $statusLabel->archived ? 'Ya' : 'Tidak' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Show in Nav</div>
                                            <div class="fw-semibold">{{ $statusLabel->show_in_nav ? 'Ya' : 'Tidak' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Default Label</div>
                                            <div class="fw-semibold">{{ $statusLabel->default_label ? 'Ya' : 'Tidak' }}</div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Dibuat Oleh</div>
                                            <div class="fw-semibold">{{ $statusLabel->creator?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Diperbarui Oleh</div>
                                            <div class="fw-semibold">{{ $statusLabel->updater?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Waktu Dibuat</div>
                                            <div class="fw-semibold">{{ $statusLabel->created_at?->format('d M Y H:i') ?: '-' }}</div>
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
