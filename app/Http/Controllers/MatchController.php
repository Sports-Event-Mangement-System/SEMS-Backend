<?php

namespace App\Http\Controllers;

use App\Helper\EmailHelper;
use App\Helper\MatchHelper;
use App\Http\Requests\UpdateMatchRequest;
use App\Models\Matches;
use App\Models\Team;
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
    public function index() : JsonResponse
    {
        $tournaments = Tournament::with(['matches', 'teams'])->get();
        $tournaments = $tournaments->map(function ($tournament) {
            $tournament->image_url = $tournament->t_images ? url('uploads/tournaments/' . $tournament->t_images[0]) : null;
            $tournament->matches = $tournament->matches->map(function ($match) use ($tournament) {
                $match->participants = MatchHelper::processParticipants($match, $tournament);
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
    public function saveMatches(Request $request, int $tournament_id) : JsonResponse
    {
        $matches = $request->matches;
        foreach ($matches as $match) {
            // Check if the match already exists in the database to avoid duplicate creation
            $existingMatch = Matches::where('tournament_id', $tournament_id)
                ->where('match_id', $match['id'])
                ->first();

            if (! $existingMatch) {
                // Prepare database match data
                $match_db_data = [
                    'tournament_id' => $tournament_id,
                    'name' => ($match['state'] == 'WALK_OVER' ? 'Walkover Match ' : 'Round ' . $match['tournamentRoundText'] . ' Match ') . $match['id'],
                    'nextMatchId' => $match['nextMatchId'],
                    'nextLooserMatchId' => null,
                    'tournamentRoundText' => (string) ceil($match['id'] / 2),
                    'startTime' => '',
                    'state' => $match['state'],
                    'participants' => $match['state'] == 'WALK_OVER' ? json_encode([$match['participants'][0]]) : json_encode($match['participants']),
                    'match_id' => $match['id'],
                ];

                $match_db_data['team_id_1'] = $match['participants'][0]['id'];
                if (isset($match['participants'][1]['id'])) {
                    $match_db_data['team_id_2'] = $match['participants'][1]['id'];
                }

                // Save match data to the database
                Matches::create($match_db_data);
                // EmailHelper::MatchScheduleMail((object) $match_db_data);
            }
        }
        TiesheetResponse::updateOrCreate(
            ['tournament_id' => $tournament_id],
            [
                'tournament_id' => $tournament_id,
                'response_data' => $matches,
                'points_table' => $request->pointsTableData ? $request->pointsTableData : [],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'Matches saved successfully',
        ]);
    }

    /**
     * Get match response
     *
     * @param  int  $id  it is tournament Id
     * @return JsonResponse
     */
    public function getTiesheetResponse(int $id) : JsonResponse
    {
        $tiesheetResponse = TiesheetResponse::where('tournament_id', $id)->first();
        $showTiesheet = $tiesheetResponse ? true : false;
        $matchResponse = $tiesheetResponse->response_data ?? [];
        $pointsTable = $tiesheetResponse->points_table ?? [];
        $max_rounds = is_array(end($matchResponse)) ? end($matchResponse)['round'] : '';
        if ($tiesheetResponse) {
            return response()->json([
                'status' => true,
                'message' => 'Tiehseet fetched successfully',
                'matches' => $matchResponse,
                'points_table' => $pointsTable,
                'max_rounds' => $max_rounds,
                'showTiesheet' => $showTiesheet,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Tiesheetnot found',
        ]);
    }

    /**
     * Delete tiesheet
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function deleteTiesheet(int $id) : JsonResponse
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

    /**
     * Get match details
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function getMatchDetails(int $id) : JsonResponse
    {
        $match = Matches::where('id', $id)->first();
        if (! $match) {
            return response()->json([
                'status' => false,
                'message' => 'Match not found',
            ], 404);
        }
        $tournament = Tournament::with('teams')->find($match->tournament_id);
        $match->tournament_name = $tournament->t_name;
        $match->participants = MatchHelper::processParticipants($match, $tournament);
        return response()->json([
            'status' => true,
            'message' => 'Match details fetched successfully',
            'data' => $match,
        ]);
    }

    /**
     * Update matches
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function updateMatch(UpdateMatchRequest $request, int $id) : JsonResponse
    {
        $match = Matches::findOrFail($id);
        $winner_team = Team::find($request->matchWinner);

        $participants = json_decode($match->participants, true);

        $isTeam1Winner = $request->matchWinner === $match->team_id_1;
        $participants[0]['isWinner'] = $isTeam1Winner;
        $participants[1]['isWinner'] = ! $isTeam1Winner;

        // Update result texts
        $participants[0]['resultText'] = $request->team1ResultText ?? null;
        $participants[1]['resultText'] = $request->team2ResultText ?? null;


        // Update next match participants Only if the match is not a final match
        if ($match->nextMatchId !== null) {
            $next_match = Matches::where('match_id', $match->nextMatchId)->where('tournament_id', $match->tournament_id)->first();
            $similar_matches = Matches::where('nextMatchId', $match->nextMatchId)->get();

            foreach ($similar_matches as $index => $similar_match) {
                if ($match->match_id === $similar_match->match_id) {
                    $next_match_participants = json_decode($next_match->participants, true);

                    if ($request->matchWinner !== null) {
                        $next_match_participants[$index]['id'] = $winner_team->id;
                        $next_match_participants[$index]['name'] = $winner_team->team_name;
                        $next_match_participants[$index]['teamLogo'] = url('uploads/teams/' . $winner_team->team_logo);
                    } else {
                        $next_match_participants[$index]['id'] = null;
                        $next_match_participants[$index]['name'] = null;
                    }

                    // Update the next match's team slots
                    if ($index === 0) {
                        $next_match->team_id_1 = $request->matchWinner !== null ? $winner_team->id : null;
                    } elseif ($index === 1) {
                        $next_match->team_id_2 = $request->matchWinner !== null ? $winner_team->id : null;
                    }

                    $next_match->update([
                        'team_id_1' => $next_match->team_id_1 ?? null,
                        'team_id_2' => $next_match->team_id_2 ?? null,
                        'participants' => json_encode($next_match_participants),
                    ]);
                }
            }
        }
        // Update tiesheet and points table
        $tiesheet_response = TiesheetResponse::where('tournament_id', $match->tournament_id)->first();
        if ($tiesheet_response) {
            $response_data = $tiesheet_response->response_data;
            $points_table = $tiesheet_response->points_table;

            if ($points_table) {
                $points_table = $this->updatePointsTable(
                    $points_table,
                    $participants,
                    $match->match_winner,
                    $request->matchWinner
                );
            }

            // Update response data
            foreach ($response_data as &$response_match) {
                if ($response_match['id'] == $match->match_id) {
                    $response_match['startTime'] = $request->startTime;
                    $response_match['state'] = $request->state;
                    $response_match['participants'] = $participants;
                }
                if ($match->nextMatchId !== null && $response_match['id'] == $next_match->match_id) {
                    $response_match['participants'] = $next_match_participants;
                }
            }

            $tiesheet_response->update([
                'response_data' => $response_data,
                'points_table' => $points_table
            ]);
        }
        // Update match details
        $match->update([
            'startTime' => $request->startTime,
            'match_winner' => $request->matchWinner ?? null,
            'participants' => json_encode($participants),
            'state' => $request->state,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Match updated successfully',
        ]);
    }


    /**
     * Update points table
     *
     * @param  array  $points_table
     * @param  array  $participants
     * @param  mixed  $previousWinner
     * @param  mixed  $newWinner
     * @return array
     */
    private function updatePointsTable(array $points_table, array $participants, $previousWinner, $newWinner) : array
    {
        $isNewResult = $previousWinner !== $newWinner;

        foreach ($points_table as &$team) {
            if ($team['id'] === $participants[0]['id'] || $team['id'] === $participants[1]['id']) {
                if ($isNewResult) {
                    $this->revertPreviousResult($team, $previousWinner);
                    $this->applyNewResult($team, $newWinner);
                }
            }
        }

        return $this->sortPointsTable($points_table);
    }

    /**
     * Revert previous result
     *
     * @param  array  &$team
     * @param  mixed  $previousWinner
     */
    private function revertPreviousResult(array &$team, $previousWinner) : void
    {
        if ($previousWinner === null) {
            return;
        }

        if ($team['id'] === (int) $previousWinner) {
            $team['points'] -= 3;
            $team['matches_won'] -= 1;
            $team['matches_played'] -= 1;
        } else {
            $team['matches_lost'] -= 1;
            $team['matches_played'] -= 1;
        }
        $team['matches_need_to_play'] += 1;
    }

    /**
     * Apply new result
     *
     * @param  array  &$team
     * @param  mixed  $newWinner
     */
    private function applyNewResult(array &$team, $newWinner) : void
    {
        if ($newWinner === null) {
            return;
        }

        if ($team['id'] !== (int) $newWinner) {
            $team['matches_lost'] += 1;
        } else {
            $team['points'] += 3;
            $team['matches_won'] += 1;
        }
        $team['matches_played'] += 1;
        $team['matches_need_to_play'] -= 1;
    }

    /**
     * Sort points table
     *
     * @param  array  $points_table
     * @return array
     */
    private function sortPointsTable(array $points_table) : array
    {
        usort($points_table, function ($a, $b) {
            if ($b['pointsca'] !== $a['points']) {
                return $b['points'] - $a['points'];
            }
            if ($b['matches_played'] !== $a['matches_played']) {
                return $a['matches_played'] - $b['matches_played'];
            }
            return strcmp($a['name'], $b['name']);
        });
        return $points_table;
    }

    /**
     * Predict next match outcome
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function predictNextMatch(int $id) : JsonResponse
    {
        $match = Matches::findOrFail($id);
        $tournament = Tournament::findOrFail($match->tournament_id);
        if ($tournament->tournament_type === 'round-robin') {

            $participants = json_decode($match->participants, true);
            if (count($participants) === 2) {
                $predictions = $this->calculatePrediction($participants, $tournament->tournament_type, $tournament);
            }

            return response()->json([
                'status' => true,
                'message' => 'Predictions generated successfully',
                'predictions' => $predictions ?? [],
            ]);
        }

        $currentParticipants = json_decode($match->participants, true);

        $prediction = $this->calculatePrediction($currentParticipants, $tournament->tournament_type, $tournament);

        return response()->json([
            'status' => true,
            'message' => 'Prediction generated successfully',
            'predictions' => $prediction,
        ]);
    }

    /**
     * Calculate prediction based on tournament format
     *
     * @param  array  $currentParticipants
     * @param  string  $format
     * @param  object  $tournament
     * @return array
     */
    private function calculatePrediction(array $currentParticipants, string $format, Tournament $tournament) : array
    {
        $prediction = [
            'team1_win' => 0,
            'team2_win' => 0,
        ];

        if ($format === 'single-elimination') {
            $team1Wins = $this->countPreviousWins($currentParticipants[0]['id']);
            $team2Wins = $this->countPreviousWins($currentParticipants[1]['id']);

            $total = $team1Wins + $team2Wins;
            if ($total > 0) {
                $prediction['team1_win'] = ($team1Wins / $total) * 100;
                $prediction['team2_win'] = ($team2Wins / $total) * 100;
            } else {
                $prediction['team1_win'] = 50;
                $prediction['team2_win'] = 50;
            }
        } elseif ($format === 'round-robin') {
            $tiesheetResponse = TiesheetResponse::where('tournament_id', $tournament->id)->first();
            if ($tiesheetResponse && $tiesheetResponse->points_table) {
                $pointsTable = $tiesheetResponse->points_table;

                $team1Points = $this->getTeamPoints($pointsTable, $currentParticipants[0]['id']);
                $team2Points = $this->getTeamPoints($pointsTable, $currentParticipants[1]['id']);

                $total = $team1Points + $team2Points;
                if ($total > 0) {
                    $prediction['team1_win'] = ($team1Points / $total) * 100;
                    $prediction['team2_win'] = ($team2Points / $total) * 100;
                }
            }
        }

        return $prediction;
    }

    /**
     * Count previous wins for a team
     *
     * @param  int  $teamId
     * @return int
     */
    private function countPreviousWins(int $teamId) : int
    {
        return Matches::where(function($query) use ($teamId) {
                $query->where('team_id_1', $teamId)
                    ->orWhere('team_id_2', $teamId);
            })
            ->where('match_winner', $teamId)
            ->count();
    }

    /**
     * Get team points from points table
     *
     * @param  array  $pointsTable
     * @param  int  $teamId
     * @return int
     */
    private function getTeamPoints(array $pointsTable, int $teamId) : int
    {
        foreach ($pointsTable as $team) {
            if ($team['id'] === $teamId) {
                return $team['points'];
            }
        }
        return 0;
    }
}
