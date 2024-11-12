<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->unsignedBigInteger('position_id')->after('id');  // Add position_id field
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');  // Foreign key
        });
    }

    public function down()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};
