<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kpi_entry', function (Blueprint $table) {
            $table->integer('month')->after('final_score');
            $table->integer('year')->after('month');
            
            // Add unique constraint to prevent duplicate entries
            $table->unique(['users_id', 'goals_id', 'month', 'year'], 'kpi_unique_entry');
        });
    }

    public function down()
    {
        Schema::table('kpi_entry', function (Blueprint $table) {
            $table->dropUnique('kpi_unique_entry');
            $table->dropColumn(['month', 'year']);
        });
    }
};