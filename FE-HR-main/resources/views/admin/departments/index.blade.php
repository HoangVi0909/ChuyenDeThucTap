@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold text-success">
                <i class="fas fa-building me-2"></i>Quản lý phòng ban
            </h1>
            <a href="{{ route('admin.departments.create') }}" class="btn btn-success shadow">
                <i class="fas fa-plus me-1"></i>Thêm phòng ban
            </a>
        </div>
        {{-- <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-2"
                    style="background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%); color: #fff;">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase mb-1 fw-bold">Tổng phòng ban</div>
                            <div class="h4 mb-0 fw-bold">
                                @isset($departments)
                                    {{ is_array($departments) ? count($departments) : 0 }}
                                @else
                                    0
                                @endisset
                            </div>
                        </div>
                        <i class="fas fa-building fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 fw-bold"><i class="fas fa-list me-1"></i>Danh sách phòng ban</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tên phòng ban</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (is_array($departments) && count($departments) > 0)
                                @foreach ($departments as $department)
                                    <tr>
                                        <td class="fw-bold text-success">#{{ $department['id'] ?? 'N/A' }}</td>
                                        <td class="fw-semibold">{{ $department['name'] ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.departments.edit', $department['id']) }}"
                                                class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.departments.destroy', $department['id']) }}"
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
                                    <td colspan="3" class="text-center">Không có dữ liệu phòng ban</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
