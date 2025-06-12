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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('game_id')->index();
            $table->unsignedTinyInteger('color')->default(0);
            $table->string('figures',8);
            $table->unsignedSmallInteger('score')->default(0);
            $table->unsignedTinyInteger('skills')->default(0);
            $table->unsignedTinyInteger('combo')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
