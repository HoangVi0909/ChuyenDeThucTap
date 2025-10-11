@extends('layouts.admin')

@section('content')

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i> Thêm lịch làm việc mới</h4>
                    {{-- <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a> --}}
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
                    <form action="{{ route('admin.work-schedules.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Nhân viên <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id"
                                name="employee_id" required>
                                <option value="">-- Chọn nhân viên --</option>
                                @if (isset($employees) && $employees->count() > 0)
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee['id'] }}"
                                            {{ old('employee_id') == $employee['id'] ? 'selected' : '' }}>
                                            {{ $employee['username'] ?? 'N/A' }} - {{ $employee['name'] ?? 'N/A' }}
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
                                id="work_date" name="work_date" value="{{ old('work_date', date('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}" required>
                            @error('work_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Chỉ có thể chọn ngày từ hôm nay trở đi</div>
                        </div>
                        <div class="mb-3">
                            <label for="shift" class="form-label">Ca làm việc <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input @error('shift') is-invalid @enderror" type="radio"
                                            name="shift" id="shift_s" value="S"
                                            {{ old('shift') == 'S' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="shift_s">
                                            <strong>Ca sáng (S)</strong>
                                            <br><small class="text-muted">8:00 - 17:00</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input @error('shift') is-invalid @enderror" type="radio"
                                            name="shift" id="shift_c" value="C"
                                            {{ old('shift') == 'C' ? 'checked' : '' }} required>
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
                                <li>Không thể tạo lịch cho ngày đã qua</li>
                            </ul>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-times"></i> Hủy</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save"></i> Lưu lịch làm
                                việc</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validate weekend selection
            const workDateInput = document.getElementById('work_date');
            workDateInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const dayOfWeek = selectedDate.getDay(); // 0 = Sunday, 6 = Saturday
                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    if (confirm(
                            'Bạn đã chọn ngày cuối tuần. Bạn có chắc chắn muốn tạo lịch làm việc cho ngày này?'
                        )) {
                        // User confirmed, do nothing
                    } else {
                        this.value = '';
                    }
                }
            });
        });
    </script>
@endsection
