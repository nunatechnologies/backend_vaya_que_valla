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
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name',30)->nullable()->after('name');
            $table->enum('user_type', ['PERSON','ORGANIZATION'])->nullable()->after('password');
            $table->string('cod_phone',5)->nullable()->after('user_type');
            $table->string('phone',15)->nullable()->after('cod_phone');
            $table->string('username',30)->nullable()->after('phone');
            $table->string('profile_image')->nullable()->after('username');
            $table->enum('entity_status',['active','inactive'])->default('active')->after('profile_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type','code_phone','phone','username','profile_image','entity_status']);
        });
    }
};
