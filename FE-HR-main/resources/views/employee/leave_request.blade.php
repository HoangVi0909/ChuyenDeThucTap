@extends('layouts.employee')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Gửi đơn xin nghỉ phép</h6>
                    </div>
                    <div class="card-body">
                        <form id="leaveRequestForm">
                            <div class="mb-3">
                                <label for="type" class="form-label">Loại phép</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="annual">Nghỉ phép năm</option>
                                    <option value="sick">Nghỉ ốm</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Từ ngày</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Đến ngày</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">Lý do</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Lý do xin nghỉ..."></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Gửi
                                    đơn</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử đơn xin nghỉ phép</h6>
                    </div>
                    <div class="card-body">
                        <div id="leaveRequestsList">
                            <div class="text-center py-4">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadLeaveRequests();
            document.getElementById('leaveRequestForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitLeaveRequest();
            });
        });
        async function submitLeaveRequest() {
            const form = document.getElementById('leaveRequestForm');
            const formData = new FormData(form);
            const data = {
                type: formData.get('type'),
                start_date: formData.get('start_date'),
                end_date: formData.get('end_date'),
                reason: formData.get('reason')
            };
            try {
                const token = '{{ $token ?? '' }}';
                const response = await fetch('/api/employee/leave-requests', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (response.ok) {
                    alert('Gửi đơn xin nghỉ phép thành công!');
                    form.reset();
                    loadLeaveRequests();
                } else {
                    let msg = result.message || 'Lỗi gửi đơn';
                    if (result.errors) {
                        msg += '\n' + Object.values(result.errors).flat().join('\n');
                    }
                    alert(msg);
                }
            } catch (error) {
                alert('Có lỗi xảy ra khi gửi đơn!');
            }
        }
        async function loadLeaveRequests() {
            const container = document.getElementById('leaveRequestsList');
            try {
                const token = '{{ $token ?? '' }}';
                const response = await fetch('/api/employee/my-leave-requests', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const requests = await response.json();
                    if (!requests.length) {
                        container.innerHTML =
                            '<p class="text-muted text-center py-4">Bạn chưa có đơn xin nghỉ phép nào</p>';
                        return;
                    }
                    let html = '<ul class="list-group">';
                    requests.forEach(r => {
                        html += `<li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>${r.type === 'annual' ? 'Nghỉ phép năm' : (r.type === 'sick' ? 'Nghỉ ốm' : 'Khác')}</strong> (${r.start_date} - ${r.end_date})</span>
                            <span class="badge bg-${r.status === 'approved' ? 'success' : (r.status === 'rejected' ? 'danger' : 'warning')} text-uppercase">${r.status}</span>
                        </div>
                        <div><small>Lý do: ${r.reason || '-'}</small></div>
                        ${r.admin_note ? `<div class="alert alert-info mt-2 py-1 px-2"><strong>Ghi chú:</strong> ${r.admin_note}</div>` : ''}
                    </li>`;
                    });
                    html += '</ul>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p class="text-danger">Không thể tải danh sách đơn phép</p>';
                }
            } catch (error) {
                container.innerHTML = '<p class="text-danger">Có lỗi xảy ra</p>';
            }
        }
    </script>
@endsection
