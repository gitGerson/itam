@extends('layouts.sneat')

@section('title')
    Detail Custom Field
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="mb-1">Detail Custom Field</h5>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermission('inventory.custom_fields.edit'))
                                <a href="{{ route('custom-fields.edit', $customField) }}" class="btn btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('custom-fields.index') }}" class="btn btn-secondary">
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
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Field</div>
                                            <div class="fs-5 fw-bold">{{ $customField->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Tipe Elemen</div>
                                            <div class="fw-semibold">
                                                {{ \App\Models\CustomField::elementOptions()[$customField->element] ?? \Illuminate\Support\Str::headline($customField->element) }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Format Validasi</div>
                                            <div>
                                                @if($customField->format)
                                                    <code>{{ $customField->format }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Opsi</div>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @if($customField->field_encrypted)
                                                    <span class="badge bg-label-warning">Encrypted</span>
                                                @endif
                                                @if($customField->show_in_email)
                                                    <span class="badge bg-label-info">Tampil di Email</span>
                                                @endif
                                                @if(!$customField->field_encrypted && !$customField->show_in_email)
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Teks Bantuan</div>
                                            <div class="fw-semibold text-break">{{ $customField->help_text ?: '-' }}</div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Dibuat Oleh</div>
                                            <div class="fw-semibold">{{ $customField->creator?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Diperbarui Oleh</div>
                                            <div class="fw-semibold">{{ $customField->updater?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Waktu Dibuat</div>
                                            <div class="fw-semibold">{{ $customField->created_at?->format('d M Y H:i') ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Pilihan nilai --}}
                            <div class="col-lg-4">
                                <div class="border rounded-3 p-3 h-100 bg-body-tertiary">
                                    <div class="text-muted small text-uppercase fw-semibold mb-2">Pilihan Nilai</div>
                                    @if($customField->hasValueList())
                                        @php $values = $customField->fieldValuesList(); @endphp
                                        @if($values)
                                            <ul class="list-unstyled mb-0">
                                                @foreach($values as $value)
                                                    <li class="py-1 border-bottom small">{{ $value }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted small">Belum ada pilihan nilai.</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">Tidak berlaku untuk tipe elemen ini.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
