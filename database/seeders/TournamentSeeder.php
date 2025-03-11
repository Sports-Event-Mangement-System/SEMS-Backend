<?php

namespace Database\Seeders;

use App\Models\Tournament;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tournament::truncate();
        $tournament = Tournament::create([
            't_name' => 'Football Champions League',
            't_description' => 'The Football Champions League is one of the most prestigious and widely followed club football tournaments in the world.',
            't_images' => '["1739035305_champion-league.jpg","1739035305_soccer_tournament.jpg"]',
            'prize_pool' => '50000',
            'ts_date' => date('Y-m-d', strtotime('+10 days')),
            'te_date' => date('Y-m-d', strtotime('+20 days')),
            'rs_date' => date('Y-m-d'),
            're_date' => date('Y-m-d', strtotime('+2 days')),
            'phone_number' => '9845646116',
            'email' => 'championsleague@gmail.com',
            'address' => 'Europe, Spain',
            'status' => true,
            'featured' => true,
            'tournament_type' => 'single-elimination',
            'max_teams' => '16',
            'min_teams' => '4',
            'max_players_per_team' => '11',
            'min_players_per_team' => '2',
        ]);
        $tournament = Tournament::create([
            't_name' => 'National Cricket League',
            't_description' => 'This is the cricket legaue for Countries exciting tournament to make your self glory and achive for your country.',
            't_images' => '["1739035441_cricket Tounrament.jpg"]',
            'prize_pool' => '55000',
            'ts_date' => date('Y-m-d', strtotime('+5 days')),
            'te_date' => date('Y-m-d', strtotime('+15 days')),
            'rs_date' => date('Y-m-d', strtotime('-2 days')),
            're_date' => date('Y-m-d'),
            'phone_number' => '9845646116',
            'email' => 'cricketleague@gmail.com',
            'address' => 'Asia, India',
            'status' => true,
            'featured' => true,
            'tournament_type' => 'round-robin',
            'max_teams' => '8',
            'min_teams' => '4',
            'max_players_per_team' => '11',
            'min_players_per_team' => '4',
        ]);
        $tournament->save();
    }
}
