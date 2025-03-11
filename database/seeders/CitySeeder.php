<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::create(['name' => 'Trinidad', 'department' => 'Beni']);
        City::create(['name' => 'Santa Cruz De la Sierra', 'department' => 'Santa Cruz']);
    }
}
