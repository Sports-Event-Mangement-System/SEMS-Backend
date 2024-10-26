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
}
