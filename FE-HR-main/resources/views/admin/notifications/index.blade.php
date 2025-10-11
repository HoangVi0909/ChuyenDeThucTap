@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-danger">
                <i class="fas fa-bell me-2"></i>Quản lý Thông báo
            </h1>
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-danger shadow">
                <i class="fas fa-plus me-1"></i>Thêm thông báo
            </a>
        </div>
        {{-- <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-2"
                    style="background: linear-gradient(90deg, #ff512f 0%, #dd2476 100%); color: #fff;">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1 fw-bold">Tổng thông báo</div>
                            <div class="h4 mb-0 fw-bold">
                                @isset($notifications)
                                    {{ is_array($notifications) ? count($notifications) : 0 }}
                                @else
                                    0
                                @endisset
                            </div>
                        </div>
                        <i class="fas fa-bell fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-danger text-white">
                <h6 class="m-0 fw-bold"><i class="fas fa-list me-1"></i>Danh sách thông báo</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Loại</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (is_array($notifications) && count($notifications) > 0)
                                @foreach ($notifications as $notification)
                                    <tr>
                                        <td class="fw-bold text-danger">#{{ $notification['id'] ?? 'N/A' }}</td>
                                        <td class="fw-semibold">{{ $notification['title'] ?? 'N/A' }}</td>
                                        <td><span class="badge bg-info">{{ $notification['type'] ?? 'N/A' }}</span></td>
                                        <td>
                                            <a href="{{ route('admin.notifications.edit', $notification['id']) }}"
                                                class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.notifications.destroy', $notification['id']) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">Không có dữ liệu thông báo</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
