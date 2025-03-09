<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::create([
            'tournament_id' => 1,
            'team_name' => 'Real Madrid',
            'team_logo' => '1739035582_real madrid.png',
            'coach_name' => 'Carl Santiago',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'realmadrid@gmail.com',
            'address' => 'Spain, Madrid',
            'status' => 1,
            'player_number' => 2
        ]);
        $team = Team::create([
            'tournament_id' => 1,
            'team_name' => 'Manchester City',
            'team_logo' => '1739035670_city).png',
            'coach_name' => 'CIty coach',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'mancity@gmail.com',
            'address' => 'England, Manchester',
            'status' => 1,
            'player_number' => 2
        ]);
        $team = Team::create([
            'tournament_id' => 1,
            'team_name' => 'Barcelona',
            'team_logo' => '1739035739_barcelona.png',
            'coach_name' => 'Barcelona coach',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'barcelona@gmail.com',
            'address' => 'Spain, Barcelona',
            'status' => 1,
            'player_number' => 2
        ]);
        $team = Team::create([
            'tournament_id' => 1,
            'team_name' => 'Manchester United',
            'team_logo' => '1739035885_Manchester_United_FC_crest.svg (1).png',
            'coach_name' => 'Ten Hag',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'manchesterunited@gmail.com',
            'address' => 'England, Manchester',
            'status' => 1,
            'player_number' => 2
        ]);
        $team = Team::create([
            'tournament_id' => 1,
            'team_name' => 'Chelsea',
            'team_logo' => '1739036148_Chelsea Football Players.jpeg',
            'coach_name' => 'Ten Hag',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'chelsea@gmail.com',
            'address' => 'England, London',
            'status' => 1,
            'player_number' => 2
        ]);

        //Cricket Touranament ID 2
        $team = Team::create([
            'tournament_id' => 2,
            'team_name' => 'India',
            'team_logo' => '1739068515_India.png',
            'coach_name' => 'India coach',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'india@gmail.com',
            'address' => 'India, Mumbai',
            'status' => 1,
            'player_number' => 4
        ]);
        $team = Team::create([
            'tournament_id' => 2,
            'team_name' => 'Nepal',
            'team_logo' => '1739068569_Nepal.png',
            'coach_name' => 'Nepal coach',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'nepal@gmail.com',
            'address' => 'Nepal, Kathmandu',
            'status' => 1,
            'player_number' => 4
        ]);
        $team = Team::create([
            'tournament_id' => 2,
            'team_name' => 'Australia',
            'team_logo' => '1739068619_australia-flag-logo-F7EC70207B-seeklogo.com.png',
            'coach_name' => 'Australia coach',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'australia@gmail.com',
            'address' => 'Australia, Sydney',
            'status' => 1,
            'player_number' => 4
        ]);
        $team = Team::create([
            'tournament_id' => 2,
            'team_name' => 'West Indies',
            'team_logo' => '1739068694_west-indies.png',
            'coach_name' => 'Kalia Cochran',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'westindies@gmail.com',
            'address' => 'West Indies, Jamaica',
            'status' => 1,
            'player_number' => 4
        ]);
        $team = Team::create([
            'tournament_id' => 2,
            'team_name' => 'USA',
            'team_logo' => '1739068729_pngtree-rounded-flag-of-usa-png-image_5677590.png',
            'coach_name' => 'Laura England',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'usa@gmail.com',
            'address' => 'USA, New York',
            'status' => 1,
            'player_number' => 4
        ]);
        $team = Team::create([
            'tournament_id' => 2,
            'team_name' => 'Bangladesh',
            'team_logo' => '1739068818_bangladeshj.jpg',
            'coach_name' => 'Gil Moreno',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'bangladesh@gmail.com',
            'address' => 'Bangladesh, Dhaka',
            'status' => 1,
            'player_number' => 4
        ]);
        $team = Team::create([
            'tournament_id' => 2,
            'team_name' => 'Pakistan',
            'team_logo' => '1739068911_pakistan.png',
            'coach_name' => 'Pakistan coach',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'pakistan@gmail.com',
            'address' => 'Pakistan, Islamabad',
            'status' => 1,
            'player_number' => 4
        ]);
        $team = Team::create([
            'tournament_id' => 2,
            'team_name' => 'Bhutan',
            'team_logo' => '1739069026_bhutan.png',
            'coach_name' => 'Bhutan coach',
            'phone_number' => '+1 (275) 519-8842',
            'email' => 'bhutan@gmail.com',
            'address' => 'Bhutan, Thimphu',
            'status' => 1,
            'player_number' => 4
        ]);
        $team->save();
    }
}
