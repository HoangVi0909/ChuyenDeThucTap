@extends('layouts.employee')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Lịch làm việc
                        </h4>
                        @if(isset($employee['name']))
                            <span class="badge bg-light text-dark">{{ $employee['name'] }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3 text-start">
                                <a href="{{ route('employee.work-schedule', ['week_start' => date('Y-m-d', strtotime("$weekStart -7 days"))]) }}"
                                    class="btn btn-outline-primary">
                                    <i class="fas fa-chevron-left me-1"></i>Tuần trước
                                </a>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="current-week">
                                    Tuần từ {{ $weekStart }} đến {{ $weekEnd }}
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <a href="{{ route('employee.work-schedule', ['week_start' => date('Y-m-d', strtotime("$weekStart +7 days"))]) }}"
                                    class="btn btn-outline-primary">
                                    Tuần sau <i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Ca/Ngày</th>
                                        @foreach($weekDays as $day)
                                            <th>{{ date('D', strtotime($day['date'])) }}<br>{{ $day['date'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['S' => 'Ca sáng', 'C' => 'Ca chiều'] as $shiftCode => $shiftName)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $shiftName }}</div>
                                                <div class="small text-muted">
                                                    {{ $shiftCode == 'S' ? '07:30 - 17:00' : '13:00 - 21:00' }}</div>
                                            </td>
                                            @foreach($weekDays as $day)
                                                <td>
                                                    @php
                                                        $scheduleItem = collect($workSchedules)
                                                            ->first(function ($schedule) use ($day, $shiftCode) {
                                                                $scheduleDate = isset($schedule['work_date']) ? date('Y-m-d', strtotime($schedule['work_date'])) : null;
                                                                return is_array($schedule) &&
                                                                    ($schedule['shift'] ?? '') === $shiftCode &&
                                                                    $scheduleDate === $day['dateFormat'];
                                                            });
                                                    @endphp
                                                    @if($scheduleItem)
                                                        <div class="badge bg-success">{{ $shiftName }}</div>
                                                    @else
                                                        <div class="text-muted">-</div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(count($workSchedules) === 0)
                            <div class="text-center mt-3">
                                <p class="text-muted">Chưa có lịch làm việc cho tuần này.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection