<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat user admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'level' => 'admin',
            'relasi' => 'front-office',
            'status' => 1,
            'api_token' => Str::random(60),
        ]);

        // Membuat user koki
        User::create([
            'name' => 'Koki',
            'email' => 'koki@gmail.com',
            'password' => Hash::make('koki123'),
            'level' => 'koki',
            'relasi' => 'dapur',
            'status' => 1,
            'api_token' => Str::random(60),
        ]);

        // Membuat user kasir
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@gmail.com',
            'password' => Hash::make('kasir123'),
            'level' => 'kasir',
            'relasi' => 'front-office',
            'status' => 1,
            'api_token' => Str::random(60),
        ]);
    }
}
