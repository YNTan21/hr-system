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
        Schema::table('kpi_entry', function (Blueprint $table) {
            // 1. 先删除外键约束
            $table->dropForeign('kpi_entry_goals_id_foreign');
            $table->dropForeign('kpi_entry_users_id_foreign');
            
            // 2. 删除唯一索引
            $table->dropUnique('kpi_unique_entry');
            
            // 3. 重新添加外键约束
            $table->foreign('goals_id')
                  ->references('id')
                  ->on('goals')
                  ->onDelete('cascade')
                  ->onUpdate('restrict');
                  
            $table->foreign('users_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_entry', function (Blueprint $table) {
            // 1. 删除外键约束
            $table->dropForeign('kpi_entry_goals_id_foreign');
            $table->dropForeign('kpi_entry_users_id_foreign');
            
            // 2. 重新添加唯一索引
            $table->unique(['users_id', 'goals_id', 'month', 'year'], 'kpi_unique_entry');
            
            // 3. 重新添加外键约束
            $table->foreign('goals_id')
                  ->references('id')
                  ->on('goals')
                  ->onDelete('cascade')
                  ->onUpdate('restrict');
                  
            $table->foreign('users_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('restrict');
        });
    }
};
