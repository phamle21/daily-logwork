<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@logwork.local')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@logwork.local',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }
    }
}
