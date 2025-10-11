<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkSchedule extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'work_date', 'shift'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}