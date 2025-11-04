<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Người nhận (employee hoặc admin)
            $table->string('user_type')->nullable(); // 'employee' hoặc 'admin'
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable(); // leave, salary, ...
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('notifications');
    }
};