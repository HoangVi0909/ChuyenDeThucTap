@extends('layouts.employee')
@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;
            min-height: 100vh;
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
        }

        .login-card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 400px;
            margin: auto;
        }

        .login-title {
            font-weight: 700;
            color: #2575fc;
            letter-spacing: 1px;
        }

        .login-subtitle {
            color: #6a11cb;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #2575fc;
            box-shadow: 0 0 0 0.2rem rgba(37, 117, 252, .15);
        }

        .input-group-text {
            background: #f4f6fa;
            border: none;
        }

        .btn-login {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 2rem;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
        }

        .login-logo {
            font-size: 2.5rem;
            color: #2575fc;
            margin-bottom: 0.5rem;
        }
    </style>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="login-card">
            <div class="text-center mb-4">
                <div class="login-logo">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h4 class="login-title mb-1">CỔNG THÔNG TIN NHÂN VIÊN</h4>
                <div class="login-subtitle mb-2">ĐĂNG NHẬP HỆ THỐNG</div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('employee.login.post') }}">
                @csrf
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                    <input type="text" class="form-control" name="code" placeholder="Tên đăng nhập" required autofocus>
                </div>
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" class="btn btn-login w-100 py-2 mt-2">ĐĂNG NHẬP</button>
            </form>
        </div>
    </div>
@endsection