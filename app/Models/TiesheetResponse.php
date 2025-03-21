<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiesheetResponse extends Model
{
    use HasFactory;
    protected $table = 'tiesheet_responses';

    protected $fillable = [
        'tournament_id',
        'response_data',
        'points_table',
    ];

    protected $casts = [
        'response_data' => 'array',
        'points_table' => 'array',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }
}
