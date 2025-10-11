<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            ['name' => 'Phòng Nhân sự'],
            ['name' => 'Phòng Kế toán'],
            ['name' => 'Phòng IT'],
        ]);
    }
}
