@extends('layouts.sneat')

@section('title')
    Detail Model
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="mb-1">Detail Model</h5>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermission('settings.models.edit'))
                                <a href="{{ route('models.edit', $assetModel) }}" class="btn btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('models.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="border rounded-3 p-3 h-100 bg-body-tertiary">
                                    <div class="mb-3">
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Gambar Model</div>
                                        @if($assetModel->imageUrl())
                                            <div class="text-center">
                                                <img
                                                    src="{{ $assetModel->imageUrl() }}"
                                                    alt="Gambar {{ $assetModel->name }}"
                                                    class="img-fluid rounded border bg-white"
                                                    style="max-height: 260px;"
                                                >
                                            </div>
                                            <div class="small text-muted mt-3 text-break">{{ $assetModel->image }}</div>
                                        @else
                                            <div class="border rounded-3 d-flex align-items-center justify-content-center text-muted bg-white"
                                                 style="min-height: 260px;">
                                                Gambar belum tersedia
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold mb-2">Catatan</div>
                                        <div class="fw-semibold text-break">{{ $assetModel->notes ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Model</div>
                                            <div class="fs-5 fw-bold">{{ $assetModel->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Model Number</div>
                                            <div class="fw-semibold">{{ $assetModel->model_number ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Manufacturer</div>
                                            <div class="fw-semibold">{{ $assetModel->manufacturer?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Kategori</div>
                                            <div class="fw-semibold">{{ $assetModel->category?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Fieldset</div>
                                            <div class="fw-semibold">{{ $assetModel->fieldset?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">EOL</div>
                                            <div class="fw-semibold">{{ $assetModel->eol ? $assetModel->eol.' bulan' : '-' }}</div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Dibuat Oleh</div>
                                            <div class="fw-semibold">{{ $assetModel->creator?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Diperbarui Oleh</div>
                                            <div class="fw-semibold">{{ $assetModel->updater?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Waktu Dibuat</div>
                                            <div class="fw-semibold">{{ $assetModel->created_at?->format('d M Y H:i') ?: '-' }}</div>
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
