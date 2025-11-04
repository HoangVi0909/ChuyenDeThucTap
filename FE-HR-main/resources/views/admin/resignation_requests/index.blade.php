@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-alt me-2"></i>Quản lý đơn xin nghỉ phép
            </h1>
        </div>

        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Bộ lọc
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.resignation-requests.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="employee_search" class="form-label">Tìm kiếm nhân viên</label>
                            <input type="text" class="form-control" id="employee_search" name="employee_search"
                                value="{{ request('employee_search') }}"
                                placeholder="Nhập tên, username hoặc email nhân viên...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Danh sách đơn xin nghỉ phép ({{ $pagination['total'] }} đơn)
                </h6>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nhân viên</th>
                                <th>Ngày nghỉ dự kiến</th>
                                <th>Trạng thái</th>
                                <th>Ngày gửi</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resignationRequests as $request)
                                <tr>
                                    <td>{{ $request['id'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-bold">{{ $request['employee']['name'] ?? 'N/A' }}</div>
                                                <div class="small text-muted">
                                                    {{ $request['employee']['username'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            {{ date('d/m/Y', strtotime($request['expected_resignation_date'])) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match ($request['status']) {
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                default => 'secondary',
                                            };
                                            $statusText = match ($request['status']) {
                                                'pending' => 'Chờ duyệt',
                                                'approved' => 'Đã duyệt',
                                                'rejected' => 'Từ chối',
                                                default => 'Không xác định',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>{{ date('d/m/Y H:i', strtotime($request['created_at'])) }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.resignation-requests.show', $request['id']) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($request['status'] === 'pending')
                                                <button type="button" class="btn btn-success btn-sm"
                                                    onclick="updateStatus({{ $request['id'] }}, 'approved')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="updateStatus({{ $request['id'] }}, 'rejected')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <div class="text-muted">Không có đơn xin nghỉ phép nào</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal cho update status -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xử lý đơn xin nghỉ phép</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="admin_note" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea class="form-control" id="admin_note" name="admin_note" rows="3" placeholder="Nhập ghi chú của bạn..."></textarea>
                        </div>
                        <input type="hidden" id="status_input" name="status">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateStatus(id, status) {
            const modal = new bootstrap.Modal(document.getElementById('statusModal'));
            const form = document.getElementById('statusForm');
            const statusInput = document.getElementById('status_input');
            const submitBtn = document.getElementById('submitBtn');
            const modalTitle = document.querySelector('#statusModal .modal-title');

            // Update form action
            form.action = `{{ route('admin.resignation-requests.index') }}/${id}/status`;

            // Update status input
            statusInput.value = status;

            // Update modal title and button
            if (status === 'approved') {
                modalTitle.textContent = 'Duyệt đơn xin nghỉ phép';
                submitBtn.textContent = 'Duyệt đơn';
                submitBtn.className = 'btn btn-success';
            } else {
                modalTitle.textContent = 'Từ chối đơn xin nghỉ phép';
                submitBtn.textContent = 'Từ chối đơn';
                submitBtn.className = 'btn btn-danger';
            }

            modal.show();
        }
    </script>
@endsection
