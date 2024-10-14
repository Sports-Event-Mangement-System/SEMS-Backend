<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('team')->get();
        return response()->json([
            'data' => $players,
            'status' => true,
        ]);
    }
}
