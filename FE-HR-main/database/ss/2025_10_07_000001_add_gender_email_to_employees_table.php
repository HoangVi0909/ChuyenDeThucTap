<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('gender', 10)->nullable()->after('name');
            $table->string('email')->unique()->nullable()->after('gender');
        });
    }

    public function down() {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['gender', 'email']);
        });
    }
};
