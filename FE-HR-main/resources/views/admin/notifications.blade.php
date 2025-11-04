@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Thông báo nội bộ</h5>
            </div>
            <div class="card-body">
                <div id="notificationsTable">
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
            loadNotifications();
        });
        async function loadNotifications() {
            const container = document.getElementById('notificationsTable');
            container.innerHTML =
                '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div></div>';
            try {
                const token = '{{ $token ?? '' }}';
                const response = await fetch('/api/admin/notifications', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (!data.data || !data.data.length) {
                        container.innerHTML = '<p class="text-muted text-center py-4">Không có thông báo nào</p>';
                        return;
                    }
                    let html =
                        '<table class="table table-bordered table-hover"><thead><tr><th>Tiêu đề</th><th>Nội dung</th><th>Thời gian</th><th>Trạng thái</th><th></th></tr></thead><tbody>';
                    data.data.forEach(n => {
                        html += `<tr>
                        <td>${n.title}</td>
                        <td>${n.message}</td>
                        <td>${new Date(n.created_at).toLocaleString('vi-VN')}</td>
                        <td><span class="badge bg-${n.is_read ? 'secondary' : 'warning'}">${n.is_read ? 'Đã đọc' : 'Chưa đọc'}</span></td>
                        <td>${!n.is_read ? `<button class='btn btn-sm btn-success' onclick='markAsRead(${n.id})'>Đánh dấu đã đọc</button>` : ''}</td>
                    </tr>`;
                    });
                    html += '</tbody></table>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p class="text-danger">Không thể tải thông báo</p>';
                }
            } catch (error) {
                container.innerHTML = '<p class="text-danger">Có lỗi xảy ra</p>';
            }
        }
        async function markAsRead(id) {
            const token = '{{ $token ?? '' }}';
            try {
                const response = await fetch(`/api/admin/notifications/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    loadNotifications();
                } else {
                    alert('Lỗi khi đánh dấu đã đọc');
                }
            } catch (error) {
                alert('Có lỗi xảy ra khi đánh dấu đã đọc!');
            }
        }
    </script>
@endsection
