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
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['leave_type']);
            $table->renameColumn('placeholder', 'reason');
            $table->renameColumn('leave_type', 'leave_type_id');
            $table->foreign('leave_type_id')->references('id')->on('leave_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['leave_type_id']);
            $table->renameColumn('leave_type_id', 'leave_type');
            $table->renameColumn('reason', 'placeholder');
            $table->foreign('leave_type')->references('id')->on('leave_type')->onDelete('cascade');
        });
    }
};
