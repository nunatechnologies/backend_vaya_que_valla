<?php

namespace Database\Seeders;

use App\Models\Billboard;
use App\Models\BillboardStructure;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $advertisers = User::all();
        $billboardStructures = BillboardStructure::all();
        $billboards = [
            [
                'name' => 'Valla en el centro',
                'location' => 'Avenida Central',
                'size' => 'large',
                'price_per_month' => 1200,
                'status' => 'available',
                'traffic_data' => json_encode(['cars_per_day' => 5000]),
                'image' => 'billboard1.jpg',
                'longitude' => -64.8833,
                'latitude' => -14.8333,
                'billboard_type_id' => 1,
                'city_id' => 1,
                'billboard_structure_id' => $billboardStructures->random()->id,
                'entity_status' => 'active',
                'advertiser_id' => $advertisers->random()->id,
            ],
            [
                'name' => 'Valla en el estadio',
                'location' => 'Estadio principal',
                'size' => 'medium',
                'price_per_month' => 1500,
                'status' => 'available',
                'traffic_data' => json_encode(['cars_per_day' => 8000]),
                'image' => 'billboard2.jpg',
                'longitude' => -64.8850,
                'latitude' => -14.8320,
                'billboard_type_id' => 2,
                'city_id' => 2,
                'billboard_structure_id' => $billboardStructures->random()->id,
                'entity_status' => 'active',
                'advertiser_id' => $advertisers->random()->id,
            ]
        ];

        Billboard::insert($billboards);
    }
}
