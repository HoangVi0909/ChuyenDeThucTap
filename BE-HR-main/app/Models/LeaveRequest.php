<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'status',
        'admin_note',
        'reviewed_at',
        'reviewed_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }
}
