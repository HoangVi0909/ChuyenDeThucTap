<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        }

        .table-responsive {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-building me-2"></i>HR Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.employees.index') }}">
                            <i class="fas fa-users me-1"></i>Nhân viên
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.departments.index') }}">
                            <i class="fas fa-building me-1"></i>Phòng ban
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.positions.index') }}">
                            <i class="fas fa-user-tie me-1"></i>Chức vụ
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.salaries.index') }}">
                            <i class="fas fa-money-bill-wave me-1"></i>Lương
                        </a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.work-schedules.index') }}">
                            <i class="fas fa-calendar-alt me-1"></i>Lịch làm việc
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.notifications.index') }}">
                            <i class="fas fa-bell me-1"></i>Thông báo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.feedback.index') }}">
                            <i class="fas fa-comments me-1"></i>Phản hồi
                        </a>
                    </li>
                    @if(session('admin'))
                        <li class="nav-item">
                            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-outline-light btn-sm ms-2" type="submit">
                                    <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm ms-2" href="{{ route('admin.login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            type="submit">Đăng xuất</button></form>
            </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>