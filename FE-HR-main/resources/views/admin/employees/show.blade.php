@extends('layouts.admin')
@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column align-items-center mb-4">
                            <div class="mb-3">
                                @if (!empty($employee['photo_url']))
                                    <img src="{{ $employee['photo_url'] }}" alt="Ảnh nhân viên"
                                        class="rounded-circle border shadow" width="120" height="120">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                        style="width:120px;height:120px;font-size:3rem;color:#fff;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <h3 class="fw-bold text-primary mb-1">
                                <i class="fas fa-user-circle me-2"></i>{{ $employee['name'] ?? ($employee->name ?? '') }}
                            </h3>
                            <div class="mb-2">
                                <span class="badge bg-info me-1"><i
                                        class="fas fa-building me-1"></i>{{ $employee['department']['name'] ?? ($employee->department->name ?? '') }}</span>
                                <span class="badge bg-warning text-dark"><i
                                        class="fas fa-briefcase me-1"></i>{{ $employee['position']['name'] ?? ($employee->position->name ?? '') }}</span>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p><i class="fas fa-calendar me-2 text-secondary"></i><strong>Ngày sinh:</strong>
                                    {{ $employee['birth_date'] ?? ($employee->birth_date ?? '') }}</p>
                                <p><i class="fas fa-id-card me-2 text-secondary"></i><strong>CCCD:</strong>
                                    {{ $employee['cccd'] ?? ($employee->cccd ?? '') }}</p>
                                <p><i class="fas fa-venus-mars me-2 text-secondary"></i><strong>Giới tính:</strong>
                                    {{ $employee['gender'] ?? ($employee->gender ?? '') }}</p>
                                <p><i class="fas fa-envelope me-2 text-secondary"></i><strong>Email:</strong>
                                    {{ $employee['email'] ?? ($employee->email ?? '') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><i class="fas fa-graduation-cap me-2 text-secondary"></i><strong>Trình độ:</strong>
                                    {{ $employee['qualification'] ?? ($employee->qualification ?? '') }}</p>
                                <p><i class="fas fa-phone me-2 text-secondary"></i><strong>Số điện thoại:</strong>
                                    {{ $employee['phone'] ?? ($employee->phone ?? '') }}</p>
                                <p><i class="fas fa-user-tag me-2 text-secondary"></i><strong>Tên đăng nhập:</strong>
                                    {{ $employee['username'] ?? ($employee->username ?? '') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lịch sử lương --}}
                <div class="card shadow-lg border-0 mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Lịch sử lương</h5>
                    </div>
                    <div class="card-body">
                        @if (isset($salaries) && count($salaries) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tháng/Năm</th>
                                            <th>Tổng giờ</th>
                                            <th>Lương theo giờ</th>
                                            <th>Lương cơ bản</th>
                                            <th>Phụ cấp</th>
                                            <th>Thưởng</th>
                                            <th>Phạt</th>
                                            <th>Tổng lương</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salaries as $salary)
                                            <tr>
                                                <td>
                                                    <strong>{{ $salary['month_year'] ?? 'N/A' }}</strong>
                                                </td>
                                                <td>{{ number_format($salary['total_hours'] ?? 0, 1) }} giờ</td>
                                                <td>{{ number_format($salary['hourly_rate'] ?? 0) }} VNĐ</td>
                                                <td>{{ number_format($salary['base_salary'] ?? 0) }} VNĐ</td>
                                                <td>{{ number_format($salary['position_allowance'] ?? 0) }} VNĐ</td>
                                                <td class="text-success">{{ number_format($salary['bonus'] ?? 0) }} VNĐ
                                                </td>
                                                <td class="text-danger">{{ number_format($salary['penalty'] ?? 0) }} VNĐ
                                                </td>
                                                <td>
                                                    <strong
                                                        class="text-primary">{{ number_format($salary['total_salary'] ?? 0) }}
                                                        VNĐ</strong>
                                                </td>
                                                <td>
                                                    @php
                                                        $status = $salary['status'] ?? 'draft';
                                                        $statusClass =
                                                            [
                                                                'draft' => 'warning',
                                                                'approved' => 'success',
                                                                'paid' => 'primary',
                                                            ][$status] ?? 'secondary';

                                                        $statusText =
                                                            [
                                                                'draft' => 'Chờ duyệt',
                                                                'approved' => 'Đã duyệt',
                                                                'paid' => 'Đã trả',
                                                            ][$status] ?? $status;
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Thống kê tổng quan --}}
                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h6 class="text-muted">Số tháng có lương</h6>
                                        <h4 class="text-primary">{{ count($salaries) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h6 class="text-muted">Tổng lương đã nhận</h6>
                                        <h4 class="text-success">
                                            {{ number_format(collect($salaries)->where('status', 'paid')->sum('total_salary')) }}
                                            VNĐ</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h6 class="text-muted">Lương trung bình</h6>
                                        <h4 class="text-info">{{ number_format(collect($salaries)->avg('total_salary')) }}
                                            VNĐ</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h6 class="text-muted">Lương cao nhất</h6>
                                        <h4 class="text-warning">
                                            {{ number_format(collect($salaries)->max('total_salary')) }} VNĐ</h4>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Chưa có dữ liệu lương</h5>
                                <p class="text-muted">Nhân viên này chưa có bản ghi lương nào.</p>
                                <a href="{{ route('admin.salaries.create') }}?employee_id={{ $employee['id'] ?? '' }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Tạo lương mới
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary px-4 py-2"><i
                            class="fas fa-arrow-left me-1"></i>Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>
@endsection
