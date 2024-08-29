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
            $table->enum('t_type',['indoor','outdoor'])->default('outdoor');
            $table->timestamps('ts_date');
            $table->timestamps('te_date');
            $table->timestamps('rs_date');
            $table->timestamps('re_date');
            $table->string('phone_number');
            $table->string('email');
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
