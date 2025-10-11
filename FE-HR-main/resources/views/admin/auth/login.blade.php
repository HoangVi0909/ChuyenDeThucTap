<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - HR Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px 15px 0 0 !important;
            color: white;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 12px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">
                    <div class="card-header text-center py-4">
                        <h3 class="mb-2"><i class="fas fa-users-cog me-2"></i>HR Management System</h3>
                        <p class="mb-0 opacity-90">Đăng nhập vào hệ thống quản lý nhân sự</p>
                    </div>
                    <div class="card-body p-4">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('message'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle me-2"></i>{{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.login') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="username" class="form-label fw-semibold">
                                    <i class="fas fa-user me-2 text-muted"></i>Tên đăng nhập
                                </label>
                                <input type="text" name="username" id="username" class="form-control" required
                                    value="{{ old('username') }}" placeholder="Nhập tên đăng nhập hoặc email">
                                @error('username')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2 text-muted"></i>Mật khẩu
                                </label>
                                <input type="password" name="password" id="password" class="form-control" required
                                    placeholder="Nhập mật khẩu">
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                </button>
                            </div>
                        </form>

                        {{-- <div class="text-center">
                            <a href="{{ route('admin.forgot') }}" class="text-decoration-none">
                                <i class="fas fa-key me-1"></i>Quên mật khẩu?
                            </a>
                        </div> --}}

                        <hr class="my-4">

                        <div class="alert alert-light border-0 text-center mb-0">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Hệ thống bảo mật - Chỉ dành cho quản trị viên
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
