<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('employees')->insert([
            [
                'name' => 'Nguyen Van A',
                'gender' => 'Nam',
                'email' => 'nguyenvana@example.com',
                'photo' => 'employees/avatar-male-1.jpg',
                'birth_date' => '1990-01-01',
                'cccd' => '123456789',
                'qualification' => 'Đại học',
                'phone' => '0901234567',
                'position_id' => 1,
                'department_id' => 1,
                'username' => 'nv.a',
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tran Thi B',
                'gender' => 'Nữ',
                'email' => 'tranthib@example.com',
                'photo' => 'employees/avatar-female-1.jpg',
                'birth_date' => '1992-02-02',
                'cccd' => '987654321',
                'qualification' => 'Cao đẳng',
                'phone' => '0907654321',
                'position_id' => 2,
                'department_id' => 2,
                'username' => 'nv.b',
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}