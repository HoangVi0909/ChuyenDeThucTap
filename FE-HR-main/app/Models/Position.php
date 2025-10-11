<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'department_id',
        'salary_min',
        'salary_max',
        'status'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}