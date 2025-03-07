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
        Schema::table('leave_type', function (Blueprint $table) {
            $table->string('color')->default('#3b82f6'); // 默认蓝色
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_type', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
