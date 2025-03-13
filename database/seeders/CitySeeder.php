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
        City::create(['name' => 'Santa Cruz', 'department' => 'Santa Cruz']);
    }
}
