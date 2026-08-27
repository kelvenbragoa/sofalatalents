<?php

namespace Database\Seeders;

use App\Models\OrderItemStatus;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(UserTableSeeder::class);
        
        $this->call(RoleTableSeeder::class);
        // $this->call(EpisodeSeeder::class);
        // $this->call(ParticipantSeeder::class);


    }
}
