@extends('layouts.employee')
@section('content')
    <div class="container py-4">
        <div class="card mx-auto"
            style="max-width:700px;border-radius:1.2rem;box-shadow:0 8px 32px 0 rgba(37,117,252,0.12);">
            <div class="card-header bg-primary text-white fw-bold" style="border-radius:1.2rem 1.2rem 0 0;">
                <i class="fa fa-bell me-2"></i> Thông báo từ admin
            </div>
            <div class="card-body">
                @if (!empty($notifications))
                    <ul class="list-group">
                        @foreach ($notifications as $noti)
                            <li class="list-group-item">
                                <b>{{ $noti['title'] ?? '' }}</b>
                                <span class="text-muted small ms-2">{{ $noti['created_at'] ?? '' }}</span>
                                <div class="mt-1">{{ $noti['content'] ?? '' }}</div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted">Không có thông báo nào.</div>
                @endif
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary"><i
                        class="fas fa-arrow-left me-1"></i> Quay lại</a>

            </div>

        </div>
    </div>
@endsection