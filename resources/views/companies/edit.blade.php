@extends('layouts.sneat')

@section('title')
    Edit Company
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Company</h5>
                        <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('companies.update', $company) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('companies.partials.form', ['company' => $company])

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Update
                                </button>
                                <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                                    <i class="bx bx-x me-1"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
