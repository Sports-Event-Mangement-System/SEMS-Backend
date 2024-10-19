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
        if (!$tournament) {
            return response()->json([
                'status' => false,
                'message' => 'Tournament not found or no teams available.',
            ], 404);
        }

        $teams = $tournament->teams;
        $formattedDate = now()->format('Y-m-d');
        $registrationEndDate = $formattedDate >= $tournament->re_date;
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
        $randomTeams = $request->input('randomTeams') === 'true';
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
     * @param int $max_teams It is just the team count
     * @param array $teams This contains teams data
     * @param bool $randomTeams
     * @return array
     */
    public function generateMatchesResponse($max_teams, $teams, $randomTeams = false): array
    {
        $matches = [];
        $matchId = 1;
        $round = 1;

        // Generate participants for the first round
        $participants = $this->generateParticipants($max_teams, $teams, $randomTeams);

        $totalRounds = ceil(log(count($participants), 2));

        for ($currentRound = 1; $currentRound <= $totalRounds; $currentRound++) {
            $matchesInRound = ceil(count($participants) / 2);
            $nextRoundParticipants = [];

            for ($i = 0; $i < $matchesInRound; $i++) {
                $matchParticipants = [];

                // For the first round, pair teams until we can't make full pairs
                if ($currentRound == 1 && count($participants) >= 2) {
                    $matchParticipants[] = array_shift($participants);
                    $matchParticipants[] = array_shift($participants);
                } else {
                    // Add up to two participants to this match
                    for ($j = 0; $j < 2; $j++) {
                        if (!empty($participants)) {
                            $matchParticipants[] = array_shift($participants);
                        }
                    }
                }

                $nextMatchId = ($currentRound < $totalRounds) ? $matchId + $matchesInRound : null;
                $state = (count($matchParticipants) == 1) ? "WALK_OVER" : "UPCOMING";

                $match = $this->createMatchEntry($matchId++, $matchParticipants, $nextMatchId, (string)$currentRound, $state);

                if ($state == "WALK_OVER") {
                    $match['participants'][0]['isWinner'] = true;
                    $match['participants'][0]['status'] = "WALK_OVER";
                    $match['participants'][0]['resultText'] = "Won";
                    $nextRoundParticipants[] = $match['participants'][0];
                } else {
                    $nextRoundParticipants[] = null;
                }

                $matches[] = $match;
            }

            $participants = $nextRoundParticipants;
        }

        // Correct nextMatchId for matches
        $roundMatches = [];
        foreach ($matches as $match) {
            $roundMatches[$match['tournamentRoundText']][] = $match;
        }

        for ($i = 1; $i < $totalRounds; $i++) {
            $currentRound = $roundMatches[$i];
            $nextRound = $roundMatches[$i + 1];

            foreach ($currentRound as $index => $match) {
                $nextMatchIndex = floor($index / 2);
                $matches[$match['id'] - 1]['nextMatchId'] = $nextRound[$nextMatchIndex]['id'];
            }
        }

        $matches[count($matches) - 1]['nextMatchId'] = null;

        return $matches;
    }

    private function createMatchEntry($matchId, $participants, $nextMatchId, $round, $state = "UPCOMING"): array
    {
        $isWalkover = ($state == "WALK_OVER");

        return [
            'id' => $matchId,
            'nextMatchId' => $nextMatchId,
            'name' => ($isWalkover ? 'Walkover Match ' : 'Match ') . $matchId,
            'tournamentRoundText' => $round,
            'startTime' => now()->toDateString(),
            'state' => $state,
            'participants' => array_map(function ($participant) use ($state) {
                return [
                    'id' => $participant['id'] ?? '',
                    'resultText' => ($state == "WALK_OVER") ? "Won" : null,
                    'isWinner' => ($state == "WALK_OVER"),
                    'status' => ($state == "WALK_OVER") ? "WALK_OVER" : null,
                    'name' => $participant['name'] ?? '',
                ];
            }, $participants),
        ];
    }

    /**
     * Generate participants for the tournament
     *
     * @param int $max_teams
     * @param array $teams
     * @param bool $randomTeams
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
                    'name' => "Team " . ($i + 1), // Placeholder name
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
