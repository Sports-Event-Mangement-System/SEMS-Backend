<?php

namespace App\Helper;

use App\Models\Tournament;

class MatchHelper
{
    /**
     * Process participants
     *
     * @param  object  $match
     * @param  object  $tournament
     * @return array|null
     */
    public static function processParticipants($match, $tournament)
    {
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
            return $updatedParticipants;
        }
        return null;
    }

    public static function generatePointsTable($teams)
    {
        $points_table = [];
        foreach ($teams as $team) {
            $points_table[$team->id] = [
                'name' => $team->team_name,
                'logo_url' => url('uploads/teams/' . $team->team_logo),
                'points' => 0,
                'matches_played' => 0,
                'matches_won' => 0,
                'matches_lost' => 0,
            ];
        }
        return $points_table;
    }
}
