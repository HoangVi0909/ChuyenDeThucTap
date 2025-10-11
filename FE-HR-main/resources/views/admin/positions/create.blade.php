@extends('layouts.admin')
@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-briefcase me-2"></i> Thêm chức vụ</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.positions.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên chức vụ</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-times"></i> Hủy</a>
                            <button type="submit" class="btn btn-info px-4"><i class="fas fa-save"></i> Thêm chức
                                vụ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
