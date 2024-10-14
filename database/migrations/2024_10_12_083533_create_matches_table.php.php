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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->OnDelete('cascade');
            $table->foreignId('team_id_1')->constrained('teams')->OnDelete('cascade');
            $table->foreignId('team_id_2')->constrained('teams')->OnDelete('cascade');
            $table->string('match_name');
            $table->string('match_date');
            $table->string('match_time');
            $table->string('match_venue');
            $table->string('match_result')->nullable();
            $table->string('match_winner')->nullable();
            $table->string('match_looser')->nullable();
            $table->string('match_status')->default('pending');
            $table->string('match_report')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
