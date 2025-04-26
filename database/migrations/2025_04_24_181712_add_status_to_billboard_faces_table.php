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
            $table->enum('status',['ROJO','AMARILLO','VERDE'])->default('VERDE')->after('location_detail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billboard_faces', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
