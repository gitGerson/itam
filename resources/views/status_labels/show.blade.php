@extends('layouts.sneat')

@section('title')
    Detail Status Label
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
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
                    {{-- Info utama + flags + audit --}}
                    <div class="col-12">
                        <div class="border rounded-3 p-3 h-100">

                            {{-- Nama & Warna --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Status Label</div>
                                    <div class="fs-5 fw-bold">{{ $statusLabel->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small text-uppercase fw-semibold mb-1">Kode Warna</div>
                                    <div class="fw-semibold">
                                        @if($statusLabel->color)
                                            <span class="d-inline-flex align-items-center gap-2">
                                                <span class="rounded border" style="display:inline-block;width:16px;height:16px;background-color:{{ $statusLabel->color }};"></span>
                                                {{ $statusLabel->color }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Flags: Tipe Status --}}
                            <div class="text-muted small text-uppercase fw-semibold mb-2">Tipe Status</div>
                            <div class="row g-3 mb-4">
                                @php
                                    $flags = [
                                        ['label' => 'Deployable', 'value' => $statusLabel->deployable, 'active_class' => 'bg-label-success'],
                                        ['label' => 'Pending',    'value' => $statusLabel->pending,    'active_class' => 'bg-label-warning'],
                                        ['label' => 'Archived',   'value' => $statusLabel->archived,   'active_class' => 'bg-label-secondary'],
                                    ];
                                @endphp
                                @foreach($flags as $flag)
                                    <div class="col-md-4">
                                        <div class="border rounded-3 p-3 text-center">
                                            <div class="text-muted small text-uppercase fw-semibold mb-2">{{ $flag['label'] }}</div>
                                            @if($flag['value'])
                                                <span class="badge {{ $flag['active_class'] }} fs-6 px-3 py-2">Aktif</span>
                                            @else
                                                <span class="badge bg-label-light text-muted fs-6 px-3 py-2">Tidak Aktif</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Flags: Pengaturan Tambahan --}}
                            <div class="text-muted small text-uppercase fw-semibold mb-2">Pengaturan Tambahan</div>
                            <div class="row g-3 mb-4">
                                @php
                                    $settings = [
                                        ['label' => 'Tampilkan di Navigasi', 'value' => $statusLabel->show_in_nav,   'active_class' => 'bg-label-info'],
                                        ['label' => 'Default Label',         'value' => $statusLabel->default_label, 'active_class' => 'bg-label-primary'],
                                    ];
                                @endphp
                                @foreach($settings as $setting)
                                    <div class="col-md-4">
                                        <div class="border rounded-3 p-3 text-center">
                                            <div class="text-muted small text-uppercase fw-semibold mb-2">{{ $setting['label'] }}</div>
                                            @if($setting['value'])
                                                <span class="badge {{ $setting['active_class'] }} fs-6 px-3 py-2">Aktif</span>
                                            @else
                                                <span class="badge bg-label-light text-muted fs-6 px-3 py-2">Tidak Aktif</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="my-3">

                            {{-- Audit --}}
                            <div class="row g-3">
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
@endsection
