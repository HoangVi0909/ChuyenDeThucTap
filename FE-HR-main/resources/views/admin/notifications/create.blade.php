@extends('layouts.admin')
@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-bell"></i> Thêm thông báo</h4>
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
                    <form action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề</label>
                            <input type="text" name="title" id="title"
                                class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung</label>
                            <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="4"
                                required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Loại</label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                <option value="news" @if (old('type') === 'news') selected @endif>Tin tức</option>
                                <option value="event" @if (old('type') === 'event') selected @endif>Sự kiện</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-danger px-4"><i class="fas fa-plus"></i> Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
