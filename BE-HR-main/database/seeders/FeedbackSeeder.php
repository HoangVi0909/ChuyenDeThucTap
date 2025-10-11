<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('feedback')->insert([
            [
                'employee_id' => 1,
                'content' => 'Cần cải thiện môi trường làm việc.',
            ],
            [
                'employee_id' => 2,
                'content' => 'Đề xuất tăng lương.',
            ],
        ]);
    }
}
