<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_records', function (Blueprint $table) {
            $table->decimal('tax_amount', 15, 2)->default(0)->after('total_salary');
        });
    }

    public function down(): void
    {
        Schema::table('salary_records', function (Blueprint $table) {
            $table->dropColumn('tax_amount');
        });
    }
};
