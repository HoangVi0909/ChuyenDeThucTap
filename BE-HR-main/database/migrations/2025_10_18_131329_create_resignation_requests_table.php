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
        Schema::create('resignation_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->text('reason'); // Lý do nghỉ việc
            $table->date('expected_resignation_date'); // Ngày nghỉ dự kiến
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Trạng thái
            $table->text('admin_note')->nullable(); // Ghi chú của admin
            $table->unsignedBigInteger('reviewed_by')->nullable(); // ID admin duyệt
            $table->timestamp('reviewed_at')->nullable(); // Thời gian duyệt
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('reviewed_by')->references('id')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resignation_requests');
    }
};
