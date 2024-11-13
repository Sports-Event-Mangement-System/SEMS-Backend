<?php

namespace App\Http\Controllers;

use App\Helper\EmailHelper;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Matches;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index() : JsonResponse
    {
        $teams = Team::with('tournament')->get();
        foreach ($teams as $team) {
            $team->logo_urls = url('uploads/teams/' . $team->team_logo);
        }

        return response()->json([
            'data' => $teams,
            'status' => true,
        ]);
    }

    public function store(StoreTeamRequest $request) : JsonResponse
    {
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
        foreach ($players as $key => $player) {
            $player_save = Player::create([
                'team_id' => $team->id,
                'player_name' => $player['player_name'],
                'player_email' => $player['player_email'],
                'is_captain' => $key === 0 ? 1 : 0,
            ]);
        }

        $player_save->save();

        return response()->json([
            'message' => $team->team_name . 'Team Register successfully',
            'team' => $team,
            'status' => true,
        ]);
    }
    public function update(UpdateTeamRequest $request, $id) : JsonResponse
    {
        $validatedData = $request->validated();
        $team = Team::find($id);

        if ($request->hasFile('team_logo')) {
            if (! empty($team->team_logo) && file_exists(public_path('uploads/teams/' . $team->team_logo))) {
                unlink(public_path('uploads/teams/' . $team->team_logo));
            }
            $file = $request->file('team_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/teams'), $filename);
        } elseif (! empty($request->existing_images)) {
            $filename = basename($request->existing_images);
        }

        $team->update([
            'tournament_id' => $validatedData['tournament_id'],
            'team_name' => $validatedData['team_name'],
            'team_logo' => $filename,
            'coach_name' => $validatedData['coach_name'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'player_number' => $validatedData['player_number'],
        ]);
        $players = $request->players;
        foreach ($players as $key => $player) {
            if (! empty($player['player_id'])) {
                $playerfind = Player::findOrFail($player['player_id']);
                $playerfind->update([
                    'team_id' => $team->id,
                    'player_name' => $player['player_name'],
                    'player_email' => $player['player_email'],
                    'is_captain' => $key === 0 ? 1 : 0,
                ]);
            } else {
                $player_save = Player::create([
                    'team_id' => $team->id,
                    'player_name' => $player['player_name'],
                    'player_email' => $player['player_email'],
                    'is_captain' => $key === 0 ? 1 : 0,
                ]);
                $player_save->save();
            }
        }

        return response()->json([
            'message' => 'Team Updated successfully',
            'team' => $team,
            'status' => true,
        ]);
    }

    public function updateStatus(Request $request, $id) : JsonResponse
    {
        $team = Team::find($id);
        $team->status = $request->status;
        $team->save();

        if ($team->status == 1) {

            EmailHelper::TeamActiveMail($team);

        }

        return response()->json([
            'message' => $team->team_name . 'status updated successfully',
            'status' => true,
        ]);
    }

    public function getTeam(Request $request, $id) : JsonResponse
    {
        $team = Team::with('tournament')->findOrFail($id);
        $team->logo_urls = url('uploads/teams/' . $team->team_logo);
        $team['players'] = $team->players;
        $matches = Matches::where('team_id_1', $id)->orWhere('team_id_2', $id)->get();
        $fixture_matches = [];
        $result_matches = [];
        foreach ($matches as $match) {
            $participants = json_decode($match->participants, true);
            $match['participants'] = $participants;
            if ($match['state'] === 'UPCOMING' || $match['state'] === 'SCHEDULED') {
                $fixture_matches[] = $match;
            }
            if ($match['state'] === 'DONE' || $match['state'] === 'SCORE_DONE' || $match['state'] === 'WALK_OVER') {
                $result_matches[] = $match;
            }
        }
        $team['fixture_matches'] = $fixture_matches;
        $team['result_matches'] = $result_matches;
        if( $request->user_id && $team->followers->where('user_id', $request->user_id)->first()){
            $team['is_followed'] = true;
        }else{
            $team['is_followed'] = false;
        }
        return response()->json([
            'team' => $team,
            'status' => true,
        ]);
    }

    public function destroy($id) : JsonResponse
    {
        $team = Team::find($id);
        if ($team) {
            Player::where('team_id', $id)->delete();
            $team->delete();
        }
        return response()->json([
            'message' => 'Team and associated players deleted successfully',
            'status' => true,
        ]);
    }

    public function teamsByTournament($id) : JsonResponse
    {
        $teams = Team::where('tournament_id', $id)->get();
        foreach ($teams as $team) {
            $team->logo_urls = url('uploads/teams/' . $team->team_logo);
        }
        return response()->json([
            'teams' => $teams,
            'status' => true,
        ]);
    }

    public function followTeam(Request $request, $team_id) : JsonResponse
    {
        $team = Team::find($team_id);
        $user = User::where('id', $request->user_id)->first();
        $follower = $team->followers()->where('user_id', $user->id)->first();
        if ($follower) {
            $follower->update([
                'user_email' => $user->email,
                'team_id' => $team_id,
            ]);
        } else {
            $team->followers()->create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'team_id' => $team_id,
            ]);
        }

        return response()->json([
            'message' => 'Team followed successfully',
            'status' => true,
        ]);
    }

}
