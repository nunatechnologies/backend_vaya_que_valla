<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('space_type', 20)->nullable()->after('status')
                ->comment('billboard o digital');
            $table->string('project_type', 30)->nullable()->after('space_type')
                ->comment('standard_print, special_project, video_quote, advisory');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['space_type', 'project_type']);
        });
    }
};
