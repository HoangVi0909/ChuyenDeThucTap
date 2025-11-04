@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-primary">
                <i class="fas fa-money-bill-wave me-2"></i>Quản lý lương
            </h1>
            <div>
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#calculateModal">
                    <i class="fas fa-calculator me-1"></i>Tính lương tháng
                </button>
                <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Thêm bản ghi
                </a>
            </div>
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


        {{-- Filters --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-1"></i>Bộ lọc</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.salaries.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="employee_id" class="form-label">Nhân viên</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">Tất cả nhân viên</option>
                            @if (isset($employees) && is_array($employees))
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee['id'] ?? '' }}"
                                        {{ request('employee_id') == ($employee['id'] ?? '') ? 'selected' : '' }}>
                                        {{ $employee['name'] ?? 'N/A' }} - {{ $employee['department']['name'] ?? 'N/A' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="month" class="form-label">Tháng</label>
                        <input type="month" class="form-control" id="month" name="month"
                            value="{{ request('month') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt
                            </option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã trả</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i>Lọc
                        </button>
                        <a href="{{ route('admin.salaries.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt me-1"></i>Làm mới
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Salary Table --}}
        <div class="card shadow">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 fw-bold"><i class="fas fa-table me-1"></i>Danh sách lương</h6>
            </div>
            <div class="card-body">
                @if (isset($salaries) && is_array($salaries) && count($salaries) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nhân viên</th>
                                    <th>Phòng ban</th>
                                    <th>Tháng</th>
                                    <th>Giờ làm</th>
                                    <th>Lương cơ bản</th>
                                    <th>Phụ cấp</th>
                                    <th>Thưởng</th>
                                    <th>Phạt</th>
                                    <th>Tổng lương (trước thuế)</th>
                                    <th>Thuế (5%)</th>
                                    <th>Thực lĩnh</th>
                                    <th>Trạng thái</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salaries as $salary)
                                    <tr>
                                        <td>{{ $salary['employee']['name'] ?? 'N/A' }}</td>
                                        <td>{{ $salary['employee']['department']['name'] ?? 'N/A' }}</td>
                                        <td>{{ $salary['month_year'] ?? 'N/A' }}</td>
                                        <td>{{ number_format($salary['total_hours'] ?? 0, 1) }} giờ</td>
                                        <td>
                                            {{ number_format($salary['base_salary'] ?? 0) }} đ
                                            @if (config('app.debug'))
                                                <br><small class="text-muted">
                                                    ({{ number_format($salary['total_hours'] ?? 0, 1) }}h ×
                                                    {{ number_format($salary['hourly_rate'] ?? 0) }})
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ number_format($salary['position_allowance'] ?? 0) }} đ</td>
                                        <td>{{ number_format($salary['bonus'] ?? 0) }} đ</td>
                                        <td>{{ number_format($salary['penalty'] ?? 0) }} đ</td>
                                        @php
                                            $gross =
                                                ($salary['base_salary'] ?? 0) +
                                                ($salary['position_allowance'] ?? 0) +
                                                ($salary['bonus'] ?? 0) -
                                                ($salary['penalty'] ?? 0);
                                            $tax = $salary['tax_amount'] ?? 0;
                                            $net = $gross - $tax;
                                        @endphp
                                        <td><strong>{{ number_format($gross) }} đ</strong></td>
                                        <td class="text-danger">{{ number_format($tax) }} đ</td>
                                        <td class="text-success">{{ number_format($net) }} đ</td>
                                        <td>
                                            @switch($salary['status'] ?? 'draft')
                                                @case('draft')
                                                    <span class="badge bg-warning">Chờ duyệt</span>
                                                @break

                                                @case('approved')
                                                    <span class="badge bg-success">Đã duyệt</span>
                                                @break

                                                @case('paid')
                                                    <span class="badge bg-info">Đã trả</span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary">N/A</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.salaries.show', $salary['id'] ?? '#') }}"
                                                    class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.salaries.edit', $salary['id'] ?? '#') }}"
                                                    class="btn btn-sm btn-primary" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if (isset($salary['id']))
                                                    <form method="POST"
                                                        action="{{ route('admin.salaries.destroy', $salary['id']) }}"
                                                        class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có dữ liệu lương</h5>
                        <p class="text-muted">Nhấn "Tính lương tháng" để tạo bản ghi lương mới</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Calculate Salary Modal --}}
    <div class="modal fade" id="calculateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calculator"></i> Tính lương tháng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.salaries.calculate') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="calculateMonth" class="form-label">Tháng/Năm</label>
                            <input type="month" class="form-control" id="calculateMonth" name="month"
                                value="{{ date('Y-m') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="calculateEmployee" class="form-label">Nhân viên (tùy chọn)</label>
                            <select class="form-select" id="calculateEmployee" name="employee_id">
                                <option value="">Tất cả nhân viên</option>
                                @if (isset($employees) && is_array($employees))
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee['id'] ?? '' }}">
                                            {{ $employee['name'] ?? 'N/A' }} -
                                            {{ $employee['department']['name'] ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="form-text">Để trống để tính lương cho tất cả nhân viên</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-calculator"></i> Tính lương
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function recalculateSalary(salaryId) {
            if (confirm('Bạn có muốn tính lại lương cho bản ghi này không?')) {
                // Show loading
                const button = event.target.closest('button');
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

                // Make AJAX request
                fetch(`{{ config('services.backend_api.url') }}/api/admin/salary-management/${salaryId}/recalculate`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer {{ session('admin_token') }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        button.innerHTML = originalContent;
                        button.disabled = false;

                        if (data.success) {
                            alert('Tính lại lương thành công!');
                            location.reload(); // Reload page to show updated data
                        } else {
                            alert('Lỗi: ' + (data.message || 'Không thể tính lại lương'));
                        }
                    })
                    .catch(error => {
                        button.innerHTML = originalContent;
                        button.disabled = false;
                        console.error('Error:', error);
                        alert('Lỗi kết nối: ' + error.message);
                    });
            }
        }
    </script>
@endsection
