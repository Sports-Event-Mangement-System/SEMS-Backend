<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $tournaments = Tournament::all();
        $players = Player::all();
        $teams = Team::all();
        $total_tournaments = $tournaments->count();
        $total_players = $players->count();
        $total_teams = $teams->count();
        $active_tournaments = $tournaments->where('status', 1)->count();
        $active_teams = $teams->where('status', 1)->count();
        $ongoing_tournaments = $tournaments->where('ts_date', '<=', now())->where('te_date', '>=', now())->count();
        $upcoming_tournaments = $tournaments->where('ts_date', '>', now())->count();
        $completed_tournaments = $tournaments->where('te_date', '<', now())->count();

        return response()->json([
            'status' => true,
            'message' => 'Dashboard data fetched successfully',
            'data' => [
                'total_tournaments' => $total_tournaments,
                'total_players' => $total_players,
                'total_teams' => $total_teams,
                'ongoing_tournaments' => $ongoing_tournaments,
                'upcoming_tournaments' => $upcoming_tournaments,
                'completed_tournaments' => $completed_tournaments,
                'active_tournaments' => $active_tournaments,
                'active_teams' => $active_teams,
            ],
        ]);
    }
}
