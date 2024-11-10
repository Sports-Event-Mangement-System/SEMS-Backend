<?php

namespace App\Http\Controllers;

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
     * @param  UpdateMatchRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function updateMatch(UpdateMatchRequest $request, int $id): JsonResponse
    {
        $match = Matches::findOrFail($id);
        $winner_team = Team::find($request->matchWinner);

        // Update current match participants
        $participants = $this->updateParticipants($match, $winner_team, $request);

        // Handle next match updates if exists
        if ($match->nextMatchId !== null) {
            $this->updateNextMatch($match, $winner_team);
        }

        // Update tiesheet and points table
        $this->updateTiesheetAndPoints($match, $request, $participants);

        // Update match details
        $match->update([
            'startTime' => $request->startTime,
            'match_winner' => $request->matchWinner ?? null,
            'participants' => json_encode($participants),
            'state' => $request->state,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Matches updated successfully',
        ]);
    }

    /**
     * Update participants
     *
     * @param  Matches  $match
     * @param  Team|null  $winner_team
     * @param  UpdateMatchRequest  $request
     * @return array
     */
    private function updateParticipants(Matches $match, ?Team $winner_team, UpdateMatchRequest $request): array
    {
        $participants = json_decode($match->participants, true);

        if ($winner_team) {
            $isTeam1Winner = $request->matchWinner == $match->team_id_1;
            $participants[0]['isWinner'] = $isTeam1Winner;
            $participants[1]['isWinner'] = !$isTeam1Winner;
        } else {
            $participants[0]['isWinner'] = false;
            $participants[1]['isWinner'] = false;
        }

        $participants[0]['resultText'] = $request->team1ResultText ?? null;
        $participants[1]['resultText'] = $request->team2ResultText ?? null;

        return $participants;
    }

    /**
     * Update next match
     *
     * @param  Matches  $match
     * @param  Team|null  $winner_team
     */
    private function updateNextMatch(Matches $match, ?Team $winner_team): void
    {
        $next_match = Matches::where('match_id', $match->nextMatchId)->first();
        $similar_matches = Matches::where('nextMatchId', $match->nextMatchId)->get();

        foreach ($similar_matches as $index => $similar_match) {
            if ($match->match_id === $similar_match->match_id) {
                $this->updateNextMatchParticipant($next_match, $winner_team, $index);
            }
        }
    }

    /**
     * Update next match participant
     *
     * @param  Matches  $next_match
     * @param  Team|null  $winner_team
     * @param  int  $index
     */
    private function updateNextMatchParticipant(Matches $next_match, ?Team $winner_team, int $index): void
    {
        $next_match_participants = json_decode($next_match->participants, true);

        if ($winner_team) {
            $next_match_participants[$index]['id'] = $winner_team->id;
            $next_match_participants[$index]['name'] = $winner_team->team_name;
            $team_id = $winner_team->id;
        } else {
            $next_match_participants[$index]['id'] = null;
            $next_match_participants[$index]['name'] = null;
            $team_id = null;
        }

        $update_data = [
            'participants' => json_encode($next_match_participants),
        ];

        $update_data[$index === 0 ? 'team_id_1' : 'team_id_2'] = $team_id;

        $next_match->update($update_data);
    }

    /**
     * Update tiesheet and points
     *
     * @param  Matches  $match
     * @param  UpdateMatchRequest  $request
     * @param  array  $participants
     */
    private function updateTiesheetAndPoints(Matches $match, UpdateMatchRequest $request, array $participants): void
    {
        $tiesheet_response = TiesheetResponse::where('tournament_id', $match->tournament_id)->first();
        if (!$tiesheet_response) {
            return;
        }

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

        $this->updateResponseData($response_data, $match, $request, $participants);

        $tiesheet_response->update([
            'response_data' => $response_data,
            'points_table' => $points_table
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
    private function updatePointsTable(array $points_table, array $participants, $previousWinner, $newWinner): array
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
    private function revertPreviousResult(array &$team, $previousWinner): void
    {
        if ($previousWinner === null) {
            return;
        }

        if ($team['id'] === (int)$previousWinner) {
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
    private function applyNewResult(array &$team, $newWinner): void
    {
        if ($newWinner === null) {
            return;
        }

        if ($team['id'] !== (int)$newWinner) {
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
    private function sortPointsTable(array $points_table): array
    {
        usort($points_table, function ($a, $b) {
            if ($b['points'] !== $a['points']) {
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
     * Update response data
     *
     * @param  array  &$response_data
     * @param  Matches  $match
     * @param  UpdateMatchRequest  $request
     * @param  array  $participants
     */
    private function updateResponseData(array &$response_data, Matches $match, UpdateMatchRequest $request, array $participants): void
    {
        foreach ($response_data as &$response_match) {
            if ($response_match['id'] == $match->match_id) {
                $response_match['startTime'] = $request->startTime;
                $response_match['state'] = $request->state;
                $response_match['participants'] = $participants;
            }
            if ($match->nextMatchId !== null && isset($next_match_participants) && $response_match['id'] == $match->nextMatchId) {
                $response_match['participants'] = $next_match_participants;
            }
        }
    }
    /**
     * Send match schedule
     *
     * @param  Request  $request
     */
    public function sendMatchSchedule(Request $request)
    {
        return view('emails.matches.match_schedule');
    }
}
