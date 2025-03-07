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
            $table->dropColumn('status');
            $table->enum('status', ['pending', 'approved', 'reverted'])->default('pending');
            $table->timestamp('reverted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_entry', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->dropColumn('reverted_at');
        });
    }
};
