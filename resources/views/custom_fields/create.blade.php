@extends('layouts.sneat')

@section('title')
    Tambah Custom Field
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Tambah Custom Field</h5>
                            <p class="mb-0 text-muted">Definisikan field tambahan untuk model aset inventory.</p>
                        </div>
                        <a href="{{ route('custom-fields.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('custom-fields.store') }}" method="POST">
                            @csrf

                            @include('custom_fields.partials.form', ['customField' => null])

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('custom-fields.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
