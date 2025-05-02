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
        Schema::table('billboard_faces', function (Blueprint $table) {
            $table->date('rented_from')->nullable()->after('status');
            $table->date('available_from')->nullable()->after('rented_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billboard_faces', function (Blueprint $table) {
            $table->dropColumn(['rented_from','available_from']);
        });
    }
};
