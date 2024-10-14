<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory;
    protected $table = "matches";

    protected $guarded = ['id'];

    public function Team()
    {
        return $this->hasMany(Team::class, 'team_id');
    }

    public function Tournaments()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }
}
