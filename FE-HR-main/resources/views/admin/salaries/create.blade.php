@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-primary">
                <i class="fas fa-plus me-2"></i>Thêm lương mới
            </h1>
            <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                {{-- Hiển thị lỗi đặc biệt cho trùng lặp --}}
                @if ($errors->has('duplicate'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Cảnh báo trùng lặp!</h6>
                        <p class="mb-0">{!! nl2br(e($errors->first('duplicate'))) !!}</p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Hiển thị lỗi khác --}}
                @if ($errors->any() && !$errors->has('duplicate'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6>Có lỗi xảy ra:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                @if (!str_contains($error, 'Đã tồn tại'))
                                    <li>{{ $error }}</li>
                                @endif
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.salaries.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Nhân viên <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id"
                                    name="employee_id" required>
                                    <option value="">-- Chọn nhân viên --</option>
                                    @if (isset($employees) && is_array($employees) && count($employees) > 0)
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee['id'] ?? '' }}"
                                                {{ old('employee_id') == ($employee['id'] ?? '') || (isset($selectedEmployeeId) && $selectedEmployeeId == ($employee['id'] ?? '')) ? 'selected' : '' }}>
                                                {{ $employee['name'] ?? 'N/A' }} -
                                                {{ $employee['department']['name'] ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Không có nhân viên nào</option>
                                    @endif
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="month_year" class="form-label">Tháng/Năm <span
                                        class="text-danger">*</span></label>
                                <input type="month" class="form-control @error('month_year') is-invalid @enderror"
                                    id="month_year" name="month_year" value="{{ old('month_year', date('Y-m')) }}" required>
                                @error('month_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="duplicate-warning" class="alert alert-warning mt-2" style="display: none;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span id="duplicate-message"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_hours" class="form-label">Tổng giờ làm</label>
                                <input type="number" step="0.1"
                                    class="form-control @error('total_hours') is-invalid @enderror" id="total_hours"
                                    name="total_hours" value="{{ old('total_hours', 0) }}" min="0">
                                @error('total_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hourly_rate" class="form-label">Lương theo giờ (VNĐ/giờ)</label>
                                <input type="number" class="form-control @error('hourly_rate') is-invalid @enderror"
                                    id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', 0) }}" min="0">
                                @error('hourly_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Salary Preview --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-calculator me-2"></i>Tính toán lương:</h6>
                                <div id="salary-calculation">
                                    <span id="basic-salary-display">Lương cơ bản: 0 VNĐ</span><br>
                                    <span id="total-salary-display">Tổng lương: 0 VNĐ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="position_allowance" class="form-label">Phụ cấp (VNĐ)</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('position_allowance') is-invalid @enderror"
                                    id="position_allowance" name="position_allowance"
                                    value="{{ old('position_allowance', 0) }}" min="0">
                                @error('position_allowance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="bonus" class="form-label">Thưởng (VNĐ)</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('bonus') is-invalid @enderror" id="bonus"
                                    name="bonus" value="{{ old('bonus', 0) }}" min="0">
                                @error('bonus')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="penalty" class="form-label">Phạt (VNĐ)</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('penalty') is-invalid @enderror" id="penalty"
                                    name="penalty" value="{{ old('penalty', 0) }}" min="0">
                                @error('penalty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status">
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Chờ
                                        duyệt</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Đã duyệt
                                    </option>
                                    <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Đã trả</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                            placeholder="Nhập ghi chú (tùy chọn)">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Lưu lương
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Real-time calculation script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('employee_id');
            const monthYearInput = document.getElementById('month_year');
            const totalHoursInput = document.getElementById('total_hours');
            const hourlyRateInput = document.getElementById('hourly_rate');
            const positionAllowanceInput = document.getElementById('position_allowance');
            const bonusInput = document.getElementById('bonus');
            const penaltyInput = document.getElementById('penalty');
            const basicSalaryDisplay = document.getElementById('basic-salary-display');
            const totalSalaryDisplay = document.getElementById('total-salary-display');
            const duplicateWarning = document.getElementById('duplicate-warning');
            const duplicateMessage = document.getElementById('duplicate-message');
            const submitButton = document.querySelector('button[type="submit"]');

            function calculateSalary() {
                const hours = parseFloat(totalHoursInput.value) || 0;
                const rate = parseFloat(hourlyRateInput.value) || 0;
                const allowance = parseFloat(positionAllowanceInput.value) || 0;
                const bonus = parseFloat(bonusInput.value) || 0;
                const penalty = parseFloat(penaltyInput.value) || 0;

                const basicSalary = hours * rate;
                const totalSalary = basicSalary + allowance + bonus - penalty;

                basicSalaryDisplay.textContent =
                    `Lương cơ bản: ${formatCurrency(basicSalary)} VNĐ (${hours} giờ × ${formatCurrency(rate)} VNĐ/giờ)`;
                totalSalaryDisplay.textContent =
                    `Tổng lương: ${formatCurrency(totalSalary)} VNĐ (Cơ bản: ${formatCurrency(basicSalary)} + Phụ cấp: ${formatCurrency(allowance)} + Thưởng: ${formatCurrency(bonus)} - Phạt: ${formatCurrency(penalty)})`;
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount);
            }

            function checkDuplicateSalary() {
                const employeeId = employeeSelect.value;
                const monthYear = monthYearInput.value;

                if (!employeeId || !monthYear) {
                    duplicateWarning.style.display = 'none';
                    submitButton.disabled = false;
                    return;
                }

                // Gọi API kiểm tra trùng lặp
                fetch(`{{ config('services.backend_api.url') }}/api/admin/salary-management?employee_id=${employeeId}&month_year=${monthYear}`, {
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer {{ session('admin_token') }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data && data.data.data && data.data.data.length > 0) {
                            // Đã tồn tại bảng lương
                            const existingRecord = data.data.data[0];
                            const employeeName = employeeSelect.options[employeeSelect.selectedIndex].text
                                .split(' - ')[1] || 'nhân viên này';

                            duplicateMessage.innerHTML = `
                            <strong>Cảnh báo:</strong> Đã tồn tại bảng lương cho <strong>${employeeName}</strong> trong tháng <strong>${monthYear}</strong>.<br>
                            <small>Tổng lương hiện tại: ${formatCurrency(existingRecord.total_salary)} VNĐ - Trạng thái: ${getStatusText(existingRecord.status)}</small>
                        `;
                            duplicateWarning.style.display = 'block';
                            submitButton.disabled = true;
                            submitButton.innerHTML =
                                '<i class="fas fa-exclamation-triangle me-1"></i>Không thể tạo (đã tồn tại)';
                        } else {
                            // Không có bảng lương trùng lặp
                            duplicateWarning.style.display = 'none';
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="fas fa-save me-1"></i>Lưu lương';
                        }
                    })
                    .catch(error => {
                        console.error('Error checking duplicate:', error);
                        duplicateWarning.style.display = 'none';
                        submitButton.disabled = false;
                    });
            }

            function getStatusText(status) {
                switch (status) {
                    case 'draft':
                        return 'Chờ duyệt';
                    case 'approved':
                        return 'Đã duyệt';
                    case 'paid':
                        return 'Đã trả';
                    default:
                        return status;
                }
            }

            // Attach event listeners
            [totalHoursInput, hourlyRateInput, positionAllowanceInput, bonusInput, penaltyInput].forEach(input => {
                input.addEventListener('input', calculateSalary);
            });

            // Kiểm tra trùng lặp khi thay đổi nhân viên hoặc tháng
            employeeSelect.addEventListener('change', checkDuplicateSalary);
            monthYearInput.addEventListener('change', checkDuplicateSalary);

            // Initial calculation and check
            calculateSalary();
            checkDuplicateSalary();
        });
    </script>
@endsection
