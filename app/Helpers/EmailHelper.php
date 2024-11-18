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
        $team2 = Team::find($match->team_id_2);

        // Prepare recipients in batches with their contexts
        $recipients = collect();

        // Team Management
        $teamEmails = collect()
            ->when($team1->email, fn ($collection) => $collection->push([
                'email' => $team1->email,
                'type' => 'team',
                'team' => $team1,
                'opponent' => $team2
            ]))
            ->when($team2->email, fn ($collection) => $collection->push([
                'email' => $team2->email,
                'type' => 'team',
                'team' => $team2,
                'opponent' => $team1
            ]));

        // Players
        // $playerEmails = collect()
        //     ->merge($team1->players()->select('player_email')->get()->map(fn ($player) => [
        //         'email' => $player->player_email,
        //         'type' => 'player',
        //         'team' => $team1,
        //         'opponent' => $team2
        //     ]))
        //     ->merge($team2->players()->select('player_email')->get()->map(fn ($player) => [
        //         'email' => $player->player_email,
        //         'type' => 'player',
        //         'team' => $team2,
        //         'opponent' => $team1
        //     ]));

        // Followers
        $followerEmails = Follower::where('team_id', $team1->id)
            ->orWhere('team_id', $team2->id)
            ->get()
            ->map(fn ($follower) => [
                'email' => $follower->user_email,
                'type' => 'follower',
                'team' => $follower->team_id == $team1->id ? $team1 : $team2,
                'opponent' => $follower->team_id == $team1->id ? $team2 : $team1
            ]);

        // Merge all recipients and ensure unique emails
        $recipients = $teamEmails
            // ->merge($playerEmails)
            ->merge($followerEmails ?? collect())
            ->filter()
            ->unique('email');

        // Send emails in batches
        $recipients->chunk(50)->each(function ($chunk) use ($match, $tournament, $match_participants) {
            foreach ($chunk as $recipient) {
                $subject = match ($recipient['type']) {
                    'team' => "Your Team {$recipient['team']->team_name} has a scheduled match against {$recipient['opponent']->team_name}",
                    'player' => "Your Team {$recipient['team']->team_name} has an upcoming match against {$recipient['opponent']->team_name}",
                    'follower' => "Upcoming Match Alert: {$recipient['team']->team_name} vs {$recipient['opponent']->team_name}",
                } . " in {$tournament->t_name} tournament";

                Mail::to($recipient['email'])->queue(new MatchSchedule([
                    'subject' => $subject,
                    'match' => $match,
                    'participants' => $match_participants,
                    'tournament' => $tournament,
                    'recipientType' => $recipient['type'],
                    'supportedTeam' => $recipient['team'],
                    'opponentTeam' => $recipient['opponent']
                ]));
            }
        });
    }
}
