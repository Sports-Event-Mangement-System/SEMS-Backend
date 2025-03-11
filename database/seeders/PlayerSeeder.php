<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Player::truncate();

        $teams = Team::all();

        foreach ($teams as $team) {
            for ($i = 1; $i <= $team->player_number; $i++) {
                Player::create([
                    'team_id' => $team->id,
                    'player_name' => "Player {$i} - " . $team->team_name,
                    'player_email' => strtolower(str_replace(' ', '', $team->team_name)) . ".player{$i}@example.com",
                    'is_captain' => $i === 1 ? 1 : 0,
                ]);
            }
        }
    }
}
