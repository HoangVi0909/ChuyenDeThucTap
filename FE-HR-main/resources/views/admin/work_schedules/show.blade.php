@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Chi tiết lịch làm việc</h4>
                        {{-- <div>
                            <a href="{{ route('admin.work-schedules.edit', $workSchedule['id']) }}"
                                class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div> --}}
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin nhân viên</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Mã nhân viên:</strong></td>
                                        <td>
                                            @if (isset($workSchedule['employee']))
                                                <span
                                                    class="badge bg-secondary">{{ $workSchedule['employee']['employee_code'] ?? 'N/A' }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tên nhân viên:</strong></td>
                                        <td>
                                            @if (isset($workSchedule['employee']))
                                                {{ $workSchedule['employee']['name'] ?? 'N/A' }}
                                            @else
                                                <span class="text-muted">ID:
                                                    {{ $workSchedule['employee_id'] ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h6>Thông tin lịch làm việc</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>ID lịch:</strong></td>
                                        <td>{{ $workSchedule['id'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày làm việc:</strong></td>
                                        <td>
                                            @php
                                                $workDate = $workSchedule['work_date'] ?? '';
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
                                                        ' (' .
                                                        ($dayNames[$dayOfWeek] ?? $dayOfWeek) .
                                                        ')';
                                                } else {
                                                    echo 'N/A';
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ca làm việc:</strong></td>
                                        <td>
                                            @php
                                                $shift = $workSchedule['shift'] ?? '';
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
                                    </tr>
                                    <tr>
                                        <td><strong>Thời gian:</strong></td>
                                        <td>
                                            @if ($shift == 'S')
                                                8:00 - 17:00 (8 tiếng)
                                            @elseif($shift == 'C')
                                                14:00 - 23:00 (9 tiếng)
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6>Thông tin hệ thống</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="200"><strong>Ngày tạo:</strong></td>
                                        <td>
                                            @if (isset($workSchedule['created_at']))
                                                {{ date('d/m/Y H:i:s', strtotime($workSchedule['created_at'])) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cập nhật lần cuối:</strong></td>
                                        <td>
                                            @if (isset($workSchedule['updated_at']))
                                                {{ date('d/m/Y H:i:s', strtotime($workSchedule['updated_at'])) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <h6><i class="fas fa-info-circle"></i> Thông tin ca làm việc:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Ca sáng (S):</strong>
                                    <ul class="mb-0">
                                        <li>Thời gian: 8:00 - 17:00</li>
                                        <li>Tổng thời gian: 8 tiếng</li>
                                        <li>Nghỉ trưa: 12:00 - 13:00</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong>Ca chiều (C):</strong>
                                    <ul class="mb-0">
                                        <li>Thời gian: 14:00 - 23:00</li>
                                        <li>Tổng thời gian: 9 tiếng</li>
                                        <li>Nghỉ ăn: 18:00 - 19:00</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại danh sách
                            </a>

                            <div>
                                <a href="{{ route('admin.work-schedules.edit', $workSchedule['id']) }}"
                                    class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>

                                <form action="{{ route('admin.work-schedules.destroy', $workSchedule['id']) }}"
                                    method="POST" style="display:inline-block;"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch làm việc này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
