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
            ['name' => 'Nhân viên'],
            ['name' => 'Thực tập sinh'],
        ]);
    }
}
