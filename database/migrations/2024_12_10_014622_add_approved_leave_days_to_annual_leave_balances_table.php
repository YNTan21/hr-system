<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedLeaveDaysToAnnualLeaveBalancesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('annual_leave_balances', function (Blueprint $table) {
            $table->integer('approved_leave_days')->default(0); // Column to track approved leave days
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annual_leave_balances', function (Blueprint $table) {
            $table->dropColumn('approved_leave_days');
        });
    }
}