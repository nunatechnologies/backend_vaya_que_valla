<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billboard_faces', function (Blueprint $table) {
            $table->string('name')->after('face');
            $table->string('location')->after('name');
            $table->foreignId('zone_id')->nullable()->default(null)->after('location')->constrained();
            $table->unsignedBigInteger('advertiser_id')->nullable()->default(null)->after('zone_id');
            $table->foreign('advertiser_id')->references('id')->on('users');
            $table->foreignId('city_id')->nullable()->default(null)->after('advertiser_id')->constrained();
            $table->foreignId('billboard_structure_id')->nullable()->default(null)->after('city_id')->constrained();
            $table->enum('entity_status', ['active', 'inactive'])->after('billboard_structure_id');
            $table->string('size')->after('entity_status');
            $table->decimal('price_per_month', 10, 2)->after('size');
            $table->text('traffic_data')->nullable()->after('price_per_month');
            $table->decimal('longitude', 10, 7)->after('traffic_data');
            $table->decimal('latitude', 10, 7)->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('billboard_faces', function (Blueprint $table) {
            $table->dropForeign(['advertiser_id']);
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['billboard_structure_id']);

            $table->dropColumn([
                'name',
                'location',
                'zone_id',
                'advertiser_id',
                'city_id',
                'billboard_structure_id',
                'entity_status',
                'size',
                'price_per_month',
                'traffic_data',
                'longitude',
                'latitude',
            ]);
        });
    }
};
