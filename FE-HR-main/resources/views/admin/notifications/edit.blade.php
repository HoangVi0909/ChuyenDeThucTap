@extends('layouts.admin')
@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Sửa thông báo</h4>
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
                    <form action="{{ route('admin.notifications.update', $notification['id']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề</label>
                            <input type="text" name="title" id="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $notification['title']) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung</label>
                            <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="4"
                                required>{{ old('content', $notification['content']) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Loại</label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                <option value="news" @if (old('type', $notification['type']) === 'news') selected @endif>Tin tức</option>
                                <option value="event" @if (old('type', $notification['type']) === 'event') selected @endif>Sự kiện</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-success px-4"><i class="fas fa-save"></i> Cập
                                nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
