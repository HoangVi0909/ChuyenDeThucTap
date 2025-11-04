@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-tachometer-alt"></i> Dashboard - Hệ thống quản lý nhân sự</h4>
                    </div>
                    <div class="card-body">
                        <h5>Xin chào, {{ session('admin.name') ?? 'Admin' }}!</h5>
                        <p class="text-muted">Chào mừng bạn đến với hệ thống quản lý nhân sự. Hôm nay là {{ date('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <a href="{{ route('admin.employees.index') }}" class="text-decoration-none">
                    <div class="card text-white bg-primary mb-3 card-hover">
                        <div class="card-header">
                            <i class="fas fa-users"></i> Nhân viên
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">{{ $stats['employees_count'] ?? 0 }}</h3>
                            <p class="card-text">Tổng số nhân viên</p>
                            <small class="text-white-50">Đang hoạt động: {{ $stats['active_employees'] ?? 0 }}</small>
                        </div>
                        <div class="card-footer">
                            <small class="text-white text-decoration-underline">Xem chi tiết <i
                                    class="fas fa-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.departments.index') }}" class="text-decoration-none">
                    <div class="card text-white bg-success mb-3 card-hover">
                        <div class="card-header">
                            <i class="fas fa-building"></i> Phòng ban
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">{{ $stats['departments_count'] ?? 0 }}</h3>
                            <p class="card-text">Tổng số phòng ban</p>
                            <small class="text-white-50">Đang hoạt động</small>
                        </div>
                        <div class="card-footer">
                            <small class="text-white text-decoration-underline">Xem chi tiết <i
                                    class="fas fa-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.positions.index') }}" class="text-decoration-none">
                    <div class="card text-white bg-warning mb-3 card-hover">
                        <div class="card-header">
                            <i class="fas fa-briefcase"></i> Vị trí
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">{{ $stats['positions_count'] ?? 0 }}</h3>
                            <p class="card-text">Tổng số vị trí</p>
                            <small class="text-white-50">Các chức vụ</small>
                        </div>
                        <div class="card-footer">
                            <small class="text-white text-decoration-underline">Xem chi tiết <i
                                    class="fas fa-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.feedback.index') }}" class="text-decoration-none">
                    <div class="card text-white bg-info mb-3 card-hover">
                        <div class="card-header">
                            <i class="fas fa-comments"></i> Phản hồi
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">{{ $stats['notifications_count'] ?? 0 }}</h3>
                            <p class="card-text">Tổng phản hồi</p>
                            <small class="text-white-50">Từ nhân viên</small>
                        </div>
                        <div class="card-footer">
                            <small class="text-white text-decoration-underline">Xem chi tiết <i
                                    class="fas fa-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            <style>
                .card-hover {
                    transition: box-shadow 0.2s, transform 0.2s;
                }

                .card-hover:hover {
                    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
                    transform: translateY(-2px) scale(1.03);
                    cursor: pointer;
                }
            </style>
        </div>

        <!-- Additional Statistics + System Status -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-dark mb-3 h-100">
                    <div class="card-header">
                        <i class="fas fa-calendar"></i> Lịch làm việc
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ $stats['total_work_schedules'] ?? 0 }}</h3>
                        <p class="card-text">Lịch trình</p>
                        <small class="text-white-50">Đã lên lịch</small>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.work-schedules.index') }}" class="text-white text-decoration-none">
                            <small>Xem chi tiết <i class="fas fa-arrow-right"></i></small>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3 h-100">
                    <div class="card-header">
                        <i class="fas fa-bell"></i> Thông báo
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ $stats['total_notifications'] ?? 0 }}</h3>
                        <p class="card-text">Thông báo</p>
                        <small class="text-white-50">Hệ thống</small>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.notifications.index') }}" class="text-white text-decoration-none">
                            <small>Xem chi tiết <i class="fas fa-arrow-right"></i></small>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3 h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-pie"></i> Tổng quan
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">100%</h3>
                        <p class="card-text">Hoạt động</p>
                        <small class="text-white-50">Hệ thống ổn định</small>
                    </div>
                    <div class="card-footer">
                        <small class="text-white-50">Tất cả đều hoạt động tốt</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-header bg-dark text-white">
                        <h5><i class="fas fa-cogs"></i> Trạng thái hệ thống</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Dữ liệu
                                <span class="badge bg-success rounded-pill">Hoạt động</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                API Backend
                                <span class="badge bg-success rounded-pill">Chạy</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Xác thực
                                <span class="badge bg-success rounded-pill">Hoạt động</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Cập nhật lần cuối
                                <span class="badge bg-primary rounded-pill">{{ date('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--
        <!-- Data Tables Section -->
        <div class="row">
            <!-- Recent Employees -->
            @if (isset($stats['recent_employees']) && count($stats['recent_employees']) > 0)
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-users"></i> Nhân viên mới nhất</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Username</th>
                                        <th>Số điện thoại</th>
                                        <th>Phòng ban</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stats['recent_employees'] as $employee)
                                    <tr>
                                        <td>{{ $employee['name'] ?? 'N/A' }}</td>
                                        <td>{{ $employee['username'] ?? 'N/A' }}</td>
                                        <td>{{ $employee['phone'] ?? 'N/A' }}</td>
                                        <td>{{ $employee['department']['name'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Xem tất cả
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Departments with Employee Count -->
            @if (isset($stats['employees_by_department']) && count($stats['employees_by_department']) > 0)
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-building"></i> Phòng ban & Nhân viên</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Phòng ban</th>
                                        <th>Số nhân viên</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stats['employees_by_department'] as $dept)
                                    <tr>
                                        <td>{{ $dept['name'] ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $dept['employees_count'] ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Hoạt động</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-eye"></i> Xem tất cả
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Additional Information Rows -->
        <div class="row">
            <!-- Available Positions -->
            @if (isset($stats['positions_list']) && count($stats['positions_list']) > 0)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5><i class="fas fa-briefcase"></i> Danh sách vị trí</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach ($stats['positions_list'] as $position)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $position['name'] ?? 'N/A' }}
                                <span class="badge bg-warning rounded-pill">{{ $position['id'] ?? '' }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.positions.index') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-eye"></i> Xem tất cả
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Feedback -->
            @if (isset($stats['recent_feedback']) && count($stats['recent_feedback']) > 0)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-comments"></i> Phản hồi gần đây</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($stats['recent_feedback'] as $feedback)
                        <div class="border-bottom pb-2 mb-2">
                            <small class="text-muted">{{ $feedback['employee']['name'] ?? 'Unknown' }}</small>
                            <p class="mb-1">{{ Str::limit($feedback['content'] ?? 'No content', 50) }}</p>
                            <small class="text-muted">{{ date('d/m/Y', strtotime($feedback['created_at'] ?? now()))
                                }}</small>
                        </div>
                        @endforeach
                        <div class="text-center">
                            <a href="{{ route('admin.feedback.index') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Xem tất cả
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif --}}


        <!-- Quick Actions -->
        {{-- <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5><i class="fas fa-rocket"></i> Thao tác nhanh</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-block w-100">
                                        <i class="fas fa-user-plus"></i> Thêm nhân viên
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="{{ route('admin.departments.create') }}"
                                        class="btn btn-success btn-block w-100">
                                        <i class="fas fa-building"></i> Thêm phòng ban
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="{{ route('admin.positions.create') }}" class="btn btn-warning btn-block w-100">
                                        <i class="fas fa-briefcase"></i> Thêm vị trí
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="{{ route('admin.notifications.create') }}"
                                        class="btn btn-danger btn-block w-100">
                                        <i class="fas fa-bell"></i> Tạo thông báo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
    </div>
@endsection
