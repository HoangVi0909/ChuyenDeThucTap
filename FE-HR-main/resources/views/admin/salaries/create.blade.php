@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Thêm lương mới</h4>
                        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
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

                        <form action="{{ route('admin.salaries.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Nhân viên <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id"
                                    name="employee_id" required>
                                    <option value="">-- Chọn nhân viên --</option>
                                    @if (isset($employees) && $employees->count() > 0)
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee['id'] }}" {{ old('employee_id') == $employee['id'] ? 'selected' : '' }}>
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
                                <label for="amount" class="form-label">Số tiền lương <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                        id="amount" name="amount" value="{{ old('amount') }}" min="0" step="1000"
                                        placeholder="Nhập số tiền lương" required>
                                    <span class="input-group-text">VNĐ</span>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Ví dụ: 5000000 (5 triệu VNĐ)</div>
                            </div>

                            <div class="mb-3">
                                <label for="month" class="form-label">Tháng <span class="text-danger">*</span></label>
                                <input type="month" class="form-control @error('month') is-invalid @enderror" id="month"
                                    name="month" value="{{ old('month', date('Y-m')) }}" required>
                                @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Ghi chú</label>
                                <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note"
                                    rows="3" placeholder="Nhập ghi chú (không bắt buộc)">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Tối đa 1000 ký tự</div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu lương
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<div class="alert alert-info">
    <strong>Debug:</strong>
    Employees count: {{ isset($employees) ? $employees->count() : 'Variable not set' }}
    <br>Employees type: {{ isset($employees) ? get_class($employees) : 'Variable not set' }}
    @if (isset($employees) && $employees->count() > 0)
        <br>First employee: {{ $employees->first()['name'] ?? 'No name' }}
        <br>First employee ID: {{ $employees->first()['id'] ?? 'No ID' }}
    @endif
</div>

<!-- Form -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin lương</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.salaries.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="employee_id" class="form-label">Nhân viên</label>
                <select name="employee_id" id="employee_id" class="form-select" required>
                    <option value="">-- Chọn nhân viên --</option>
                    @if (isset($employees) && $employees->count() > 0)
                        @foreach ($employees as $employee)
                            <option value="{{ $employee['id'] }}">
                                {{ $employee['name'] }} - {{ $employee['department']['name'] ?? 'N/A' }}
                            </option>
                        @endforeach
                    @else
                        <option value="">Không có nhân viên</option>
                    @endif
                </select>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Số tiền (VNĐ)</label>
                <input type="number" name="amount" id="amount" class="form-control" min="0" step="1"
                    placeholder="Nhập số tiền lương" required>
                <small class="text-muted">Ví dụ: 5000000 (5 triệu VNĐ)</small>
            </div>

            <div class="mb-3">
                <label for="month" class="form-label">Tháng</label>
                <input type="month" name="month" id="month" class="form-control" value="{{ date('Y-m') }}" required>
            </div>

            <div class="mb-3">
                <label for="note" class="form-label">Ghi chú</label>
                <textarea name="note" id="note" class="form-control" rows="3"
                    placeholder="Ghi chú thêm về lương tháng này..."></textarea>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary me-md-2">
                    <i class="fas fa-times me-1"></i>Hủy
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Thêm lương
                </button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
    // Format số tiền khi nhập
    document.getElementById('amount').addEventListener('input', function (e) {
        const value = e.target.value;
        if (value) {
            const formatted = new Intl.NumberFormat('vi-VN').format(value);
            e.target.setAttribute('title', formatted + ' VNĐ');
        }
    });
</script>
@endsection