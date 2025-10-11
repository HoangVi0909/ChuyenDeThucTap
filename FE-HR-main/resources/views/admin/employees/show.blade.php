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
                <div class="text-center mt-4">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary px-4 py-2"><i
                            class="fas fa-arrow-left me-1"></i>Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>
@endsection