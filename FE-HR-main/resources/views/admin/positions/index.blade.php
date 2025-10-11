@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-warning">
                <i class="fas fa-briefcase me-2"></i>Quản lý chức vụ
            </h1>
            <a href="{{ route('admin.positions.create') }}" class="btn btn-warning shadow text-dark">
                <i class="fas fa-plus me-1"></i>Thêm chức vụ
            </a>
        </div>
        {{-- <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-2"
                    style="background: linear-gradient(90deg, #f7971e 0%, #ffd200 100%); color: #fff;">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1 fw-bold">Tổng chức vụ</div>
                            <div class="h4 mb-0 fw-bold">
                                @isset($positions)
                                    {{ is_array($positions) ? count($positions) : 0 }}
                                @else
                                    0
                                @endisset
                            </div>
                        </div>
                        <i class="fas fa-briefcase fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-warning text-dark">
                <h6 class="m-0 fw-bold"><i class="fas fa-list me-1"></i>Danh sách chức vụ</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tên chức vụ</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (is_array($positions) && count($positions) > 0)
                                @foreach ($positions as $position)
                                    <tr>
                                        <td class="fw-bold text-warning">#{{ $position['id'] ?? 'N/A' }}</td>
                                        <td class="fw-semibold">{{ $position['name'] ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.positions.edit', $position['id']) }}"
                                                class="btn btn-warning btn-sm text-dark"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.positions.destroy', $position['id']) }}"
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
                                    <td colspan="3" class="text-center">Không có dữ liệu chức vụ</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
