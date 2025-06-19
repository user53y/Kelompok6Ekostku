<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PemilikSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'BuTik',
            'email' => 'e.kostbutik@gmail.com',
            'password' => Hash::make('Pemilik26'),
            'role' => 'pemilik',
            'no_telepon' => '08123456789',
        ]);
    }
}
