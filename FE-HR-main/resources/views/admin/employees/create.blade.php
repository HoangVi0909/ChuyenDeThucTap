@extends('layouts.admin')
@section('content')

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i> Thêm nhân viên mới</h4>
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
                    <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên nhân viên</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Giới tính</label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">Chọn giới tính</option>
                                <option value="Nam" {{ old('gender') == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gender') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('gender') == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- <div class="mb-3">
                            <label for="photo" class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo"
                                name="photo">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="cccd" class="form-label">CCCD</label>
                            <input type="text" class="form-control @error('cccd') is-invalid @enderror" id="cccd"
                                name="cccd" value="{{ old('cccd') }}" required>
                            @error('cccd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="qualification" class="form-label">Trình độ</label>
                            <select class="form-select @error('qualification') is-invalid @enderror" id="qualification"
                                name="qualification" required>
                                <option value="">Chọn trình độ</option>
                                <option value="Cao đẳng" {{ old('qualification') == 'Cao đẳng' ? 'selected' : '' }}>Cao
                                    đẳng</option>
                                <option value="Đại học" {{ old('qualification') == 'Đại học' ? 'selected' : '' }}>Đại học
                                </option>
                            </select>
                            @error('qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Phòng ban</label>
                            <select class="form-select @error('department_id') is-invalid @enderror" id="department_id"
                                name="department_id" required>
                                <option value="">Chọn phòng ban</option>
                                @if (!empty($departments) && is_array($departments))
                                    @foreach ($departments as $department)
                                        <option value="{{ $department['id'] ?? $department->id }}">
                                            {{ $department['name'] ?? $department->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="position_id" class="form-label">Chức vụ</label>
                            <select class="form-select @error('position_id') is-invalid @enderror" id="position_id"
                                name="position_id" required>
                                <option value="">Chọn chức vụ</option>
                                @if (!empty($positions) && is_array($positions))
                                    @foreach ($positions as $position)
                                        <option value="{{ $position['id'] ?? $position->id }}">
                                            {{ $position['name'] ?? $position->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('position_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                id="username" name="username" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-times"></i> Hủy</a>
                            <button type="submit" class="btn btn-success px-4"><i class="fas fa-save"></i> Thêm nhân
                                viên</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
