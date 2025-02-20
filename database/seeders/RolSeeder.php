<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\RolSpatie;
use Spatie\Permission\Models\Role;


class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => RolSpatie::ADMINISTRADOR->name]);
        Role::firstOrCreate(['name' => RolSpatie::OPERADOR->name]);
        Role::firstOrCreate(['name' => RolSpatie::ANUNCIANTE->name]);
        Role::firstOrCreate(['name' => RolSpatie::AGENCIA->name]);
        Role::firstOrCreate(['name' => RolSpatie::CLIENTE->name]);
    }
}
