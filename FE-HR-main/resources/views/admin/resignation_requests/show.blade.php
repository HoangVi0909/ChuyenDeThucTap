@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-alt me-2"></i>Chi tiết đơn xin nghỉ phép #{{ $resignationRequest['id'] }}
            </h1>
            <a href="{{ route('admin.resignation-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <div class="row">
            <!-- Thông tin đơn -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>Thông tin đơn xin nghỉ phép
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Trạng thái:</strong></div>
                            <div class="col-sm-9">
                                @php
                                    $statusClass = match ($resignationRequest['status']) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary',
                                    };
                                    $statusText = match ($resignationRequest['status']) {
                                        'pending' => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Từ chối',
                                        default => 'Không xác định',
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }} fs-6">{{ $statusText }}</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Ngày gửi đơn:</strong></div>
                            <div class="col-sm-9">{{ date('d/m/Y H:i:s', strtotime($resignationRequest['created_at'])) }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Ngày nghỉ dự kiến:</strong></div>
                            <div class="col-sm-9">
                                <span class="text-primary fw-bold">
                                    {{ date('d/m/Y', strtotime($resignationRequest['expected_resignation_date'])) }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Lý do nghỉ việc:</strong></div>
                            <div class="col-sm-9">
                                <div class="bg-light p-3 rounded">
                                    {{ $resignationRequest['reason'] }}
                                </div>
                            </div>
                        </div>

                        @if ($resignationRequest['admin_note'])
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Ghi chú admin:</strong></div>
                                <div class="col-sm-9">
                                    <div class="bg-info bg-opacity-10 p-3 rounded border-start border-info border-4">
                                        {{ $resignationRequest['admin_note'] }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($resignationRequest['reviewed_at'])
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Thời gian xử lý:</strong></div>
                                <div class="col-sm-9">
                                    {{ date('d/m/Y H:i:s', strtotime($resignationRequest['reviewed_at'])) }}</div>
                            </div>

                            @if (isset($resignationRequest['reviewer']))
                                <div class="row mb-3">
                                    <div class="col-sm-3"><strong>Người xử lý:</strong></div>
                                    <div class="col-sm-9">{{ $resignationRequest['reviewer']['username'] ?? 'N/A' }}</div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Thông tin nhân viên -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user me-2"></i>Thông tin nhân viên
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-user-circle fa-4x text-muted"></i>
                        </div>

                        <div class="mb-2">
                            <strong>Tên:</strong> {{ $resignationRequest['employee']['name'] ?? 'N/A' }}
                        </div>
                        <div class="mb-2">
                            <strong>Username:</strong> {{ $resignationRequest['employee']['username'] ?? 'N/A' }}
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong> {{ $resignationRequest['employee']['email'] ?? 'N/A' }}
                        </div>
                        <div class="mb-2">
                            <strong>Điện thoại:</strong> {{ $resignationRequest['employee']['phone'] ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if ($resignationRequest['status'] === 'pending')
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-tasks me-2"></i>Thao tác
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success"
                                    onclick="updateStatus({{ $resignationRequest['id'] }}, 'approved')">
                                    <i class="fas fa-check me-1"></i>Duyệt đơn
                                </button>
                                <button type="button" class="btn btn-danger"
                                    onclick="updateStatus({{ $resignationRequest['id'] }}, 'rejected')">
                                    <i class="fas fa-times me-1"></i>Từ chối đơn
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
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

        @if (session('success'))
            alert('{{ session('success') }}');
        @endif

        @if (session('error'))
            alert('{{ session('error') }}');
        @endif
    </script>
@endsection
