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
            'last_name' => 'lastname 1',
            'email' => 'user1@example.com',
            'phone' => '12345678',
            'cod_phone' => '+591',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::ADMINISTRADOR->name);

        $user = User::create([
            'name' => 'User 2',
            'last_name' => 'lastname 2',
            'email' => 'user2@example.com',
            'phone' => '12345677',
            'cod_phone' => '+591',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::OPERADOR->name);

        $user = User::create([
            'name' => 'User 3',
            'last_name' => 'lastname 3',
            'email' => 'user3@example.com',
            'phone' => '12345676',
            'cod_phone' => '+591',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::ANUNCIANTE->name);

        $user = User::create([
            'name' => 'User 4',
            'last_name' => 'lastname 4',
            'email' => 'user4@example.com',
            'phone' => '12345675',
            'cod_phone' => '+591',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::AGENCIA->name);

        $user = User::create([
            'name' => 'User 5',
            'last_name' => 'lastname 5',
            'email' => 'user5@example.com',
            'phone' => '12345674',
            'cod_phone' => '+591',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole(RolSpatie::CLIENTE->name);
    }
}
