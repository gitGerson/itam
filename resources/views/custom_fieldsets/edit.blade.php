@extends('layouts.sneat')

@section('title')
    Edit Custom Fieldset
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Edit Custom Fieldset</h5>
                            <p class="mb-0 text-muted">{{ $customFieldset->name }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermission('inventory.custom_fieldsets.view'))
                                <a href="{{ route('custom-fieldsets.show', $customFieldset) }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-show me-1"></i> Lihat
                                </a>
                            @endif
                            <a href="{{ route('custom-fieldsets.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('custom-fieldsets.update', $customFieldset) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @include('custom_fieldsets.partials.form', [
                                'customFieldset' => $customFieldset,
                                'customFields' => $customFields,
                            ])

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('custom-fieldsets.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
