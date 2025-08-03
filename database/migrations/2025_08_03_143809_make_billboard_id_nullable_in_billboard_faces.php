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
            // Primero, eliminamos la restricción foreign key si existe
            $table->dropForeign(['billboard_id']);

            // Luego hacemos que el campo sea nullable
            $table->foreignId('billboard_id')
                  ->nullable()
                  ->change();

            // Volvemos a aplicar la foreign key
            $table->foreign('billboard_id')->references('id')->on('billboards');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billboard_faces', function (Blueprint $table) {
            $table->dropForeign(['billboard_id']);

            $table->foreignId('billboard_id')
                  ->nullable(false)
                  ->change();

            $table->foreign('billboard_id')->references('id')->on('billboards');
        });
    }

};
