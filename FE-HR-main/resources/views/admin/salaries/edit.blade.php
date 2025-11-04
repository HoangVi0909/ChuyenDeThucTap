@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-primary">
                <i class="fas fa-edit me-2"></i>Chỉnh sửa lương
            </h1>
            <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        {{-- Debug Information --}}
        @if (config('app.debug'))
            <div class="alert alert-info">
                <strong>Debug Info:</strong><br>
                Salary type: {{ gettype($salary ?? 'undefined') }}<br>
                Salary has id: {{ isset($salary['id']) ? 'Yes' : 'No' }}<br>
                @if (isset($salary) && is_array($salary))
                    Salary keys: {{ implode(', ', array_keys($salary)) }}<br>
                @endif
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('admin.salaries.update', $salary['id'] ?? 0) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Nhân viên</label>
                                <select class="form-select" id="employee_id" name="employee_id" required>
                                    @if (isset($employees) && is_array($employees))
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee['id'] ?? '' }}"
                                                {{ ($salary['employee_id'] ?? '') == ($employee['id'] ?? '') ? 'selected' : '' }}>
                                                {{ $employee['name'] ?? 'N/A' }} -
                                                {{ $employee['department']['name'] ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="month_year" class="form-label">Tháng/Năm</label>
                                <input type="month" class="form-control" id="month_year" name="month_year"
                                    value="{{ $salary['month_year'] ?? '' }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_hours" class="form-label">Tổng giờ làm</label>
                                <input type="number" step="0.1" class="form-control" id="total_hours"
                                    name="total_hours" value="{{ $salary['total_hours'] ?? 0 }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hourly_rate" class="form-label">Lương theo giờ</label>
                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate"
                                    value="{{ $salary['hourly_rate'] ?? 0 }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="position_allowance" class="form-label">Phụ cấp</label>
                                <input type="number" step="0.01" class="form-control" id="position_allowance"
                                    name="position_allowance" value="{{ $salary['position_allowance'] ?? 0 }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="bonus" class="form-label">Thưởng</label>
                                <input type="number" step="0.01" class="form-control" id="bonus" name="bonus"
                                    value="{{ $salary['bonus'] ?? 0 }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="penalty" class="form-label">Phạt</label>
                                <input type="number" step="0.01" class="form-control" id="penalty" name="penalty"
                                    value="{{ $salary['penalty'] ?? 0 }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" {{ ($salary['status'] ?? '') == 'draft' ? 'selected' : '' }}>Chờ
                                        duyệt</option>
                                    <option value="approved"
                                        {{ ($salary['status'] ?? '') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                    <option value="paid" {{ ($salary['status'] ?? '') == 'paid' ? 'selected' : '' }}>Đã
                                        trả</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ $salary['notes'] ?? '' }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
