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
        if (!$tournament ) {
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
                    'message' => 'At least 2 teams are required to generate matches.',
                ], 400);
            }
            $totalMatches = count($teams);
        }
        $randomTeams = $request->randomTeams ? true : false;
        $response = $this->generateMatchesResponse($totalMatches, $teams, $randomTeams);

        return response()->json([
            'status' => true,
            'message' => 'Tisheet generated successfully',
            'matches' => $response,
            'saveButton' => $registrationEndDate,
        ]);
    }

    /**
     * Generate matches for the tournament
     *
     * @param  int  $max_teams  It is just he team count
     * @param  array  $teams  This contains teams data
     * @return array
     */
    public function generateMatchesResponse($max_teams, $teams, $randomTeams = false)
    {
        $matches = [];
        $matchId = 1; // Start match ID from 1
        $round = 1;

        // Generate participants for the first round
        $participants = $this->generateParticipants($max_teams, $teams, $randomTeams);

        // If only 2 teams, handle a final match directly
        if ($max_teams === 2) {
            $finalMatchParticipants = [
                $participants[0],
                $participants[1],
            ];

            $matches[] = $this->createMatchEntry($matchId++, $finalMatchParticipants, null);
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

                // Create match entry
                $currentMatch = $this->createMatchEntry($matchId++, $matchParticipants, null);
                $matches[] = $currentMatch;
                $currentRoundMatches[] = $currentMatch; // Store current round match for next match reference

                // Add a placeholder for the winner to the next round
                $nextRoundParticipants[] = $matchParticipants[0];
            }

            // Handle walkover for the current round if there's an odd team
            if ($remainingTeams % 2 === 1) {
                $walkoverParticipant = array_shift($participants);
                $walkoverParticipant['isWinner'] = true;
                $walkoverParticipant['status'] = 'WALK_OVER';

                // Create walkover match entry
                $walkoverMatch = $this->createMatchEntry($matchId++, [$walkoverParticipant], null, $walkoverParticipant);
                $matches[] = $walkoverMatch;
                $currentRoundMatches[] = $walkoverMatch;
                $walkoverParticipant['isWinner'] = false;
                $walkoverParticipant['status'] = null;
                $nextRoundParticipants[] = $walkoverParticipant; // Add walkover participant to the next round
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

            // Randomly decide whether to shuffle participants for the next round
            if ($randomTeams) {
                shuffle($participants);
            }

            $round++;
        }

        // Handle final match if two teams remain
        if (count($participants) === 2) {
            $finalMatchParticipants = [
                $participants[0],
                $participants[1],
            ];

            $matches[] = $this->createMatchEntry($matchId++, $finalMatchParticipants, null);
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
     * @param  array  $walkoverParticipant  data for walkover participants.
     * @return array
     */
    private function createMatchEntry(
        $matchId,
        $participants,
        $nextMatchId,
        $walkoverParticipant = null
    ): array {
        // Set common match data
        $isWalkover = $walkoverParticipant !== null;
        $match_data = [
            'id' => $matchId,
            'name' => ($isWalkover ? 'Walkover Match ' : 'Match ').$matchId,
            'nextMatchId' => $nextMatchId,
            'nextLooserMatchId' => null,
            'tournamentRoundText' => (string) ceil($matchId / 2),
            'startTime' => now()->toDateString(),
            'state' => $isWalkover ? 'WALK_OVER' : 'DONE',
            'participants' => $isWalkover ? [$walkoverParticipant] : $participants,
        ];

        return $match_data;
    }

    /**
     * Generate participants for the tournament
     *
     * @param  int  $max_teams
     * @param  array  $teams
     * @param  bool  $randomTeams
     * @return array
     */
    public function generateParticipants($max_teams, $teams, $randomTeams = false): array
    {
        $participants = [];
        $teamCount = count($teams);

        for ($i = 0; $i < $max_teams; $i++) {
            if ($i < $teamCount) {
                $team = $teams[$i];
                $participants[] = [
                    'id' => $team['id'], // Use team ID
                    'name' => $team['team_name'], // Use team name
                    'status' => null,
                    'isWinner' => false,
                ];
            } else {
                $participants[] = [
                    'id' => $i + 1, // Unique ID for each participant
                    'name' => "Team ".($i + 1), // Placeholder name
                    'status' => null,
                    'isWinner' => false,
                ];
            }
        }
        if ($randomTeams) {
            shuffle($participants);
        }

        return $participants;
    }

}
