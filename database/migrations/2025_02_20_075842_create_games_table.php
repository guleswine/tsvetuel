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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            //$table->integer('user_id')->index();
            //$table->integer('party')->nullable();
            //$table->string('forms')->nullable();
            $table->jsonb('field')->nullable();
            $table->unsignedBigInteger('move_user_id');
            $table->unsignedTinyInteger('state')->default(0);
            $table->unsignedTinyInteger('used_skills')->default(0);
            $table->float('version')->default(0 );
            //$table->string('skills')->nullable();
            //$table->integer('player_type')->nullable();
            //$table->integer('score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
