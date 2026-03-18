<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billboard_faces', function (Blueprint $table) {
            $table->string('approval_status', 20)->default('approved')->after('entity_status');
            // approved = visible y operativa
            // pending = creada por proveedor, pendiente de aprobacion admin
            // rejected = rechazada por admin
        });
    }

    public function down(): void
    {
        Schema::table('billboard_faces', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }
};
