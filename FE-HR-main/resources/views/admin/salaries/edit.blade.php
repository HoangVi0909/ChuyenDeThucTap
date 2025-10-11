@extends('layouts.admin')
@section('content')
    <h1>Sửa lương</h1>
    <form action="{{ route('admin.salaries.update', $salary['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Nhân viên</label>
            <input type="number" name="employee_id" class="form-control" value="{{ $salary['employee_id'] }}" required>
        </div>
        <div class="form-group">
            <label>Số tiền</label>
            <input type="number" name="amount" class="form-control" value="{{ $salary['amount'] }}" required>
        </div>
        <div class="form-group">
            <label>Tháng</label>
            <input type="month" name="month" class="form-control" value="{{ $salary['month'] }}" required>
        </div>
        <div class="form-group">
            <label>Ghi chú</label>
            <textarea name="note" class="form-control">{{ $salary['note'] }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
@endsection