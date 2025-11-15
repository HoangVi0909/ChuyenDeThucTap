<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $feedbacks = [
            'Cần cải thiện môi trường làm việc.',
            'Đề xuất tăng lương.',
            'Yêu cầu trang thiết bị làm việc mới.',
            'Mong có thêm khóa đào tạo kỹ năng.',
            'Cần cải thiện quy trình nội bộ.',
            'Đề xuất tổ chức nhiều hoạt động team building hơn.',
            'Yêu cầu nâng cấp phần mềm công ty.',
            'Cần cải thiện chế độ phúc lợi.',
            'Mong được phản hồi nhanh hơn từ quản lý.',
            'Đề xuất thay đổi giờ làm việc linh hoạt.'
        ];

        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'employee_id' => $i + 1, // gán lần lượt cho 10 nhân viên đầu
                'content' => $feedbacks[$i],
            ];
        }

        DB::table('feedback')->insert($data);
    }
}
