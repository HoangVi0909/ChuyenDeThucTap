@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Quản lý Lương</h4>
                        <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm lương mới
                        </a>
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
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Mã NV</th>
                                        <th>Tên nhân viên</th>
                                        <th>Số tiền</th>
                                        <th>Tháng</th>
                                        <th>Ghi chú</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (is_array($salaries) && count($salaries) > 0)
                                        @foreach ($salaries as $salary)
                                            <tr>
                                                <td>{{ $salary['id'] ?? 'N/A' }}</td>
                                                <td>
                                                    @if (isset($salary['employee']))
                                                        <span
                                                            class="badge bg-secondary">{{ $salary['employee']['employee_code'] ?? 'N/A' }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($salary['employee']))
                                                        <strong>{{ $salary['employee']['name'] ?? 'N/A' }}</strong>
                                                    @else
                                                        <span class="text-muted">ID:
                                                            {{ $salary['employee_id'] ?? 'N/A' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-success fw-bold">
                                                        {{ number_format($salary['amount'] ?? 0, 0, ',', '.') }} VNĐ
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $month = $salary['month'] ?? '';
                                                        if ($month) {
                                                            $formatted = date('m/Y', strtotime($month));
                                                            echo $formatted;
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                    @endphp
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $salary['note'] ?? 'Không có ghi chú' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.salaries.show', $salary['id']) }}"
                                                            class="btn btn-sm btn-info" title="Xem chi tiết">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.salaries.edit', $salary['id']) }}"
                                                            class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin.salaries.destroy', $salary['id']) }}"
                                                            method="POST" style="display:inline-block;"
                                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
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
                                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                                        <br>Chưa có dữ liệu lương nào
                                                        <br><small>Hãy thêm lương mới bằng cách nhấn nút "Thêm lương
                                                            mới"</small>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection