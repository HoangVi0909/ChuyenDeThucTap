@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Chỉnh sửa lịch làm việc</h4>
                        <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6>Có lỗi xảy ra:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.work-schedules.update', $workSchedule['id']) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Nhân viên <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id"
                                    name="employee_id" required>
                                    <option value="">-- Chọn nhân viên --</option>
                                    @if (isset($employees) && $employees->count() > 0)
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee['id'] }}"
                                                {{ old('employee_id', $workSchedule['employee_id'] ?? '') == $employee['id'] ? 'selected' : '' }}>
                                                {{ $employee['employee_code'] ?? 'N/A' }} - {{ $employee['name'] ?? 'N/A' }}
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

                            <div class="mb-3">
                                <label for="work_date" class="form-label">Ngày làm việc <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('work_date') is-invalid @enderror"
                                    id="work_date" name="work_date"
                                    value="{{ old('work_date', isset($workSchedule['work_date']) ? date('Y-m-d', strtotime($workSchedule['work_date'])) : '') }}" required>
                                @error('work_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="shift" class="form-label">Ca làm việc <span
                                        class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input @error('shift') is-invalid @enderror"
                                                type="radio" name="shift" id="shift_s" value="S"
                                                {{ old('shift', $workSchedule['shift'] ?? '') == 'S' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="shift_s">
                                                <strong>Ca sáng (S)</strong>
                                                <br><small class="text-muted">8:00 - 17:00</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input @error('shift') is-invalid @enderror"
                                                type="radio" name="shift" id="shift_c" value="C"
                                                {{ old('shift', $workSchedule['shift'] ?? '') == 'C' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="shift_c">
                                                <strong>Ca chiều (C)</strong>
                                                <br><small class="text-muted">14:00 - 23:00</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('shift')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Lưu ý:</h6>
                                <ul class="mb-0">
                                    <li>Mỗi nhân viên chỉ có thể có một ca làm việc trong một ngày</li>
                                    <li>Ca sáng: 8:00 - 17:00 (8 tiếng)</li>
                                    <li>Ca chiều: 14:00 - 23:00 (9 tiếng)</li>
                                </ul>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Cập nhật lịch làm việc
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Đảm bảo input date có giá trị đúng định dạng
    const workDateInput = document.getElementById('work_date');
    if (workDateInput && workDateInput.value) {
        // Kiểm tra và format lại nếu cần
        const currentValue = workDateInput.value;
        try {
            const date = new Date(currentValue);
            if (!isNaN(date.getTime())) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                workDateInput.value = `${year}-${month}-${day}`;
            }
        } catch (e) {
            console.log('Date format error:', e);
        }
    }
});
</script>
@endsection
