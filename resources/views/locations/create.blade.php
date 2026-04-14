@extends('layouts.sneat')

@section('title')
    Tambah Lokasi
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Tambah Lokasi</h5>
                    <p class="mb-0 text-muted">Kelola data lokasi cabang atau site untuk inventory.</p>
                </div>
                <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('locations.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @include('locations.partials.form', [
                        'location' => null,
                        'showCurrentImage' => false,
                    ])

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
