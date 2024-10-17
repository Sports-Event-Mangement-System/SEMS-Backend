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
        // Add any other fields you want to be mass-assignable
    ];

    protected $casts = [
        'response_data' => 'array',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }
}
