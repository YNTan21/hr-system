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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('position'); // Remove the original position column
            $table->foreignId('position_id')->nullable()->constrained('positions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['position_id']); // Remove the foreign key constraint
            $table->dropColumn('position_id');    // Remove the position_id column

            $table->string('position')->nullable(); // Restore the original position column
        });
    }
};
