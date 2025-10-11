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
        'username',
        'password',
        'phone',
        'address',
        'date_of_birth',
        'hire_date',
        'department_id',
        'position_id',
        'status',
        'photo',
        'birth_date',
        'cccd',
        'qualification',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'hire_date' => 'date',
        ];
    }
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