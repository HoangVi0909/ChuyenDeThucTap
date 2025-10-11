@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-info">
                <i class="fas fa-calendar-alt me-2"></i>Quản lý Lịch làm việc
            </h1>
            <a href="{{ route('admin.work-schedules.create') }}" class="btn btn-info shadow">
                <i class="fas fa-plus me-1"></i>Thêm lịch làm việc mới
            </a>
        </div>
        {{-- <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-2"
                    style="background: linear-gradient(90deg, #00c6ff 0%, #0072ff 100%); color: #fff;">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1 fw-bold">Tổng lịch làm việc</div>
                            <div class="h4 mb-0 fw-bold">
                                @isset($workSchedules)
                                    {{ is_array($workSchedules) ? count($workSchedules) : 0 }}
                                @else
                                    0
                                @endisset
                            </div>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 fw-bold"><i class="fas fa-list me-1"></i>Danh sách lịch làm việc</h6>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (isset($error) && $error)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h6>Không thể kết nối đến API Backend</h6>
                        <p>{{ $error }}</p>
                        <small>Vui lòng kiểm tra:
                            <br>- Server backend có đang chạy trên port 8000 không?
                            <br>- API endpoint có đúng không?
                            <br>- Kết nối mạng có ổn định không?
                        </small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Tên nhân viên</th>
                                <th>Ngày làm việc</th>
                                <th>Ca làm việc</th>
                                <th>Thời gian</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($workSchedules) > 0)
                                @foreach ($workSchedules as $schedule)
                                    <tr>
                                        <td>
                                            @if (isset($schedule['employee']))
                                                <span class="fw-bold text-info"><i class="fas fa-user"></i>
                                                    {{ $schedule['employee']['name'] ?? 'N/A' }}</span>
                                            @else
                                                <span class="text-muted">ID: {{ $schedule['employee_id'] ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $workDate = $schedule['work_date'] ?? '';
                                                if ($workDate) {
                                                    $formatted = date('d/m/Y', strtotime($workDate));
                                                    $dayOfWeek = date('l', strtotime($workDate));
                                                    $dayNames = [
                                                        'Monday' => 'Thứ 2',
                                                        'Tuesday' => 'Thứ 3',
                                                        'Wednesday' => 'Thứ 4',
                                                        'Thursday' => 'Thứ 5',
                                                        'Friday' => 'Thứ 6',
                                                        'Saturday' => 'Thứ 7',
                                                        'Sunday' => 'Chủ nhật',
                                                    ];
                                                    echo $formatted .
                                                        '<br><small class="text-muted">' .
                                                        ($dayNames[$dayOfWeek] ?? $dayOfWeek) .
                                                        '</small>';
                                                } else {
                                                    echo 'N/A';
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            @php
                                                $shift = $schedule['shift'] ?? '';
                                                $shifts = [
                                                    'S' => ['name' => 'Ca sáng', 'class' => 'bg-warning'],
                                                    'C' => ['name' => 'Ca chiều', 'class' => 'bg-info'],
                                                ];
                                                $shiftInfo = $shifts[$shift] ?? [
                                                    'name' => 'N/A',
                                                    'class' => 'bg-secondary',
                                                ];
                                            @endphp
                                            <span class="badge {{ $shiftInfo['class'] }}">{{ $shiftInfo['name'] }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                @if ($shift == 'S')
                                                    8:00 - 17:00
                                                @elseif($shift == 'C')
                                                    14:00 - 23:00
                                                @else
                                                    N/A
                                                @endif
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                @if (!empty($schedule['id']))
                                                    <a href="{{ route('admin.work-schedules.show', $schedule['id']) }}"
                                                        class="btn btn-info btn-sm" title="Xem chi tiết"><i
                                                            class="fas fa-eye"></i></a>
                                                    <a href="{{ route('admin.work-schedules.edit', $schedule['id']) }}"
                                                        class="btn btn-warning btn-sm" title="Chỉnh sửa"><i
                                                            class="fas fa-edit"></i></a>
                                                    <form
                                                        action="{{ route('admin.work-schedules.destroy', $schedule['id']) }}"
                                                        method="POST" style="display:inline-block;"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch làm việc này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            title="Xóa"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        @if (isset($error) && $error)
                                            <div class="text-muted">
                                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                                <br>Không thể tải dữ liệu từ server
                                            </div>
                                        @else
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                                <br>Chưa có lịch làm việc nào
                                                <br><small>Hãy thêm lịch làm việc mới bằng cách nhấn nút "Thêm lịch làm việc
                                                    mới"</small>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if (method_exists($workSchedules, 'links'))
                    <div class="d-flex justify-content-center mt-3">
                        {!! $workSchedules->links('pagination::bootstrap-5') !!}
                    </div>
                @endif
                <style>
                    .pagination~.pagination-info,
                    .pagination-info {
                        display: none !important;
                    }
                </style>
            </div>
        </div>
    @endsection
