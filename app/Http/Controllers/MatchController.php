<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\TiesheetResponse;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{

    /**
     * Save matches to the database
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tournaments = Tournament::with(['matches', 'teams'])->get();
        $tournaments = $tournaments->map(function ($tournament) {
            $tournament->image_url = $tournament->t_images ? url('uploads/tournaments/' . $tournament->t_images[0]) : null;
            $tournament->matches = $tournament->matches->map(function ($match) use ($tournament) {
                if ($match->participants) {
                    $participants = json_decode($match->participants, true);
                    $updatedParticipants = [];
                    foreach ($participants as $participant) {
                        $team = $tournament->teams->firstWhere('id', $participant['id']);
                        if ($team) {
                            $participant['logo_url'] = url('uploads/teams/' . $team->team_logo);
                        }
                        $updatedParticipants[] = $participant;
                    }
                    $match->participants = $updatedParticipants;
                }
                return $match;
            });
            return $tournament;
        });
        return response()->json([
            'status' => true,
            'message' => 'Matches fetched successfully',
            'tournaments' => $tournaments,
        ]);
    }

    /**
     * Save matches to the database
     *
     * @param  Request  $request
     * @param  int  $tournament_id
     * @return JsonResponse
     */
    public function saveMatches(Request $request, int $tournament_id): JsonResponse
    {
        $matches = $request->matches;
        foreach ($matches as $match) {
            // Check if the match already exists in the database to avoid duplicate creation
            $existingMatch = Matches::where('tournament_id', $tournament_id)
                ->where('match_id', $match['id'])
                ->first();

            if (!$existingMatch) {
                // Prepare database match data
                $match_db_data = [
                    'tournament_id' => $tournament_id,
                    'name' => ($match['state'] == 'WALK_OVER' ? 'Walkover Match ' : 'Round '.$match['tournamentRoundText'].' Match ').$match['id'],
                    'nextMatchId' => $match['nextMatchId'],
                    'nextLooserMatchId' => null,
                    'tournamentRoundText' => (string) ceil($match['id'] / 2),
                    'startTime' => '',
                    'state' => $match['state'],
                    'participants' => $match['state'] == 'WALK_OVER' ? json_encode([$match['participants'][0]]) : json_encode($match['participants']),
                    'match_id' => $match['id'],
                ];

                $match_db_data['team_id_1'] = $match['participants'][0]['id'];
                isset( $match_db_data['team_id_2'] ) ? $match_db_data['team_id_2'] = $match['participants'][1]['id']: $match_db_data['team_id_2'] = null;

                // Save match data to the database
                Matches::create($match_db_data);
            }
        }
        TiesheetResponse::updateOrCreate(
            ['tournament_id' => $tournament_id],
            ['response_data' => $matches]
        );
        return response()->json([
            'status' => true,
            'message' => 'Matches saved successfully',
        ]);
    }

    /**
     * Get match response
     *
     * @param  int  $id it is tournament Id
     * @return JsonResponse
     */
    public function getTiesheetResponse(int $id): JsonResponse
    {
        $matchResponse = TiesheetResponse::where('tournament_id', $id)->first();
        $showTiesheet = $matchResponse ? true : false;
        $matchResponse = $matchResponse ? $matchResponse->response_data : [];
        return response()->json([
            'status' => true,
            'message' => 'Tournament response fetched successfully',
            'data' => $matchResponse,
            'showTiesheet' => $showTiesheet,
        ]);
    }

    /**
     * Delete tiesheet
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function deleteTiesheet(int $id): JsonResponse
    {
        // Delete the tiesheet response
        TiesheetResponse::where('tournament_id', $id)->delete();

        // Delete related matches
        Matches::where('tournament_id', $id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Tiesheet and related matches deleted successfully',
        ]);
    }

}
