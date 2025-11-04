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
        Schema::create('timekeeping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('work_date'); // Ngày làm việc
            $table->time('check_in')->nullable(); // Giờ vào
            $table->time('check_out')->nullable(); // Giờ ra
            $table->decimal('hours_worked', 8, 2)->default(0); // Số giờ làm việc
            $table->decimal('overtime_hours', 8, 2)->default(0); // Giờ tăng ca
            $table->enum('status', ['present', 'absent', 'late', 'early_leave', 'holiday'])->default('present');
            $table->text('notes')->nullable(); // Ghi chú
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->unique(['employee_id', 'work_date']); // Mỗi nhân viên chỉ có 1 bản ghi/ngày
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timekeeping');
    }
};
