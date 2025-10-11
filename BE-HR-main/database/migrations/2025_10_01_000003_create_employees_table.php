<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender', 10)->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('photo')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('cccd', 20)->unique();
            $table->string('qualification')->nullable();
            $table->string('phone', 20)->nullable();
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
