@extends('layouts.admin')
@section('content')
    <h1>Quên mật khẩu</h1>
    @if (session('message'))
        <div class="alert alert-info">{{ session('message') }}</div>
    @endif
    <form action="{{ route('auth.forgot') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
    </form>
@endsection