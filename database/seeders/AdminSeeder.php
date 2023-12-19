<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "nom" => "admin",
            "telephone" => 772889673,
            "email" => "admin@gmail.com",
            "password" => Hash::make('azertyuiop'),
            "role" => "admin",
        ]);
    }
}
