<?php

namespace App\Helper;

use App\Mail\MatchSchedule;
use App\Mail\TeamStatusActive;
use App\Models\Follower;
use App\Models\Team;
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
    public static function TeamActiveMail(object $team) : void
    {
        $tournament = Tournament::find($team->tournament_id);

        $subject = $team->team_name . ' team is now active for the ' . $tournament->t_name . ' tournament';
        $recipientEmail = $team->email ?? 'team@mail.com';

        $mailData = [
            'subject' => $subject,
            'team' => $team,
            'tournament' => $tournament,
        ];
        Mail::to($recipientEmail)->send(new TeamStatusActive($mailData));
    }

    /**
     * Send Maile function to team mail when match is scheduled.
     *
     * @param  object  $match
     * @return void
     */
    public static function MatchScheduleMail(object $match) : void
    {
        $tournament = Tournament::find($match->tournament_id);
        $match_participants = MatchHelper::processParticipants($match, $tournament);
        $team1 = Team::find($match->team_id_1);
        $team2 = $match->team_id_2 ? Team::find($match->team_id_2) : null;

        // Prepare recipients for teams only
        $teamEmails = collect();

        // Add team1 email
        if ($team1 && $team1->email) {
            $teamEmails->push([
                'email' => $team1->email,
                'type' => 'team',
                'team' => $team1,
                'opponent' => $team2
            ]);
        }

        // Add team2 email if team2 exists
        if ($team2 && $team2->email) {
            $teamEmails->push([
                'email' => $team2->email,
                'type' => 'team',
                'team' => $team2,
                'opponent' => $team1
            ]);
        }

        // Send emails to teams
        $teamEmails->each(function ($recipient) use ($match, $tournament, $match_participants) {
            $opponentName = $recipient['opponent'] ? $recipient['opponent']->team_name : 'No Opponent (Walkover)';
            $subject = "Your Team {$recipient['team']->team_name} has a scheduled match against {$opponentName} in {$tournament->t_name} tournament";

            Mail::to($recipient['email'])->queue(new MatchSchedule([
                'subject' => $subject,
                'match' => $match,
                'participants' => $match_participants,
                'tournament' => $tournament,
                'recipientType' => $recipient['type'],
                'supportedTeam' => $recipient['team'],
                'opponentTeam' => $recipient['opponent']
            ]));
        });
    }
}
