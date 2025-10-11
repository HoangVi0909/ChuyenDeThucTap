@extends('layouts.admin')
@section('content')

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i> Cập nhật nhân viên</h4>
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
                    <form action="{{ route('admin.employees.update', $employee['id'] ?? $employee->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên nhân viên</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $employee['name'] ?? ($employee->name ?? '')) }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Giới tính</label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">Chọn giới tính</option>
                                <option value="Nam" {{ old('gender', $employee['gender'] ?? ($employee->gender ?? '')) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gender', $employee['gender'] ?? ($employee->gender ?? '')) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('gender', $employee['gender'] ?? ($employee->gender ?? '')) == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee['email'] ?? ($employee->email ?? '')) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo"
                                name="photo">
                            @if (!empty($employee['photo']) || !empty($employee->photo))
                                <img src="{{ asset('storage/' . ($employee['photo'] ?? $employee->photo)) }}"
                                    alt="Ảnh nhân viên" width="100" class="mt-2 rounded shadow">
                            @endif
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                id="birth_date" name="birth_date"
                                value="{{ old('birth_date', $employee['birth_date'] ?? ($employee->birth_date ?? '')) }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="cccd" class="form-label">CCCD</label>
                            <input type="text" class="form-control @error('cccd') is-invalid @enderror" id="cccd"
                                name="cccd" value="{{ old('cccd', $employee['cccd'] ?? ($employee->cccd ?? '')) }}"
                                required>
                            @error('cccd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="qualification" class="form-label">Trình độ</label>
                            <select class="form-select @error('qualification') is-invalid @enderror" id="qualification" name="qualification" required>
                                <option value="">Chọn trình độ</option>
                                <option value="Cao đẳng" {{ old('qualification', $employee['qualification'] ?? ($employee->qualification ?? '')) == 'Cao đẳng' ? 'selected' : '' }}>Cao đẳng</option>
                                <option value="Đại học" {{ old('qualification', $employee['qualification'] ?? ($employee->qualification ?? '')) == 'Đại học' ? 'selected' : '' }}>Đại học</option>
                            </select>
                            @error('qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $employee['phone'] ?? ($employee->phone ?? '')) }}">
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
                                        <option value="{{ $department['id'] ?? $department->id }}"
                                            @if (($employee['department_id'] ?? ($employee->department_id ?? '')) == ($department['id'] ?? $department->id)) selected @endif>
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
                                        <option value="{{ $position['id'] ?? $position->id }}"
                                            @if (($employee['position_id'] ?? ($employee->position_id ?? '')) == ($position['id'] ?? $position->id)) selected @endif>
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
                                id="username" name="username"
                                value="{{ old('username', $employee['username'] ?? ($employee->username ?? '')) }}"
                                required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Nhập mật khẩu mới">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-times"></i> Hủy</a>
                            <button type="submit" class="btn btn-warning px-4"><i class="fas fa-save"></i> Cập
                                nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
