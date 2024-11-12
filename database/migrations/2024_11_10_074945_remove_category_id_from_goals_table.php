<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('goals', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['category_id']);  // Drop the foreign key constraint first
    
            // Drop the category_id column
            $table->dropColumn('category_id');  
        });
    }
    
    public function down()
    {
        Schema::table('goals', function (Blueprint $table) {
            // If rolling back, you can add the column back
            $table->unsignedBigInteger('category_id')->nullable();
    
            // Re-create the foreign key constraint if rolling back
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }
    
    
};
