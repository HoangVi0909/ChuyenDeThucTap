@extends('layouts.employee')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-money-check-alt me-2"></i>Lương của tôi
                        </h4>
                        @if (isset($employee['name']))
                            <span class="badge bg-light text-dark">{{ $employee['name'] }}</span>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (isset($salaries) && count($salaries) > 0)
                            {{-- Thống kê tổng quan --}}
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                            <h5>{{ count($salaries) }}</h5>
                                            <small>Tháng có lương</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                            <h5>{{ number_format($totalAmount ?? 0) }}</h5>
                                            <small>Tổng thu nhập (VNĐ)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                                            <h5>{{ number_format($averageAmount ?? 0) }}</h5>
                                            <small>Lương TB (VNĐ)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-star fa-2x mb-2"></i>
                                            <h5>{{ $latestSalary ? number_format($latestSalary['total_salary'] ?? 0) : 'N/A' }}
                                            </h5>
                                            <small>Lương gần nhất (VNĐ)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Bảng lịch sử lương --}}
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><i class="fas fa-calendar me-1"></i>Tháng/Năm</th>
                                            <th><i class="fas fa-clock me-1"></i>Tổng giờ</th>
                                            <th><i class="fas fa-dollar-sign me-1"></i>Lương/giờ</th>
                                            <th><i class="fas fa-money-bill me-1"></i>Lương cơ bản</th>
                                            <th><i class="fas fa-plus-circle me-1"></i>Phụ cấp</th>
                                            <th><i class="fas fa-gift me-1"></i>Thưởng</th>
                                            <th><i class="fas fa-minus-circle me-1"></i>Phạt</th>
                                            <th><i class="fas fa-money-check-alt me-1"></i>Tổng lương (trước thuế)</th>
                                            <th><i class="fas fa-percentage me-1"></i>Thuế (5%)</th>
                                            <th><i class="fas fa-wallet me-1"></i>Thực lĩnh</th>
                                            <th><i class="fas fa-info-circle me-1"></i>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salaries as $salary)
                                            <tr>
                                                <td>
                                                    <strong
                                                        class="text-primary">{{ $salary['month_year'] ?? 'N/A' }}</strong>
                                                </td>
                                                <td>{{ number_format($salary['total_hours'] ?? 0, 1) }} giờ</td>
                                                <td>{{ number_format($salary['hourly_rate'] ?? 0) }} VNĐ</td>
                                                <td>{{ number_format($salary['base_salary'] ?? 0) }} VNĐ</td>
                                                <td class="text-info">
                                                    {{ number_format($salary['position_allowance'] ?? 0) }} VNĐ</td>
                                                <td class="text-success">{{ number_format($salary['bonus'] ?? 0) }} VNĐ
                                                </td>
                                                <td class="text-danger">{{ number_format($salary['penalty'] ?? 0) }} VNĐ
                                                </td>
                                                @php
                                                    $gross =
                                                        ($salary['base_salary'] ?? 0) +
                                                        ($salary['position_allowance'] ?? 0) +
                                                        ($salary['bonus'] ?? 0) -
                                                        ($salary['penalty'] ?? 0);
                                                    $tax = $salary['tax_amount'] ?? 0;
                                                    $net = $gross - $tax;
                                                @endphp
                                                <td><strong class="text-primary fs-6">{{ number_format($gross) }}
                                                        VNĐ</strong></td>
                                                <td class="text-danger">{{ number_format($tax) }} VNĐ</td>
                                                <td class="text-success">{{ number_format($net) }} VNĐ</td>
                                                <td>
                                                    @php
                                                        $status = $salary['status'] ?? 'draft';
                                                        $statusConfig = [
                                                            'draft' => [
                                                                'class' => 'warning',
                                                                'icon' => 'clock',
                                                                'text' => 'Chờ duyệt',
                                                            ],
                                                            'approved' => [
                                                                'class' => 'success',
                                                                'icon' => 'check',
                                                                'text' => 'Đã duyệt',
                                                            ],
                                                            'paid' => [
                                                                'class' => 'primary',
                                                                'icon' => 'money-bill',
                                                                'text' => 'Đã trả',
                                                            ],
                                                        ];
                                                        $config = $statusConfig[$status] ?? [
                                                            'class' => 'secondary',
                                                            'icon' => 'question',
                                                            'text' => $status,
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $config['class'] }}">
                                                        <i
                                                            class="fas fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Chi tiết lương tháng gần nhất --}}
                            @if (count($salaries) > 0)
                                @php $latestSalary = collect($salaries)->sortByDesc('month_year')->first(); @endphp
                                <div class="mt-4">
                                    <h5 class="text-primary">
                                        <i class="fas fa-info-circle me-2"></i>Chi tiết lương tháng
                                        {{ $latestSalary['month_year'] }}
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary">Thông tin cơ bản</h6>
                                                    <p><strong>Số giờ làm việc:</strong>
                                                        {{ number_format($latestSalary['total_hours'] ?? 0, 1) }} giờ</p>
                                                    <p><strong>Lương theo giờ:</strong>
                                                        {{ number_format($latestSalary['hourly_rate'] ?? 0) }} VNĐ</p>
                                                    <p><strong>Lương cơ bản:</strong>
                                                        {{ number_format($latestSalary['base_salary'] ?? 0) }} VNĐ</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success">Phụ cấp & Thưởng phạt</h6>
                                                    <p><strong>Phụ cấp:</strong> <span
                                                            class="text-info">{{ number_format($latestSalary['position_allowance'] ?? 0) }}
                                                            VNĐ</span></p>
                                                    <p><strong>Thưởng:</strong> <span
                                                            class="text-success">{{ number_format($latestSalary['bonus'] ?? 0) }}
                                                            VNĐ</span></p>
                                                    <p><strong>Phạt:</strong> <span
                                                            class="text-danger">{{ number_format($latestSalary['penalty'] ?? 0) }}
                                                            VNĐ</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if (!empty($latestSalary['notes']))
                                        <div class="alert alert-info mt-3">
                                            <strong><i class="fas fa-sticky-note me-2"></i>Ghi chú:</strong>
                                            {{ $latestSalary['notes'] }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @else
                            {{-- Không có dữ liệu lương --}}
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-circle fa-5x text-muted mb-4"></i>
                                <h4 class="text-muted">Chưa có dữ liệu lương</h4>
                                <p class="text-muted">Bạn chưa có bản ghi lương nào. Vui lòng liên hệ với bộ phận nhân sự để
                                    biết thêm thông tin.</p>
                                <div class="mt-4">
                                    <a href="{{ route('employee.dashboard') }}" class="btn btn-primary">
                                        <i class="fas fa-home me-2"></i>Về trang chủ
                                    </a>
                                    <a href="{{ route('employee.send-feedback') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-comment me-2"></i>Gửi phản hồi
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Auto refresh page every 5 minutes to get latest salary data
        setTimeout(function() {
            location.reload();
        }, 300000); // 5 minutes
    </script>
@endsection
