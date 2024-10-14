<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = "schedules";

    protected $guarded = ['id'];

    public function Team()
    {
        return $this->hasMany(Team::class, 'team_id');
    }

    public function Tournaments()
    {
        return $this->hasMany(Tournament::class, 'tournament_id');
    }
}
