<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;

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

        $starting_this_month_tournaments = $tournaments->where('ts_date', '>=', now()->startOfMonth());
        foreach ($starting_this_month_tournaments as $starting_this_month_tournament) {
            $tournament_name = $starting_this_month_tournament->t_name;
            $st_tournmanet_this_month = array(
                'event_name' => $tournament_name .' Tourament Starting this month',
                'event_date' => $starting_this_month_tournament->ts_date,
            );
        }
        $registrations_started_this_month = $tournaments->where('rs_date', '>=', now()->startOfMonth());
        foreach ($registrations_started_this_month as $registration_starting_this_month) {
            $tournament_name = $registration_starting_this_month->t_name;
            $rs_tournmanet_this_month = array(
                'event_name' => $tournament_name .' Tournament Registrations started',
                'event_date' => $registration_starting_this_month->rs_date,
            );
        }
        $registrations_ended_this_month = $tournaments->where('re_date', '<=', now()->endOfMonth());
        foreach ($registrations_ended_this_month as $registration_ended_this_month) {
            $tournament_name = $registration_ended_this_month->t_name;
            $re_tournmanet_this_month = array(
                'event_name' => $tournament_name .' Tournament Registrations ended',
                'event_date' => $registration_ended_this_month->re_date,
            );
        }
        $events = array(
            $st_tournmanet_this_month ?? null,
            $rs_tournmanet_this_month ?? null,
            $re_tournmanet_this_month ?? null,
        );

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
                'events' => $events,
            ],
        ]);
    }
}
