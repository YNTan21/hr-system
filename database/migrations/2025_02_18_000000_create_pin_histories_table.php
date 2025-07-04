<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pin_histories', function (Blueprint $table) {
            $table->id();
            $table->string('pin'); // hashed
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pin_histories');
    }
}; 