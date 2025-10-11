@extends('layouts.admin')
@section('content')
    <h1>Đổi mật khẩu</h1>
    @if (session('message'))
        <div class="alert alert-info">{{ session('message') }}</div>
    @endif
    <form action="{{ route('auth.change_password') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu cũ</label>
            <input type="password" name="old_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu mới</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
    </form>
@endsection