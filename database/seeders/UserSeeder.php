<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\RolSpatie;
use App\Enums\UserType;

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
            'user_type' => UserType::ORGANIZATION->name,
            'password' => Hash::make('12345678'),
            'email_verified_at' => now()
        ]);
        $user->organization()->create([
            'social_reason' => 'Empresa A',
            'name_contact' => 'Juan Pérez',
            'phone_contact' => '77885544',
            'commision_percentage' => 10
        ]);
        $user->assignRole(RolSpatie::ADMINISTRADOR->name);

        $user = User::create([
            'name' => 'User 2',
            'last_name' => 'lastname 2',
            'email' => 'user2@example.com',
            'phone' => '12345677',
            'cod_phone' => '+591',
            'user_type' => UserType::ORGANIZATION->name,
            'password' => Hash::make('12345678'),
            'email_verified_at' => now()
        ]);
        $user->organization()->create([
            'social_reason' => 'Empresa B',
            'name_contact' => 'Sultano',
            'phone_contact' => '87822549',
            'commision_percentage' => 10
        ]);
        $user->assignRole(RolSpatie::OPERADOR->name);

        $user = User::create([
            'name' => 'User 3',
            'last_name' => 'lastname 3',
            'email' => 'user3@example.com',
            'phone' => '12345676',
            'cod_phone' => '+591',
            'user_type' => UserType::PERSON->name,
            'password' => Hash::make('12345678'),
            'email_verified_at' => now()
        ]);
        $user->person()->create([
            'ci' => '1234567',
        ]);
        $user->assignRole(RolSpatie::ANUNCIANTE->name);

        $user = User::create([
            'name' => 'User 4',
            'last_name' => 'lastname 4',
            'email' => 'user4@example.com',
            'phone' => '12345675',
            'cod_phone' => '+591',
            'user_type' => UserType::ORGANIZATION->name,
            'password' => Hash::make('12345678'),
            'email_verified_at' => now()
        ]);
        $user->organization()->create([
            'social_reason' => 'Empresa C',
            'name_contact' => 'Menganito',
            'phone_contact' => '89544879',
            'commision_percentage' => 10,
        ]);
        $user->assignRole(RolSpatie::AGENCIA->name);

        $user = User::create([
            'name' => 'User 5',
            'last_name' => 'lastname 5',
            'email' => 'user5@example.com',
            'phone' => '12345674',
            'cod_phone' => '+591',
            'user_type' => UserType::ORGANIZATION->name,
            'password' => Hash::make('12345678'),
            'email_verified_at' => now()
        ]);
        $user->organization()->create([
            'social_reason' => 'Empresa D',
            'name_contact' => 'Fulanito',
            'phone_contact' => '78569876',
            'commision_percentage' => 10,
        ]);
        $user->assignRole(RolSpatie::AGENCIA->name);
    }
}
