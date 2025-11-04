@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-primary">
                <i class="fas fa-users me-2"></i>Quản lý nhân viên
            </h1>
            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary shadow">
                <i class="fas fa-plus me-1"></i>Thêm nhân viên
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Bộ lọc --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-1"></i>Bộ lọc</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.employees.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Tên, email, CCCD, mã NV...">
                    </div>
                    <div class="col-md-3">
                        <label for="department" class="form-label">Phòng ban</label>
                        <select class="form-select" id="department" name="department_id">
                            <option value="">Tất cả phòng ban</option>
                            @if (isset($departments) && is_array($departments))
                                @foreach ($departments as $department)
                                    <option value="{{ $department['id'] }}"
                                        {{ request('department_id') == $department['id'] ? 'selected' : '' }}>
                                        {{ $department['name'] }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="position" class="form-label">Vị trí</label>
                        <select class="form-select" id="position" name="position_id">
                            <option value="">Tất cả vị trí</option>
                            @if (isset($positions) && is_array($positions))
                                @foreach ($positions as $position)
                                    <option value="{{ $position['id'] }}"
                                        {{ request('position_id') == $position['id'] ? 'selected' : '' }}>
                                        {{ $position['name'] }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">Tất cả</option>
                            <option value="Nam" {{ request('gender') == 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ request('gender') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i>Lọc
                        </button>
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt me-1"></i>Làm mới
                        </a>
                    </div>
                </form>
            </div>
        </div>
        {{-- <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-2 bg-gradient-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1 fw-bold">Tổng nhân viên</div>
                            <div class="h4 mb-0 fw-bold">
                                @isset($employees)
                                {{ is_array($employees) ? count($employees) : 0 }}
                                @else
                                0
                                @endisset
                            </div>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 fw-bold"><i class="fas fa-list me-1"></i>Danh sách nhân viên</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" id="dataTable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
                                <th>Tên nhân viên</th>
                                <th>Giới tính</th>
                                <th>Email</th>
                                <th>CCCD</th>
                                <th>Điện thoại</th>
                                <th>Phòng ban</th>
                                <th>Chức vụ</th>
                                <th>Ngày sinh</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($employees) && is_array($employees) && count($employees) > 0)
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td class="fw-bold text-primary">#{{ $employee['id'] ?? 'N/A' }}</td>
                                        <td>
                                            @if (isset($employee['photo_url']) && $employee['photo_url'])
                                                <img src="{{ $employee['photo_url'] }}" alt="Ảnh nhân viên" width="40"
                                                    class="rounded-circle border">
                                            @else
                                                <span class="badge bg-secondary">No Image</span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $employee['name'] ?? '' }}</td>
                                        <td>{{ $employee['gender'] ?? '' }}</td>
                                        <td>{{ $employee['email'] ?? '' }}</td>
                                        <td>{{ $employee['cccd'] ?? '' }}</td>
                                        <td>{{ $employee['phone'] ?? '' }}</td>
                                        <td><span class="badge bg-info">{{ $employee['department']['name'] ?? '' }}</span>
                                        </td>
                                        <td><span
                                                class="badge bg-warning text-dark">{{ $employee['position']['name'] ?? '' }}</span>
                                        </td>
                                        <td>{{ $employee['birth_date'] ?? '' }}</td>
                                        <td>
                                            <a href="{{ route('admin.employees.show', $employee['id']) }}"
                                                class="btn btn-info btn-sm" title="Xem chi tiết"><i
                                                    class="fas fa-eye"></i></a>
                                            <a href="{{ route('admin.employees.edit', $employee['id']) }}"
                                                class="btn btn-warning btn-sm" title="Chỉnh sửa"><i
                                                    class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.employees.destroy', $employee['id']) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Xóa"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center">Không có dữ liệu nhân viên</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;
        }
    </style>
@endsection
