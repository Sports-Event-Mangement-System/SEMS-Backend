<?php

namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Requests\StoreTeamRequest;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(): JsonResponse
    {
        $teams = Team::all();
        foreach ($teams as $team) {
            $team->logo_urls = ImageHelper::generateImageUrls($team->team_logo);
        }

        return response()->json([
            'data' => $teams,
            'status' => true
        ]);
    }

    public function store(StoreTeamRequest $request): JsonResponse
    {

        $all_data = $request->all();
        $validatedData = $request->validated();
        // Handle file uploads
        if ($request->hasFile('team_logo')) {
            $file = $request->file('team_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/teams'), $filename);
        }

        // Create the tournament
        $team = Team::create([
            'tournament_id' => $validatedData['tournament_id'],
            'team_name' => $validatedData['team_name'],
            'team_logo' => $filename,
            'coach_name' => $validatedData['coach_name'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'player_number' => $validatedData['player_number'],
        ]);

        $team->save();
        $players = $request->players;
        foreach( $players as $key => $player ){
            $player_save = Player::create([
                'team_id' => $team->id,
                'player_name' => $player['player_name'],
                'player_email' => $player['player_email'],
                'is_captain' => $key==0 ? 1 : 0,
            ]);
        }

        $player_save->save();

        return response()->json([
            'message' => $team->team_name . 'Team Register successfully',
            'team' => $team,
            'status' => true
        ]);
    }


}
