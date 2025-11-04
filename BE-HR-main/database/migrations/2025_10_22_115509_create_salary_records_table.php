<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salary_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('month_year', 7); // Format: 2025-10
            $table->decimal('total_hours', 8, 2)->default(0); // Tổng số giờ làm trong tháng (từ work_schedules)
            $table->decimal('hourly_rate', 10, 2)->default(0); // Lương theo giờ
            $table->decimal('position_allowance', 12, 2)->default(0); // Phụ cấp chức vụ
            $table->decimal('bonus', 12, 2)->default(0); // Thưởng
            $table->decimal('penalty', 12, 2)->default(0); // Phạt
            $table->decimal('base_salary', 15, 2)->default(0); // Lương cơ bản (total_hours * hourly_rate)
            $table->decimal('total_salary', 15, 2)->default(0); // Tổng lương (base + allowance + bonus - penalty)
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft'); // Trạng thái
            $table->text('notes')->nullable(); // Ghi chú
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->unique(['employee_id', 'month_year']); // Mỗi nhân viên chỉ có 1 bản ghi lương/tháng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_records');
    }
};
