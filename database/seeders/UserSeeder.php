<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\RolSpatie;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::ADMINISTRADOR->name);

        $user = User::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::OPERADOR->name);

        $user = User::create([
            'name' => 'User 3',
            'email' => 'user3@example.com',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::ANUNCIANTE->name);

        $user = User::create([
            'name' => 'User 4',
            'email' => 'user4@example.com',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::AGENCIA->name);

        $user = User::create([
            'name' => 'User 5',
            'email' => 'user5@example.com',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::CLIENTE->name);
    }
}
