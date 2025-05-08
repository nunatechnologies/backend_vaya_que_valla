<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\RolSpatie;
use App\Enums\UserType;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '00000000',
            'cod_phone' => '+591',
            'user_type' => UserType::PERSON->name,
            'password' => Hash::make('12345678'),
            'email_verified_at' => now()
        ]);
        $user->person()->create([
            'ci' => '0000000',
        ]);
        $user->assignRole(RolSpatie::ADMINISTRADOR->name);
    }
}
