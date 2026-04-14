@extends('layouts.sneat')

@section('title')
    Edit Custom Field
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Edit Custom Field</h5>
                            <p class="mb-0 text-muted">{{ $customField->name }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermission('settings.custom_fields.view'))
                                <a href="{{ route('custom-fields.show', $customField) }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-show me-1"></i> Lihat
                                </a>
                            @endif
                            <a href="{{ route('custom-fields.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('custom-fields.update', $customField) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @include('custom_fields.partials.form', ['customField' => $customField])

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('custom-fields.index') }}" class="btn btn-outline-secondary">Batal</a>
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
