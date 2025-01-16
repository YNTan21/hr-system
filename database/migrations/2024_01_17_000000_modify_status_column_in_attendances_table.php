<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyStatusColumnInAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // 先删除旧的 status 列
            $table->dropColumn('status');
            
            // 添加新的 status 列，只包含 on_time 和 late
            $table->enum('status', [
                'on_time',
                'late'
            ])->nullable();
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->string('status')->nullable();
        });
    }
} 