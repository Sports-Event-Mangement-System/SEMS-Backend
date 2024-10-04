<?php

namespace App\Helper;

use App\Mail\TeamStatusActive;
use App\Models\Tournament;
use Mail;

class EmailHelper
{

    /**
     * Send Maile function to team mail when team is active.
     *
     * @param  object  $team
     * @return void
     */
    public static function TeamActiveMail(object $team): void
    {
        $tournament = Tournament::find($team->tournament_id);

        $subject = $team->team_name.' team is now active for the '.$tournament->t_name . ' tournament';
        $recipientEmail = $team->email ?? 'team@mail.com';

        $mailData = [
            'subject' => $subject,
            'team' => $team,
            'tournament' => $tournament,
        ];
        Mail::to($recipientEmail)->send(new TeamStatusActive($mailData));
    }

}
