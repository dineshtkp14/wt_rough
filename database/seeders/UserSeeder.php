<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
        \App\Models\User::truncate();
        \App\Models\User::factory(1)->create([
            'name' => 'Dinesh Bajgain',
            'phoneno' => '1234567890',
            'email' => 'dineshtkp14@gmail.com',
            'password' => bcrypt('nepal12345'),
        ]);
    }
}
