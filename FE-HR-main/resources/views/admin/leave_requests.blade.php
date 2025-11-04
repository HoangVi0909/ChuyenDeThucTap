@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Quản lý đơn xin nghỉ phép</h5>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-3">
                        <select id="filterStatus" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ duyệt</option>
                            <option value="approved">Đã duyệt</option>
                            <option value="rejected">Từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="filterEmployee" class="form-control" placeholder="Tìm kiếm nhân viên...">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="loadLeaveRequests()">
                            <i class="fas fa-filter me-1"></i>Lọc
                        </button>
                    </div>
                </div>
                <div id="leaveRequestsTable">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
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
        });
        async function loadLeaveRequests() {
            const status = document.getElementById('filterStatus').value;
            const employee = document.getElementById('filterEmployee').value;
            const container = document.getElementById('leaveRequestsTable');
            container.innerHTML =
                '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div></div>';
            try {
                const token = '{{ $token ?? '' }}';
                let url = '/api/admin/leave-requests?';
                if (status) url += 'status=' + status + '&';
                if (employee) url += 'employee_name=' + encodeURIComponent(employee);
                const response = await fetch(url, {
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
                            '<p class="text-muted text-center py-4">Không có đơn xin nghỉ phép nào</p>';
                        return;
                    }
                    let html =
                        '<table class="table table-bordered table-hover"><thead><tr><th>Nhân viên</th><th>Loại phép</th><th>Từ ngày</th><th>Đến ngày</th><th>Lý do</th><th>Trạng thái</th><th>Ghi chú</th><th>Duyệt</th></tr></thead><tbody>';
                    requests.forEach(r => {
                        html += `<tr>
                        <td>${r.employee?.name || '-'}</td>
                        <td>${r.type === 'annual' ? 'Nghỉ phép năm' : (r.type === 'sick' ? 'Nghỉ ốm' : 'Khác')}</td>
                        <td>${r.start_date}</td>
                        <td>${r.end_date}</td>
                        <td>${r.reason || '-'}</td>
                        <td><span class="badge bg-${r.status === 'approved' ? 'success' : (r.status === 'rejected' ? 'danger' : 'warning')}">${r.status}</span></td>
                        <td>${r.admin_note || ''}</td>
                        <td>${r.status === 'pending' ? `<button class='btn btn-success btn-sm' onclick='reviewLeave(${r.id},"approved")'>Duyệt</button> <button class='btn btn-danger btn-sm' onclick='reviewLeave(${r.id},"rejected")'>Từ chối</button>` : ''}</td>
                    </tr>`;
                    });
                    html += '</tbody></table>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p class="text-danger">Không thể tải danh sách đơn phép</p>';
                }
            } catch (error) {
                container.innerHTML = '<p class="text-danger">Có lỗi xảy ra</p>';
            }
        }
        async function reviewLeave(id, status) {
            const note = prompt('Nhập ghi chú (không bắt buộc):');
            if (status !== 'approved' && status !== 'rejected') return;
            try {
                const token = '{{ $token ?? '' }}';
                const response = await fetch(`/api/admin/leave-requests/${id}/review`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status,
                        admin_note: note
                    })
                });
                const result = await response.json();
                if (response.ok) {
                    alert('Cập nhật thành công!');
                    loadLeaveRequests();
                } else {
                    alert(result.message || 'Lỗi cập nhật');
                }
            } catch (error) {
                alert('Có lỗi xảy ra khi cập nhật!');
            }
        }
    </script>
@endsection
