@extends('layouts.sneat')

@section('title')
    Detail Custom Fieldset
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="mb-1">Detail Custom Fieldset</h5>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermission('settings.custom_fieldsets.edit'))
                                <a href="{{ route('custom-fieldsets.edit', $customFieldset) }}" class="btn btn-primary">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('custom-fieldsets.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-4">
                            {{-- Info fieldset --}}
                            <div class="col-lg-5">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="mb-4">
                                        <div class="text-muted small text-uppercase fw-semibold mb-1">Nama Fieldset</div>
                                        <div class="fs-5 fw-bold">{{ $customFieldset->name }}</div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="text-muted small text-uppercase fw-semibold mb-1">Catatan</div>
                                        <div class="fw-semibold text-break">{{ $customFieldset->notes ?: '-' }}</div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="text-muted small text-uppercase fw-semibold mb-1">Repeatable</div>
                                        @if($customFieldset->repeatable)
                                            <span class="badge bg-label-success">Ya</span>
                                        @else
                                            <span class="badge bg-label-secondary">Tidak</span>
                                        @endif
                                    </div>

                                    <hr class="my-3">

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Dibuat Oleh</div>
                                            <div class="fw-semibold small">{{ $customFieldset->creator?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Diperbarui Oleh</div>
                                            <div class="fw-semibold small">{{ $customFieldset->updater?->name ?: '-' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Waktu Dibuat</div>
                                            <div class="fw-semibold small">{{ $customFieldset->created_at?->format('d M Y H:i') ?: '-' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Terakhir Diperbarui</div>
                                            <div class="fw-semibold small">{{ $customFieldset->updated_at?->format('d M Y H:i') ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Daftar custom fields --}}
                            <div class="col-lg-7">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-muted small text-uppercase fw-semibold">Custom Fields</div>
                                        <span class="badge bg-label-primary">{{ $customFieldset->customFields->count() }} field</span>
                                    </div>

                                    @if($customFieldset->customFields->isEmpty())
                                        <div class="text-muted small">Fieldset ini belum memiliki custom fields.</div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Nama Field</th>
                                                        <th>Tipe Elemen</th>
                                                        <th>Opsi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($customFieldset->customFields as $index => $field)
                                                        <tr>
                                                            <td class="text-muted">{{ $index + 1 }}</td>
                                                            <td class="fw-semibold">
                                                                @if(auth()->user()->hasPermission('settings.custom_fields.view'))
                                                                    <a href="{{ route('custom-fields.show', $field) }}">{{ $field->name }}</a>
                                                                @else
                                                                    {{ $field->name }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-label-secondary">
                                                                    {{ \App\Models\CustomField::elementOptions()[$field->element] ?? $field->element }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($field->field_encrypted)
                                                                    <span class="badge bg-label-warning">Encrypted</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
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
