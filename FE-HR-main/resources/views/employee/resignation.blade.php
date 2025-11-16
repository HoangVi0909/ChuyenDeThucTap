@extends('layouts.employee')

@section('content')
    <style>
        /* --- Styles giống cũ --- */
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }

        .card-header {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            color: #fff;
            border-radius: 10px 10px 0 0 !important;
        }

        .form-control:focus {
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

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: .875rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-approved {
            background: #d1edff;
            color: #0c5460;
            border: 1px solid #b8daff;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .timeline-item {
            border-left: 3px solid #e9ecef;
            padding-left: 20px;
            margin-bottom: 20px;
            position: relative;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #6c757d;
        }

        .timeline-item.active:before {
            background: #28a745;
        }
    </style>

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-primary"><i class="fas fa-file-alt me-2"></i>Đơn xin nghỉ phép</h1>
        </div>

        <div class="row">
            <!-- Form tạo đơn -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-plus me-2"></i>Đơn xin nghỉ phép</h6>
                    </div>
                    <div class="card-body">
                        <form id="resignationForm">
                            <div class="mb-3">
                                <label for="reason" class="form-label"><i class="fas fa-comment me-1"></i>Lý do nghỉ phép
                                    <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reason" name="reason" rows="4"
                                    placeholder="Vui lòng nêu rõ lý do xin nghỉ việc..." required></textarea>
                                <div class="form-text">10->1000 ký tự</div>
                            </div>

                            <div class="mb-3">
                                <label for="expected_resignation_date" class="form-label"><i
                                        class="fas fa-calendar-day me-1"></i>Chọn ngày muốn nghỉ<span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expected_resignation_date"
                                    name="expected_resignation_date" required>
                                <div class="form-text">Ngày nghỉ phép phải sau ngày hôm nay</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Gửi đơn
                                    xin nghỉ phép</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danh sách đơn đã gửi -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fas fa-list me-2"></i>Đơn xin nghỉ phép của tôi</h6>
                    </div>
                    <div class="card-body">
                        <div id="myRequests">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang
                                        tải...</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Minimum date tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('expected_resignation_date').min = tomorrow.toISOString().split('T')[0];

            // Load existing requests
            loadMyRequests();

            // Form submit
            document.getElementById('resignationForm').addEventListener('submit', function (e) {
                e.preventDefault();
                submitResignationRequest();
            });
        });

        // Laravel backend URL
        const backendUrl = '{{ config("app.url") }}/api/employee';

        async function submitResignationRequest() {
            const form = document.getElementById('resignationForm');
            const formData = new FormData(form);
            const data = {
                reason: formData.get('reason'),
                expected_resignation_date: formData.get('expected_resignation_date')
            };

            // Get token from Laravel session (passed from controller)
            const token = '{{ auth()->user()->api_token ?? "" }}';
            if (!token) {
                alert('Vui lòng đăng nhập lại');
                window.location.href = '/employee/login';
                return;
            }

            try {
                const response = await fetch(`${backendUrl}/resignation-requests`, {
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
                    alert('Đã gửi đơn xin nghỉ phép thành công!');
                    form.reset();
                    loadMyRequests();
                } else {
                    let msg = result.message || 'Có lỗi xảy ra';
                    if (result.errors) {
                        msg = Object.values(result.errors).flat().join('\n');
                    }
                    alert('Lỗi:\n' + msg);
                }
            } catch (err) {
                console.error(err);
                alert('Có lỗi xảy ra khi gửi đơn');
            }
        }

        async function loadMyRequests() {
            const container = document.getElementById('myRequests');
            const token = '{{ auth()->user()->api_token ?? "" }}';
            if (!token) return container.innerHTML = '<p class="text-muted">Chưa đăng nhập</p>';

            try {
                const response = await fetch(`${backendUrl}/my-resignation-requests`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if (response.ok) {
                    const requests = await response.json();
                    displayMyRequests(requests);
                } else {
                    container.innerHTML = '<p class="text-muted">Không thể tải danh sách đơn</p>';
                }
            } catch (err) {
                console.error(err);
                container.innerHTML = '<p class="text-danger">Có lỗi xảy ra</p>';
            }
        }

        function displayMyRequests(requests) {
            const container = document.getElementById('myRequests');
            if (requests.length === 0) return container.innerHTML = '<p class="text-muted text-center py-4">Bạn chưa có đơn xin nghỉ việc nào</p>';

            let html = '';
            requests.forEach(r => {
                const statusClass = r.status === 'pending' ? 'status-pending' : r.status === 'approved' ? 'status-approved' : 'status-rejected';
                const statusText = r.status === 'pending' ? 'Chờ duyệt' : r.status === 'approved' ? 'Đã duyệt' : 'Từ chối';
                const createdDate = new Date(r.created_at).toLocaleDateString('vi-VN');
                const expectedDate = new Date(r.expected_resignation_date).toLocaleDateString('vi-VN');

                html += `
            <div class="timeline-item ${r.status === 'approved' ? 'active' : ''}">
                <div class="d-flex justify-content-between mb-2">
                    <span class="status-badge ${statusClass}">${statusText}</span>
                    <small class="text-muted">${createdDate}</small>
                </div>
                <p class="mb-1"><strong>Ngày nghỉ dự kiến:</strong> ${expectedDate}</p>
                <p class="mb-2">${r.reason}</p>
                ${r.admin_note ? `<div class="alert alert-info alert-sm"><strong>Ghi chú từ admin:</strong> ${r.admin_note}</div>` : ''}
                ${r.reviewed_at ? `<small class="text-muted">Xử lý lúc: ${new Date(r.reviewed_at).toLocaleString('vi-VN')}</small>` : ''}
            </div>`;
            });

            container.innerHTML = html;
        }
    </script>
@endsection