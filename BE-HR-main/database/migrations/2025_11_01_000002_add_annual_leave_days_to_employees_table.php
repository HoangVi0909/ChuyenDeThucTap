<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('annual_leave_days')->default(12)->after('position_id'); // Số ngày phép năm còn lại
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('annual_leave_days');
        });
    }
};
