@extends('layouts.admin')
@section('content')
    <h1>Sửa chức vụ</h1>
    <form action="{{ route('admin.positions.update', $position['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Tên chức vụ</label>
            <input type="text" name="name" class="form-control" value="{{ $position['name'] }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
@endsection