<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notifications')->insert([
            [
                'title' => 'Thông báo nghỉ lễ',
                'content' => 'Công ty sẽ nghỉ lễ vào ngày 2/9.',
                'type' => 'news',
            ],
            [
                'title' => 'Sự kiện team building',
                'content' => 'Team building sẽ diễn ra vào tháng 10.',
                'type' => 'event',
            ],
        ]);
    }
}
