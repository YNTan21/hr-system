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
        Schema::create('attendance_schedules', function (Blueprint $table) {
            $table->id(); // 主键
            $table->unsignedBigInteger('schedules_id'); // 关联 Schedule 表的 ID
            $table->unsignedBigInteger('user_id'); // 关联 User 表的 ID
            $table->date('date'); // 日期
            $table->dateTime('clock_in')->nullable(); // Clock In 时间
            $table->dateTime('clock_out')->nullable(); // Clock Out 时间
            $table->enum('status', ['on_time', 'late', 'absent'])->default('absent'); // 出勤状态
            $table->decimal('overtime_hour', 5, 2)->default(0); // 加班小时数
            $table->timestamps(); // 创建和更新时间

            // 外键约束
            $table->foreign('schedules_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_schedules');
    }
};
