@extends('layouts.employee')
@section('content')
    <div class="container py-4">
        <div class="card mx-auto"
            style="max-width:500px;border-radius:1.2rem;box-shadow:0 8px 32px 0 rgba(37,117,252,0.12);">
            <div class="card-header bg-primary text-white fw-bold" style="border-radius:1.2rem 1.2rem 0 0;">
                <i class="fa fa-comments me-2"></i> Gửi góp ý cho admin
            </div>
            <div class="card-body">
                @if (session('feedback_success'))
                    <div class="alert alert-success">{{ session('feedback_success') }}</div>
                @endif
                @if (session('feedback_error'))
                    <div class="alert alert-danger">{{ session('feedback_error') }}</div>
                @endif
                <form method="POST" action="{{ route('employee.send-feedback') }}">
                    @csrf
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="4" placeholder="Nhập góp ý..."
                            required></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Gửi góp
                            ý</button>
                        <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-arrow-left me-1"></i> Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection