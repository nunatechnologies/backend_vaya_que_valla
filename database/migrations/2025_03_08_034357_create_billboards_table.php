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
        Schema::create('billboards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->unsignedBigInteger('advertiser_id');
            $table->enum('status', ['available', 'reserved','rented','inactive']);
            $table->foreignId('city_id')->constrained();
            $table->foreignId('billboard_structure_id')->constrained();
            $table->enum('entity_status', ['active', 'inactive']);
            $table->string('size');
            $table->decimal('price_per_month', 10, 2);
            $table->text('traffic_data')->nullable();
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);

            $table->foreign('advertiser_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billboards');
    }
};
