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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->OnDelete('cascade');
            $table->string('team_name');
            $table->string('team_logo')->nullable();
            $table->string('coach_name');
            $table->string('phone_number')  ;
            $table->string('email')->nullable();
            $table->string('address');
            $table->boolean('status')->default(0);
            $table->integer('player_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
