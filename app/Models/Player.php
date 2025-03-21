<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $table = "players";
    protected $guarded = ['id'];

    public function Team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
