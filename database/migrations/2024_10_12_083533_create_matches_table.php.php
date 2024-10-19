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
            $table->integer('match_id');
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->foreignId('team_id_1')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('team_id_2')->nullable()->constrained('teams')->onDelete('cascade');
            $table->string('name');
            $table->integer('nextMatchId')->nullable();
            $table->integer('nextLooserMatchId')->nullable();
            $table->string('startTime')->nullable();
            $table->string('tournamentRoundText')->nullable();
            $table->longText('participants')->nullable();
            $table->string('match_winner')->nullable();
            $table->string('match_looser')->nullable();
            $table->enum('state', ['NO_SHOW', 'WALK_OVER', 'NO_PARTY', 'DONE', 'SCORE_DONE', 'UPCOMING']);
            $table->text('match_report')->nullable();
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
