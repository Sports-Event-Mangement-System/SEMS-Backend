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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('t_name');
            $table->string('t_description', 1500);
            $table->string('t_images')->nullable();
            $table->string('prize_pool')->nullable();
            $table->date('ts_date')->nullable();
            $table->date('te_date')->nullable();
            $table->date('rs_date')->nullable();
            $table->date('re_date')->nullable();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('address');
            $table->boolean('status');
            $table->boolean('featured');
            $table->enum('tournament_type',['round-robin','single-elimination'])->default('single-elimination');
            $table->string('max_teams')->nullable();
            $table->string('min_teams')->nullable();
            $table->string('max_players_per_team')->nullable();
            $table->string('min_players_per_team')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
