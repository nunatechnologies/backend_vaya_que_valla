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
        Schema::create('billboard_faces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billboard_id')->constrained();
            $table->enum('face', ['A', 'B', 'C', 'D']);
            $table->string('location_detail'); // ie: "Face see to avenue Beni"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billboard_faces');
    }
};
