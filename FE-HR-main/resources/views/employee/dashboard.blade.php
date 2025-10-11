@extends('layouts.employee')
@section('content')
    <style>
        .profile-avatar {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #2575fc;
            box-shadow: 0 4px 24px 0 rgba(37, 117, 252, 0.12);
        }

        .profile-card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
            background: #fff;
        }

        .info-label {
            color: #6a11cb;
            font-weight: 600;
        }

        .info-value {
            color: #333;
        }

        .dashboard-badge {
            font-size: 1.2rem;
            border-radius: 1rem;
            padding: 0.3rem 1rem;
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px 0 rgba(37, 117, 252, 0.10);
        }

        .dashboard-card {
            border-radius: 1.2rem;
            transition: box-shadow 0.2s;
        }

        .dashboard-card:hover {
            box-shadow: 0 8px 32px 0 rgba(37, 117, 252, 0.18);
        }

        .dashboard-icon {
            font-size: 2rem;
            color: #2575fc;
        }
    </style>
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="card profile-card mb-3 p-3">
                    <div class="d-flex align-items-center">
                        <img src="{{ $employee['photo_url'] ?? asset('default.png') }}" class="profile-avatar me-4">
                        <div class="w-100">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div><span class="info-label"><i class="fas fa-id-badge me-1"></i> Tên đăng nhập:</span>
                                        <span class="info-value">{{ $employee['username'] ?? '' }}</span></div>
                                    <div><span class="info-label"><i class="fas fa-user me-1"></i> Họ tên:</span> <span
                                            class="info-value">{{ $employee['name'] ?? '' }}</span></div>
                                    <div><span class="info-label"><i class="fas fa-venus-mars me-1"></i> Giới tính:</span>
                                        <span class="info-value">{{ $employee['gender'] ?? '' }}</span>
                                    </div>
                                    <div><span class="info-label"><i class="fas fa-envelope me-1"></i> Email:</span>
                                        <span class="info-value">{{ $employee['email'] ?? '' }}</span>
                                    </div>
                                    <div><span class="info-label"><i class="fas fa-birthday-cake me-1"></i> Ngày
                                            sinh:</span> <span class="info-value">{{ $employee['birth_date'] ?? '' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div><span class="info-label"><i class="fas fa-building me-1"></i> Phòng ban:</span>
                                        <span class="info-value">{{ $employee['department']['name'] ?? '' }}</span>
                                    </div>
                                    <div><span class="info-label"><i class="fas fa-briefcase me-1"></i> Chức vụ:</span>
                                        <span class="info-value">{{ $employee['position']['name'] ?? '' }}</span>
                                    </div>
                                    <div><span class="info-label"><i class="fas fa-phone me-1"></i> Số điện thoại:</span>
                                        <span class="info-value">{{ $employee['phone'] ?? '' }}</span>
                                    </div>
                                    <div><span class="info-label"><i class="fas fa-graduation-cap me-1"></i> Trình
                                            độ:</span> <span
                                            class="info-value">{{ $employee['qualification'] ?? '' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row g-2">
                    <div class="col-12 mb-2">
                        <div class="card dashboard-card text-center p-3">
                            <div class="dashboard-icon mb-1"><i class="fas fa-bell"></i></div>
                            <div class="dashboard-badge mb-1">{{ $notifications_count ?? 0 }} Thông báo</div>
                            <a href="#" class="small text-primary" data-bs-toggle="modal"
                                data-bs-target="#allNotificationsModal"><i class="fas fa-eye"></i> Xem chi tiết</a>
                            <!-- Modal danh sách thông báo -->
                            <div class="modal fade" id="allNotificationsModal" tabindex="-1"
                                aria-labelledby="allNotificationsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="allNotificationsModalLabel">Danh sách thông báo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if (!empty($notifications))
                                                <ul class="list-group">
                                                    @foreach ($notifications as $noti)
                                                        <li class="list-group-item">
                                                            <b>{{ $noti['title'] ?? '' }}</b>
                                                            <span
                                                                class="text-muted small ms-2">{{ $noti['created_at'] ?? '' }}</span>
                                                            <div class="mt-1">{{ $noti['content'] ?? '' }}</div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="text-muted">Không có thông báo nào.</div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-6">
                    <div class="card text-center bg-light-blue">
                        <div class="card-body p-2">
                            <div class="fs-4 fw-bold">{{ $work_schedules_count ?? 0 }}</div>
                            <div class="text-muted small">Lịch làm trong tuần</div>
                            <a href="#" class="small" data-bs-toggle="modal" data-bs-target="#workSchedulesModal">Xem
                                chi tiết</a>
                            <!-- Modal danh sách lịch làm việc -->
                            <div class="modal fade" id="workSchedulesModal" tabindex="-1"
                                aria-labelledby="workSchedulesModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="workSchedulesModalLabel">Lịch làm trong tuần
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @php
                                            $mySchedules = collect($work_schedules ?? [])->filter(function (
                                            $ws,
                                            ) use ($employee) {
                                            return isset($ws['employee']['id']) &&
                                            isset($employee['id']) &&
                                            $ws['employee']['id'] == $employee['id'];
                                            });
                                            @endphp
                                            @if ($mySchedules->count() > 0)
                                            <ul class="list-group">
                                                @foreach ($mySchedules as $ws)
                                                <li class="list-group-item list-group-item-success">
                                                    <b>{{ $ws['work_date'] ?? '' }}</b>
                                                    <span class="text-muted small ms-2">Ca:
                                                        {{ $ws['shift'] ?? '' }}</span>
                                                </li>
                                                @endforeach
                                            </ul>
                                            @else
                                            <div class="text-muted">Không có lịch làm việc nào.</div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>

            </div>
        </div>
    </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <a href="{{ route('employee.info') }}" style="text-decoration:none">
                <div class="card text-center h-100 shadow-lg border-0"
                    style="border-radius:1.2rem;transition:box-shadow 0.2s;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                        <div class="rounded-circle mb-2"
                            style="background:linear-gradient(90deg,#6a11cb 0%,#2575fc 100%);width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-id-card fa-2x text-white"></i>
                        </div>
                        <div class="fw-bold text-primary mb-1" style="font-size:1.1rem;">Thông tin nhân viên</div>
                        <span class="text-muted small">Xem chi tiết hồ sơ cá nhân</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                    <i class="fa fa-calendar-alt fa-2x text-primary mb-2"></i>
                    <div class="fw-bold mb-1">Lịch làm việc</div>
                    <span class="text-muted small">Xem lịch làm việc của bạn</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('employee.feedback') }}" style="text-decoration:none">
                <div class="card text-center h-100 shadow-lg border-0"
                    style="border-radius:1.2rem;transition:box-shadow 0.2s;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                        <div class="rounded-circle mb-2"
                            style="background:linear-gradient(90deg,#6a11cb 0%,#2575fc 100%);width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-comments fa-2x text-white"></i>
                        </div>
                        <div class="fw-bold text-primary mb-1" style="font-size:1.1rem;">Góp ý</div>
                        <span class="text-muted small">Gửi góp ý cho admin</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('employee.notifications') }}" style="text-decoration:none">
                <div class="card text-center h-100 shadow-lg border-0"
                    style="border-radius:1.2rem;transition:box-shadow 0.2s;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                        <div class="rounded-circle mb-2"
                            style="background:linear-gradient(90deg,#6a11cb 0%,#2575fc 100%);width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-bell fa-2x text-white"></i>
                        </div>
                        <div class="fw-bold text-primary mb-1" style="font-size:1.1rem;">Thông báo</div>
                        <span class="text-muted small">Xem tất cả thông báo từ admin</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
    </div>
@endsection