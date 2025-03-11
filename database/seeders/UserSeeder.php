<?php

namespace Database\Seeders;

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
        User::truncate();
        $user = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'phone_number' => '9845646116',
            'email' => 'admin@admin.com',
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
        ]);
        $user->save();
    }
}
