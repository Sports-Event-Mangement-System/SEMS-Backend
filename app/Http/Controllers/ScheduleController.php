<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function tiesheetGenerator(Request $request, $id): JsonResponse {
        $tournament = Tournament::with('teams')->find($id);

        // Check if the tournament exists and has teams
        if (!$tournament || $tournament->teams->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tournament not found or no teams available.'
            ], 404);
        }

        $teams = $tournament->teams;
        $totalMatches = $tournament->max_teams;
        $response = $this->generateMatchesResponse($totalMatches);

        return response()->json([
            'success' => true,
            'matches' => $response
        ]);
    }
    public function generateMatchesResponse($max_teams) {
        $matches = [];
        $matchId = 1; // Start match ID from 1
        $round = 1;

        // Initial validation: Check if there are at least 2 teams
        if ($max_teams < 2) {
            return ['error' => 'At least 2 teams are required to generate matches'];
        }

        // Generate participants for the first round
        $participants = $this->generateParticipants($max_teams);

        // If only 2 teams, handle a final match directly
        if ($max_teams === 2) {
            $finalMatchParticipants = [
                $participants[0],
                $participants[1]
            ];

            // Assume the first team wins for demonstration purposes
            $finalMatchParticipants[0]['isWinner'] = true;
            $finalMatchParticipants[0]['status'] = 'PLAYED';
            $finalMatchParticipants[0]['resultText'] = 'Won';
            $finalMatchParticipants[1]['status'] = 'PLAYED';
            $finalMatchParticipants[1]['resultText'] = 'Lost';

            $matches[] = $this->createMatchEntry($matchId++, $finalMatchParticipants, null, 'Final');
            return array_reverse($matches);
        }

        // Continue generating rounds until only one team remains
        while (count($participants) > 1) {
            $remainingTeams = count($participants);
            $nextRoundParticipants = [];
            $currentRoundMatches = [];
            $matchesToHappen = ceil($remainingTeams / 2);
            if($remainingTeams == 2) {
                break;
            }

            // Create matches for the current round
            for ($i = 0; $i < $matchesToHappen; $i++) {
                if (count($participants) < 2) {
                    break; // No more participants to match
                }

                $matchParticipants = [
                    array_shift($participants), // First participant
                    array_shift($participants)  // Second participant
                ];

                // Assume the first team wins for demonstration purposes
                $matchParticipants[0]['isWinner'] = true; // Winner
                $matchParticipants[0]['status'] = 'PLAYED';
                $matchParticipants[0]['resultText'] = 'Won';
                $matchParticipants[1]['status'] = 'PLAYED';
                $matchParticipants[1]['resultText'] = 'Lost';

                // Create match entry
                $currentMatch = $this->createMatchEntry($matchId++, $matchParticipants, null, $round);
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
                    $walkoverMatch = $this->createWalkoverMatchEntry($matchId++, $walkoverParticipant);
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
                $participants[1]
            ];
            // Assume the first team wins for demonstration purposes
            $finalMatchParticipants[0]['isWinner'] = true;
            $finalMatchParticipants[0]['status'] = 'PLAYED';
            $finalMatchParticipants[0]['resultText'] = 'Won';
            $finalMatchParticipants[1]['status'] = 'PLAYED';
            $finalMatchParticipants[1]['resultText'] = 'Lost';

            $matches[] = $this->createMatchEntry($matchId++, $finalMatchParticipants, null, 'Final');
        }

        return array_reverse($matches);
    }




    // Create a match entry with participants
    private function createMatchEntry($matchId, $participants, $nextMatchId, $round) {
        return [
            'id' => $matchId,
            'name' => 'Round ' . $round . ' Match' . $matchId,
            'nextMatchId' => $nextMatchId,
            'nextLooserMatchId' => null,
            'tournamentRoundText' => $round,
            'startTime' => now()->toDateString(),
            'state' => 'DONE', // Default to done
            'participants' => $participants,
        ];
    }

    // Create a walkover match entry
    private function createWalkoverMatchEntry($matchId, $walkoverParticipant) {
        return [
            'id' => $matchId,
            'name' => 'Walkover Match ' . $matchId,
            'nextMatchId' => null,
            'tournamentRoundText' => (string)ceil($matchId / 2),
            'startTime' => now()->toDateString(),
            'state' => 'WALK_OVER',
            'participants' => [
                $walkoverParticipant, // Walkover participant
            ],
        ];
    }
    public function generateParticipants($max_teams) {
        $participants = [];

        // Generate participants with placeholder names
        for ($i = 1; $i <= $max_teams; $i++) {
            $participants[] = [
                'id' => $i, // Unique ID for each participant
                'name' => 'Team ' . $i, // Placeholder name
                'status' => 'UPCOMING', // Initial status
                'isWinner' => false, // Initial winner status
            ];
        }

        return $participants;
    }

}
