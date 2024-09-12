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
        Schema::dropIfExists('leaves');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->id();
            $table->unsignedBigInteger('employee_id'); 
            $table->foreign('employee_id')->references('id')->on('users');
            $table->unsignedBigInteger('leave_type'); 
            $table->foreign('leave_type')->references('id')->on('leave_types');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('number_of_days');
            $table->text('placeholder')->nullable();
            $table->timestamps();
    }
};
