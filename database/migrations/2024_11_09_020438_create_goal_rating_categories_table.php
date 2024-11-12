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
        Schema::create('goal_rating_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('goals'); // Link to goal
            $table->foreignId('rating_category_id')->constrained('rating_categories'); // Link to rating category
            $table->integer('min_score'); // Minimum score for the rating category in this goal
            $table->integer('max_score'); // Maximum score for the rating category in this goal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_rating_categories');
    }
};
