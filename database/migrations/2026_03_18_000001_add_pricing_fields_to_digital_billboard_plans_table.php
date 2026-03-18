<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('digital_billboard_plans', function (Blueprint $table) {
            $table->decimal('price_per_month', 10, 2)->default(0)->after('passes_per_hour');
            $table->unsignedInteger('seconds_per_day')->default(0)->after('price_per_month');
            $table->unsignedInteger('max_videos')->default(1)->after('seconds_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('digital_billboard_plans', function (Blueprint $table) {
            $table->dropColumn(['price_per_month', 'seconds_per_day', 'max_videos']);
        });
    }
};
