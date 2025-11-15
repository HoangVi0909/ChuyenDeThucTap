<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả id có trong bảng positions và departments
        $positions = DB::table('positions')->pluck('id')->toArray();
        $departments = DB::table('departments')->pluck('id')->toArray();

        $employees = [];

        // Thêm nhân viên cố định: Nguyen Van A
        $employees[] = [
            'name' => 'Nguyen Van A',
            'gender' => 'Nam',
            'email' => 'nguyenvana@example.com',
            'photo' => 'employees/avatar-male-1.jpg',
            'birth_date' => '1990-01-01',
            'cccd' => '123456789',
            'qualification' => 'Đại học',
            'phone' => '0901234567',
            'position_id' => $positions[0], // chọn vị trí đầu tiên
            'department_id' => $departments[0], // chọn phòng đầu tiên
            'username' => 'nv.a',
            'password' => Hash::make('password'),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Tạo thêm 20 nhân viên ngẫu nhiên
        for ($i = 1; $i <= 50; $i++) {
            $gender = rand(0,1) ? 'Nam' : 'Nữ';
            $employees[] = [
                'name' => ($gender == 'Nam' ? 'Nguyen Van ' : 'Tran Thi ') . $i,
                'gender' => $gender,
                'email' => "employee$i@example.com",
                'photo' => $gender == 'Nam' ? 'employees/avatar-male-'.rand(1,5).'.jpg' : 'employees/avatar-female-'.rand(1,5).'.jpg',
                'birth_date' => date('Y-m-d', strtotime('-'.rand(20,40).' years')),
                'cccd' => strval(rand(100000000,999999999)),
                'qualification' => rand(0,1) ? 'Đại học' : 'Cao đẳng',
                'phone' => '09'.rand(10000000,99999999),
                'position_id' => $positions[array_rand($positions)],
                'department_id' => $departments[array_rand($departments)],
                'username' => "nv$i",
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('employees')->insert($employees);
    }
}
