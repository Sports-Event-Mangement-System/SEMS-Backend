<?php

namespace Database\Seeders;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Follower::truncate();
        User::truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'phone_number' => '9845646116',
            'email' => 'admin@admin.com',
            'profile_image' => '1742017403.png',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $user = User::create([
            'name' => 'user',
            'username' => 'user',
            'phone_number' => '9845646116',
            'email' => 'user@mail.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'profile_image' => '1742017403.png',
        ]);
        $user->save();
    }
}
