<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void 
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('company',40)->nullable();
            $table->enum('status',['pending','approved','in_progress','rejected']);
            $table->longText('description', 300)->nullable();
            $table->longText('budget_description', 300)->nullable();
            $table->date('tentative_start_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void 
    {
        Schema::dropIfExists('requests');
    }
};
