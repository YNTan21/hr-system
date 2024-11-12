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
        Schema::table('goals', function (Blueprint $table) {
            Schema::table('goals', function (Blueprint $table) {
                // Add a JSON column to store category score ranges
                $table->json('category_score_ranges')->nullable(); // Store category ranges as JSON
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            Schema::table('goals', function (Blueprint $table) {
                // Drop the category_score_ranges column if rolling back the migration
                $table->dropColumn('category_score_ranges');
            });
        });
    }
};
