<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            ['title' => 'Thông báo nghỉ lễ', 'content' => 'Công ty sẽ nghỉ lễ vào ngày 2/9.', 'type' => 'news'],
            ['title' => 'Sự kiện team building', 'content' => 'Team building sẽ diễn ra vào tháng 10.', 'type' => 'event'],
            ['title' => 'Thông báo họp toàn công ty', 'content' => 'Họp toàn công ty vào thứ 2 tuần tới.', 'type' => 'news'],
            ['title' => 'Khóa đào tạo kỹ năng', 'content' => 'Khóa đào tạo kỹ năng mềm diễn ra vào tháng 11.', 'type' => 'event'],
            ['title' => 'Thông báo tăng lương', 'content' => 'Công ty sẽ xét tăng lương cuối năm.', 'type' => 'news'],
            ['title' => 'Ngày hội sức khỏe', 'content' => 'Ngày hội sức khỏe được tổ chức vào 20/12.', 'type' => 'event'],
            ['title' => 'Thông báo nghỉ phép', 'content' => 'Nhân viên có thể đăng ký nghỉ phép trước 15/11.', 'type' => 'news'],
            ['title' => 'Cuộc thi nội bộ', 'content' => 'Cuộc thi sáng tạo nội bộ diễn ra tháng tới.', 'type' => 'event'],
            ['title' => 'Cập nhật chính sách mới', 'content' => 'Cập nhật chính sách nghỉ ốm và làm thêm.', 'type' => 'news'],
            ['title' => 'Hội thảo kỹ thuật', 'content' => 'Hội thảo kỹ thuật sẽ diễn ra online vào 25/11.', 'type' => 'event'],
        ];

        DB::table('notifications')->insert($notifications);
    }
}
