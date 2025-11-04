@extends('layouts.employee')

@section('content')
    <style>
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }

        .card-header {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            color: white;
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
            font-size: 0.875rem;
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
            <h1 class="h3 mb-0 fw-bold text-primary">
                <i class="fas fa-file-alt me-2"></i>Đơn xin nghỉ phép
            </h1>
        </div>

        <div class="row">
            <!-- Form tạo đơn -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-plus me-2"></i>Đơn xin nghỉ phép
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="resignationForm">
                            <div class="mb-3">
                                <label for="reason" class="form-label">
                                    <i class="fas fa-comment me-1"></i>Lý do nghỉ phép <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="reason" name="reason" rows="4"
                                    placeholder="Vui lòng nêu rõ lý do xin nghỉ việc..." required></textarea>
                                <div class="form-text">10->1000 ký tự</div>
                            </div>

                            <div class="mb-3">
                                <label for="expected_resignation_date" class="form-label">
                                    <i class="fas fa-calendar-day me-1"></i>Chọn ngày muốn nghỉ<span
                                        class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="expected_resignation_date"
                                    name="expected_resignation_date" required>
                                <div class="form-text">Ngày nghỉ phép phải sau ngày hôm nay</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i>Gửi đơn xin nghỉ phép
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danh sách đơn đã gửi -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Đơn xin nghỉ phép của tôi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="myRequests">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            document.getElementById('expected_resignation_date').min = tomorrowStr;

            // Load existing requests
            loadMyRequests();

            // Handle form submission
            document.getElementById('resignationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitResignationRequest();
            });
        });

        async function submitResignationRequest() {
            const form = document.getElementById('resignationForm');
            const formData = new FormData(form);

            const data = {
                reason: formData.get('reason'),
                expected_resignation_date: formData.get('expected_resignation_date')
            };

            try {
                const token = '{{ $token }}';
                if (!token) {
                    alert('Vui lòng đăng nhập lại');
                    window.location.href = '/employee/login';
                    return;
                }

                const response = await fetch(
                    '{{ config('app.backend_url', 'http://localhost:8000') }}/api/employee/resignation-requests', {
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
                    loadMyRequests(); // Reload list
                } else {
                    if (result.errors) {
                        let errorMessage = '';
                        Object.values(result.errors).forEach(errors => {
                            errors.forEach(error => {
                                errorMessage += error + '\n';
                            });
                        });
                        alert('Lỗi:\n' + errorMessage);
                    } else {
                        alert('Lỗi: ' + result.message);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi đơn');
            }
        }

        async function loadMyRequests() {
            try {
                const token = '{{ $token }}';
                if (!token) return;

                const response = await fetch(
                    '{{ config('app.backend_url', 'http://localhost:8000') }}/api/employee/my-resignation-requests', {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                if (response.ok) {
                    const requests = await response.json();
                    displayMyRequests(requests);
                } else {
                    document.getElementById('myRequests').innerHTML =
                        '<p class="text-muted">Không thể tải danh sách đơn</p>';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('myRequests').innerHTML = '<p class="text-danger">Có lỗi xảy ra</p>';
            }
        }

        function displayMyRequests(requests) {
            const container = document.getElementById('myRequests');

            if (requests.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-4">Bạn chưa có đơn xin nghỉ việc nào</p>';
                return;
            }

            let html = '';
            requests.forEach(request => {
                const statusClass = getStatusClass(request.status);
                const statusText = getStatusText(request.status);
                const createdDate = new Date(request.created_at).toLocaleDateString('vi-VN');
                const expectedDate = new Date(request.expected_resignation_date).toLocaleDateString('vi-VN');

                html += `
                    <div class="timeline-item ${request.status === 'approved' ? 'active' : ''}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="status-badge ${statusClass}">${statusText}</span>
                            <small class="text-muted">${createdDate}</small>
                        </div>
                        <p class="mb-1"><strong>Ngày nghỉ dự kiến:</strong> ${expectedDate}</p>
                        <p class="mb-2">${request.reason}</p>
                        ${request.admin_note ? `
                                                            <div class="alert alert-info alert-sm">
                                                                <strong>Ghi chú từ admin:</strong> ${request.admin_note}
                                                            </div>
                                                        ` : ''}
                        ${request.reviewed_at ? `
                                                            <small class="text-muted">Xử lý lúc: ${new Date(request.reviewed_at).toLocaleString('vi-VN')}</small>
                                                        ` : ''}
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function getStatusClass(status) {
            switch (status) {
                case 'pending':
                    return 'status-pending';
                case 'approved':
                    return 'status-approved';
                case 'rejected':
                    return 'status-rejected';
                default:
                    return 'status-pending';
            }
        }

        function getStatusText(status) {
            switch (status) {
                case 'pending':
                    return 'Chờ duyệt';
                case 'approved':
                    return 'Đã duyệt';
                case 'rejected':
                    return 'Từ chối';
                default:
                    return 'Không xác định';
            }
        }
    </script>
@endsection
