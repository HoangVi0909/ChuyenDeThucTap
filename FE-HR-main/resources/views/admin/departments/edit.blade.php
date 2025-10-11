@extends('layouts.admin')
@section('content')
    <h1>Sửa phòng ban</h1>
    <form action="{{ route('admin.departments.update', $department['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Tên phòng ban</label>
            <input type="text" name="name" class="form-control" value="{{ $department['name'] }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
@endsection