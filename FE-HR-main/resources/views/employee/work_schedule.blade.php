@extends('layouts.employee')
@section('content')
    <style>
        .schedule-table {
            border-collapse: separate;
            border-spacing: 2px;
            background: #f8f9fa;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: top;
            height: 120px;
        }

        .schedule-table th {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            text-align: center;
            font-weight: 600;
            height: 60px;
        }

        .time-slot {
            background: #fff3cd;
            color: #856404;
            font-weight: 600;
            text-align: center;
            width: 80px;
        }

        .schedule-item {
            background: #d4edda;
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 8px;
            margin: 2px 0;
            font-size: 0.85rem;
            position: relative;
        }

        .schedule-item.morning {
            background: #d1ecf1;
            border-color: #17a2b8;
        }

        .schedule-item.evening {
            background: #f8d7da;
            border-color: #dc3545;
        }

        .schedule-item .shift-badge {
            position: absolute;
            top: 2px;
            right: 4px;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
        }

        .shift-morning {
            background: #17a2b8;
            color: white;
        }

        .shift-evening {
            background: #dc3545;
            color: white;
        }

        .week-navigation {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        .btn-week {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            color: white;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
        }

        .btn-week:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 117, 252, 0.3);
        }

        .current-week {
            background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
        }

        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-section .form-label {
            font-weight: 600;
            color: #495057;
        }

        .filter-section .form-control,
        .filter-section .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .filter-section .form-control:focus,
        .filter-section .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(37, 117, 252, 0.25);
            border-color: #2575fc;
        }

        .btn-primary {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 117, 252, 0.3);
        }
    </style>

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-primary">
                <i class="fas fa-calendar-alt me-2"></i>Lịch làm việc của tôi
            </h1>
        </div>

        <!-- Week Navigation -->
        <div class="week-navigation">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <button class="btn btn-week" onclick="changeWeek(-1)">
                        <i class="fas fa-chevron-left me-1"></i>Tuần trước
                    </button>
                </div>
                <div class="col-md-6">
                    <div class="current-week">
                        <i class="fas fa-calendar me-2"></i>
                        Tuần từ {{ $weekStart }} đến {{ $weekEnd }}
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-week" onclick="changeWeek(1)">
                        Tuần sau<i class="fas fa-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card shadow mb-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Bộ lọc lịch làm việc
                </h6>
            </div>
            <div class="card-body">
                <form id="filterForm" method="GET">
                    <div class="row g-3">
                        <!-- Lọc theo ca làm việc -->
                        <div class="col-md-3">
                            <label for="shift_filter" class="form-label">
                                <i class="fas fa-clock me-1"></i>Ca làm việc
                            </label>
                            <select class="form-select" id="shift_filter" name="shift">
                                <option value="">Tất cả ca</option>
                                <option value="S" {{ request('shift') == 'S' ? 'selected' : '' }}>Ca sáng (07:30 -
                                    17:00)</option>
                                <option value="C" {{ request('shift') == 'C' ? 'selected' : '' }}>Ca chiều (14:00 -
                                    23:00)</option>
                            </select>
                        </div>

                        <!-- Chọn từ ngày -->
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">
                                <i class="fas fa-calendar-day me-1"></i>Từ ngày
                            </label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from', $currentWeekStart) }}">
                        </div>

                        <!-- Chọn đến ngày -->
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">
                                <i class="fas fa-calendar-day me-1"></i>Đến ngày
                            </label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to', date('Y-m-d', strtotime($currentWeekStart . ' +6 days'))) }}">
                        </div>

                        <!-- Nút áp dụng và reset -->
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Áp dụng
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilter()">
                                    <i class="fas fa-sync-alt me-1"></i>Làm mới
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Schedule Table -->
        <div class="card shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table schedule-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Ca làm</th>
                                @foreach ($weekDays as $day)
                                    <th class="text-center">
                                        <div>{{ $day['dayName'] }}</div>
                                        <div class="small">{{ $day['date'] }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ca sáng -->
                            <tr>
                                <td class="time-slot">
                                    <div class="fw-bold">Ca sáng</div>
                                    <div class="small text-muted">07:30 - 17:00</div>
                                </td>
                                @foreach ($weekDays as $day)
                                    <td>
                                        @php
                                            $morningSchedule = collect($workSchedules)
                                                ->filter(function ($schedule) use ($day) {
                                                    $scheduleDate = date('Y-m-d', strtotime($schedule['work_date']));
                                                    return $scheduleDate == $day['dateFormat'] &&
                                                        $schedule['shift'] == 'S';
                                                })
                                                ->first();
                                        @endphp
                                        @if ($morningSchedule)
                                            <div class="schedule-item morning">
                                                <div class="shift-badge shift-morning">Ca sáng</div>
                                                <div class="fw-bold text-info">
                                                    <i class="fas fa-sun me-1"></i>Làm việc
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <i class="fas fa-clock me-1"></i>07:30 - 17:00
                                                </div>
                                                @if (isset($morningSchedule['note']) && $morningSchedule['note'])
                                                    <div class="small mt-1">
                                                        <i
                                                            class="fas fa-sticky-note me-1"></i>{{ $morningSchedule['note'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <!-- Ca chiều -->
                            <tr>
                                <td class="time-slot">
                                    <div class="fw-bold">Ca chiều</div>
                                    <div class="small text-muted">14:00 - 23:00</div>
                                </td>
                                @foreach ($weekDays as $day)
                                    <td>
                                        @php
                                            $eveningSchedule = collect($workSchedules)
                                                ->filter(function ($schedule) use ($day) {
                                                    $scheduleDate = date('Y-m-d', strtotime($schedule['work_date']));
                                                    return $scheduleDate == $day['dateFormat'] &&
                                                        $schedule['shift'] == 'C';
                                                })
                                                ->first();
                                        @endphp
                                        @if ($eveningSchedule)
                                            <div class="schedule-item evening">
                                                <div class="shift-badge shift-evening">Ca chiều</div>
                                                <div class="fw-bold text-danger">
                                                    <i class="fas fa-moon me-1"></i>Làm việc
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <i class="fas fa-clock me-1"></i>14:00 - 23:00
                                                </div>
                                                @if (isset($eveningSchedule['note']) && $eveningSchedule['note'])
                                                    <div class="small mt-1">
                                                        <i
                                                            class="fas fa-sticky-note me-1"></i>{{ $eveningSchedule['note'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Chú thích</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="schedule-item morning me-2" style="width: 20px; height: 20px; margin: 0;">
                                    </div>
                                    <span>Ca sáng (07:30 - 17:00)</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="schedule-item evening me-2" style="width: 20px; height: 20px; margin: 0;">
                                    </div>
                                    <span>Ca chiều (14:00 - 23:00)</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div
                                        style="width: 20px; height: 20px; background: #f8f9fa; border: 1px solid #dee2e6; margin-right: 8px;">
                                    </div>
                                    <span>Không có lịch làm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changeWeek(direction) {
            const currentDate = new Date('{{ $currentWeekStart }}');
            currentDate.setDate(currentDate.getDate() + (direction * 7));

            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const day = String(currentDate.getDate()).padStart(2, '0');
            const weekStart = `${year}-${month}-${day}`;

            // Giữ các tham số filter hiện tại
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('week_start', weekStart);

            window.location.href = `{{ route('employee.work-schedule') }}?${urlParams.toString()}`;
        }

        function resetFilter() {
            // Reset về tuần hiện tại và xóa tất cả filter
            const today = new Date();
            const monday = new Date(today.setDate(today.getDate() - today.getDay() + 1));

            const year = monday.getFullYear();
            const month = String(monday.getMonth() + 1).padStart(2, '0');
            const day = String(monday.getDate()).padStart(2, '0');
            const weekStart = `${year}-${month}-${day}`;

            window.location.href = `{{ route('employee.work-schedule') }}?week_start=${weekStart}`;
        }
    </script>
@endsection
