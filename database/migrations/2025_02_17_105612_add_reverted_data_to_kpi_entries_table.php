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
            $table->text('reverted_actual_result')->nullable();
            $table->decimal('reverted_actual_score', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_entry', function (Blueprint $table) {
            $table->dropColumn(['reverted_actual_result', 'reverted_actual_score']);
        });
    }
};
