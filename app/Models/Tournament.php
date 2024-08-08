<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $table = "tournaments";
    protected $guarded=[
        'id'
    ];
    use HasFactory;
}
