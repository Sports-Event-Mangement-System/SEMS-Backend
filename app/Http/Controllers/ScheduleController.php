<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected int $tournament_id;

    public function tiesheetGenerator(Request $request, $id): JsonResponse
    {
        $tournament = Tournament::with('teams')->find($id);
        $this->tournament_id = $id;
        // Check if the tournament exists and has teams
        if (!$tournament || $tournament->teams->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Tournament not found or no teams available.',
            ], 404);
        }

        $teams = $tournament->teams;
        $formattedDate = now()->format('Y-m-d');
        $registrationEndDate = $formattedDate >= $tournament->re_date ? true : false;
        if (!$registrationEndDate) {
            $totalMatches = $tournament->max_teams;
        } else {
            if (count($teams) < 2) {
                return response()->json([
                    'status' => false,
                    'message' => 'At least 2 teams are required to generate matches. There is only 1 Team Registered in your Tournament',
                ], 400);
            }
            $totalMatches = count($teams);
        }
        $response = $this->generateMatchesResponse($totalMatches, $teams, $registrationEndDate);

        return response()->json([
            'status' => true,
            'message' => 'Tisheet generated successfully',
            'matches' => $response,
        ]);
    }

    /**
     * Generate matches for the tournament
     *
     * @param  int  $max_teams  It is just he team count
     * @param  array  $teams  This contains teams data
     * @param  bool  $registrationEndDate  If it is true then it will create matches in database and keeps records.
     * @return array
     */
    public function generateMatchesResponse($max_teams, $teams, $registrationEndDate)
    {
        $matches = [];
        $matchId = 1; // Start match ID from 1
        $round = 1;

        // Generate participants for the first round
        $participants = $this->generateParticipants($max_teams, $teams);

        // If only 2 teams, handle a final match directly
        if ($max_teams === 2) {
            $finalMatchParticipants = [
                $participants[0],
                $participants[1],
            ];

            // Assume the first team wins for demonstration purposes
            $finalMatchParticipants[0]['isWinner'] = true;
            $finalMatchParticipants[0]['status'] = 'PLAYED';
            $finalMatchParticipants[0]['resultText'] = 'Won';
            $finalMatchParticipants[1]['status'] = 'PLAYED';
            $finalMatchParticipants[1]['resultText'] = 'Lost';

            $matches[] = $this->createMatchEntry($matchId++, $finalMatchParticipants, null, 'Final',
                $registrationEndDate);
            return array_reverse($matches);
        }

        // Continue generating rounds until only one team remains
        while (count($participants) > 1) {
            $remainingTeams = count($participants);
            $nextRoundParticipants = [];
            $currentRoundMatches = [];
            $matchesToHappen = ceil($remainingTeams / 2);
            if ($remainingTeams == 2) {
                break;
            }

            // Create matches for the current round
            for ($i = 0; $i < $matchesToHappen; $i++) {
                if (count($participants) < 2) {
                    break; // No more participants to match
                }

                $matchParticipants = [
                    array_shift($participants), // First participant
                    array_shift($participants),  // Second participant
                ];

                // Assume the first team wins for demonstration purposes
                $matchParticipants[0]['isWinner'] = true; // Winner
                $matchParticipants[0]['status'] = 'PLAYED';
                $matchParticipants[0]['resultText'] = 'Won';
                $matchParticipants[1]['status'] = 'PLAYED';
                $matchParticipants[1]['resultText'] = 'Lost';

                // Create match entry
                $currentMatch = $this->createMatchEntry($matchId++, $matchParticipants, null, $round,
                    $registrationEndDate);
                $matches[] = $currentMatch;
                $currentRoundMatches[] = $currentMatch; // Store current round match for next match reference

                // Add winner to the next round
                $nextRoundParticipants[] = $matchParticipants[0];
                // Handle walkover for the current round if there's an odd team
                if ($remainingTeams % 2 === 1) {
                    $walkoverParticipant = array_shift($participants);
                    $walkoverParticipant['isWinner'] = true;
                    $walkoverParticipant['status'] = 'WALK_OVER';

                    // Create walkover match entry
                    $walkoverMatch = $this->createMatchEntry($matchId++, $matchParticipants, null, $round,
                        $registrationEndDate, $walkoverParticipant);
                    $matches[] = $walkoverMatch;
                    $currentRoundMatches[] = $walkoverMatch;
                    $nextRoundParticipants[] = $walkoverParticipant; // Add walkover participant to the next round
                }
            }

            // Assign nextMatchId for current round matches
            if ($round === 1) {
                $currentRoundMatchCount = count($currentRoundMatches);
                // Assign nextMatchId to paired matches
                for ($i = 0; $i < count($currentRoundMatches); $i++) {

                    $nextMatchId = floor($currentRoundMatchCount + ($i / 2) + 1);
                    $matches[$i]['nextMatchId'] = $nextMatchId; // Match i

                }
            } else {
                // For rounds greater than 1, implement similar logic or adjust as needed
                foreach ($currentRoundMatches as $i => $currentMatch) {
                    $matches[$currentMatch['id'] - 1]['nextMatchId'] = $matchId;
                }
            }

            // Prepare for the next round with winners
            $participants = $nextRoundParticipants;
            $round++;
        }

        // Handle final match if two teams remain
        if (count($participants) === 2) {
            $finalMatchParticipants = [
                $participants[0],
                $participants[1],
            ];
            // Assume the first team wins for demonstration purposes
            $finalMatchParticipants[0]['isWinner'] = true;
            $finalMatchParticipants[0]['status'] = 'PLAYED';
            $finalMatchParticipants[0]['resultText'] = 'Won';
            $finalMatchParticipants[1]['status'] = 'PLAYED';
            $finalMatchParticipants[1]['resultText'] = 'Lost';

            $matches[] = $this->createMatchEntry($matchId++, $finalMatchParticipants, null, 'Final',
                $registrationEndDate);
        }

        return array_reverse($matches);
    }

    /**
     * Create a match entry or walkover entry
     *
     * @param  int  $matchId
     * @param  array  $participants
     * @param  int|null  $nextMatchId
     * @param  string  $round
     * @param  bool  $registrationEndDate  If true, it will create matches in the database and keep records.
     * @param  array  $walkoverParticipant  data for walkover participants.
     * @return array
     */
    private function createMatchEntry(
        $matchId,
        $participants,
        $nextMatchId,
        $round,
        $registrationEndDate,
        $walkoverParticipant = null
    ): array {
        // Set common match data
        $isWalkover = $walkoverParticipant !== null;
        $match_data = [
            'id' => $matchId,
            'name' => ($isWalkover ? 'Walkover Match ' : 'Round '.$round.' Match ').$matchId,
            'nextMatchId' => $nextMatchId,
            'nextLooserMatchId' => null,
            'tournamentRoundText' => (string) ceil($matchId / 2),
            'startTime' => now()->toDateString(),
            'state' => $isWalkover ? 'WALK_OVER' : 'DONE',
            'participants' => $isWalkover ? [$walkoverParticipant] : $participants,
        ];
        if ($registrationEndDate) {
            // Check if the match already exists in the database to avoid duplicate creation
            $existingMatch = Matches::where('tournament_id', $this->tournament_id)
                ->where('match_id', $matchId)
                ->first();

            if (!$existingMatch) {
                // Prepare database match data
                $match_db_data = [
                    'tournament_id' => $this->tournament_id,
                    'name' => ($isWalkover ? 'Walkover Match ' : 'Round '.$round.' Match ').$matchId,
                    'nextMatchId' => $nextMatchId,
                    'nextLooserMatchId' => null,
                    'tournamentRoundText' => (string) ceil($matchId / 2),
                    'startTime' => now()->toDateString(),
                    'state' => $isWalkover ? 'WALK_OVER' : 'DONE',
                    'participants' => $isWalkover ? json_encode([$walkoverParticipant]) : json_encode($participants),
                    'match_id' => $matchId,
                ];

                // For regular matches, store team IDs
                if (!$isWalkover) {
                    $match_db_data['team_id_1'] = $participants[0]['id'];
                    $match_db_data['team_id_2'] = $participants[1]['id'];
                } else {
                    // For walkover, only one team is present
                    $match_db_data['team_id_1'] = $participants[0]['id'];
                }

                // Save match data to the database
                Matches::create($match_db_data);
            }
        }

        return $match_data;
    }

    /**
     * Generate participants for the tournament
     *
     * @param  int  $max_teams
     * @param  array  $teams
     * @return array
     */
    public function generateParticipants($max_teams, $teams)
    {
        $participants = [];
        $teamCount = count($teams);

        for ($i = 0; $i < $max_teams; $i++) {
            if ($i < $teamCount) {
                $team = $teams[$i];
                $participants[] = [
                    'id' => $team['id'], // Use team ID
                    'name' => $team['team_name'], // Use team name
                    'status' => 'UPCOMING',
                    'isWinner' => false,
                ];
            } else {
                $participants[] = [
                    'id' => $i + 1, // Unique ID for each participant
                    'name' => "Team ".($i + 1), // Placeholder name
                    'status' => 'UPCOMING',
                    'isWinner' => false,
                ];
            }
        }
        return $participants;
    }
}
