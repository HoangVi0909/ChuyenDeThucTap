<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin nhân viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f4f6fa;
        }

        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        }

        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>

<body>
    @if ((!isset($showNavbar) || $showNavbar !== false) && session('employee_token'))
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ route('employee.dashboard') }}">
                    <i class="fas fa-user-graduate me-2"></i>Thông tin nhân viên
                </a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('employee.dashboard') }}">
                                <i class="fas fa-home me-1"></i>Trang chủ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('employee.work-schedule') }}">
                                <i class="fas fa-calendar-alt me-1"></i>Lịch làm việc
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('employee.salary') }}">
                                <i class="fas fa-money-check-alt me-1"></i>Lương của tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('employee.resignation') }}">
                                <i class="fas fa-file-alt me-1"></i>Xin nghỉ phép
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('employee.notifications') }}">
                                <i class="fas fa-bell me-1"></i>Thông báo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('employee.feedback') }}">
                                <i class="fas fa-comment me-1"></i>Góp ý
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-1"></i>Đổi mật khẩu
                        </a>
                    </li> --}}
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng
                                xuất</a>
                            <form id="logout-form" action="{{ route('employee.logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    @endif
    <div class="container">
        @yield('content')
        <!-- Modal đổi mật khẩu -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="changePasswordModalLabel"><i class="fas fa-key me-2"></i>Đổi mật
                            khẩu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('employee.change-password') }}">
                        @csrf
                        <div class="modal-body">
                            @if (session('password_success'))
                                <div class="alert alert-success">{{ session('password_success') }}</div>
                            @endif
                            @if (session('password_error'))
                                <div class="alert alert-danger">{{ session('password_error') }}</div>
                            @endif
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="new_password_confirmation"
                                    name="new_password_confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Đổi mật
                                khẩu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
