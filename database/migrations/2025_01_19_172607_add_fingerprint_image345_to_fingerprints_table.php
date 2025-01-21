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
        Schema::table('fingerprints', function (Blueprint $table) {
            $table->string('fingerprint_image3')->after('fingerprint_image2');
            $table->string('fingerprint_image4')->after('fingerprint_image3');
            $table->string('fingerprint_image5')->after('fingerprint_image4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fingerprints', function (Blueprint $table) {
            $table->dropColumn([
                'fingerprint_image3',
                'fingerprint_image4',
                'fingerprint_image5'
            ]);
        });
    }
};
