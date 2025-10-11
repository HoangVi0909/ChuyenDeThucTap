<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Department;
use App\Models\Position;
use App\Models\WorkSchedule;
use App\Models\Salary;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory;
    protected $fillable = [
        'name',
        'gender',
        'email',
        'photo',
        'birth_date',
        'cccd',
        'qualification',
        'phone',
        'position_id',
        'department_id',
        'username',
        'password',
        'remember_token'
    ];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }
    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
