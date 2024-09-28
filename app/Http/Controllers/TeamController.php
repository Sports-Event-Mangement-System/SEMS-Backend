<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(): JsonResponse
    {
        $teams = Team::all();
        return response()->json([
            'data' => $teams,
            'status' => true
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validated();
        // Handle file uploads
        $filenames = [];
        if ($request->hasFile('team_logo')) {
            foreach ($request->file('team_logo') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/teams'), $filename);
                $filenames[] = $filename;
            }
        }

        // Create the tournament
        $team = Team::create([
            'tournament_id' => $validatedData['tournament_id'],
            'team_name' => $validatedData['team_name'],
            'team_logo' => $filenames,
            'coach_name' => $validatedData['coach_name'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'status' => $validatedData['status'],
            'player_number' => $validatedData['player_number'],
        ]);

        return response()->json([
            'message' => 'Team Register successfully',
            'team' => $team,
            'status' => true
        ]);
    }


}
