<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Team extends Model
{
    use HasFactory;

    protected $table = "teams";

    protected $guarded = ['id'];

    public function players()
    {
        return $this->hasMany(Player::class);
    }
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
    public function matches()
    {
        return $this->hasMany(Matches::class);
    }
    public function followers()
    {
        return $this->hasMany(Follower::class, 'team_id');
    }
}
