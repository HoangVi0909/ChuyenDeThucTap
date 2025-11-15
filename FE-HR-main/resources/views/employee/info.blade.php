@extends('layouts.employee')
@section('content')
    <div class="container py-4">
        <div class="card profile-card p-4 mx-auto" style="max-width: 600px;">
            <div class="d-flex align-items-center mb-4">
                <div>
                    <h4 class="mb-1">{{ $employee['name'] ?? '' }}</h4>
                    <div class="text-muted">Tên đăng nhập: {{ $employee['username'] ?? '' }}</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-6">
                    <div><span class="info-label">Giới tính:</span> <span
                            class="info-value">{{ $employee['gender'] ?? '' }}</span></div>
                    <div><span class="info-label">Email:</span> <span
                            class="info-value">{{ $employee['email'] ?? '' }}</span></div>
                    <div><span class="info-label">Ngày sinh:</span> <span
                            class="info-value">{{ $employee['birth_date'] ?? '' }}</span></div>
                    <div><span class="info-label">Số điện thoại:</span> <span
                            class="info-value">{{ $employee['phone'] ?? '' }}</span></div>
                </div>
                <div class="col-6">
                    <div><span class="info-label">Phòng ban:</span> <span
                            class="info-value">{{ $employee['department']['name'] ?? '' }}</span></div>
                    <div><span class="info-label">Chức vụ:</span> <span
                            class="info-value">{{ $employee['position']['name'] ?? '' }}</span></div>
                    <div><span class="info-label">Trình độ:</span> <span
                            class="info-value">{{ $employee['qualification'] ?? '' }}</span></div>
                </div>
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary"><i
                        class="fas fa-arrow-left me-1"></i> Quay lại</a>

            </div>
        </div>
    </div>
@endsection