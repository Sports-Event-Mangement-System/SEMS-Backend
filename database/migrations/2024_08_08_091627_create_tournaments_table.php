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
            $table->string('t_description');
            $table->string('t_logo');
            $table->string('prize_pool')->nullable();
            $table->enum('t_type',['indoor','outdoor'])->default('outdoor');
            $table->timestamp('ts_date')->nullable();
            $table->timestamp('te_date')->nullable();
            $table->timestamp('rs_date')->nullable();
            $table->timestamp('re_date')->nullable();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('address');
            $table->boolean('status');
            $table->string('team_number');
            $table->boolean('featured');
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
