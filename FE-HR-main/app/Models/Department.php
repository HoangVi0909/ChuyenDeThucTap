<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'status'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}