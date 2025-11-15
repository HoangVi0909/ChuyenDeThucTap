<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('positions')->insert([
            ['name' => 'Trưởng phòng'],
            ['name' => 'Phó phòng'],
            ['name' => 'Nhân viên'],
            ['name' => 'Nhân viên cao cấp'],
            ['name' => 'Thực tập sinh'],
            ['name' => 'Quản lý dự án'],
            ['name' => 'Trợ lý'],
        ]);
    }
}
