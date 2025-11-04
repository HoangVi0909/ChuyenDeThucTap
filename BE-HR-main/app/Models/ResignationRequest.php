<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResignationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'reason',
        'expected_resignation_date',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'expected_resignation_date' => 'date',
        'reviewed_at' => 'datetime'
    ];

    // Relationship với Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Relationship với Admin (người duyệt)
    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    // Scope để lọc theo trạng thái
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessor để hiển thị trạng thái tiếng Việt
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            default => 'Không xác định'
        };
    }
}
