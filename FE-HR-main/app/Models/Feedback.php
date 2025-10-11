<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'content'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}