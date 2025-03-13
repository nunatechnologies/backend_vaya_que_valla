<?php

namespace Database\Seeders;

use App\Imports\BillboardsImport;
use App\Models\Billboard;
use App\Models\BillboardStructure;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
class BillboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $advertisers = User::all();
        $billboardStructures = BillboardStructure::all();

        $import = new BillboardsImport();
        $sheet = Excel::toArray($import, public_path('billboards.xlsx'))[0];  // Obtiene la primera hoja directamente.
        array_shift($sheet);
        $cities = City::all();

        $billboards = [];
        foreach ($sheet as $col) 
        {
            $slug = Str::slug(trim($col[2]));
            $city = $cities->firstWhere('name', trim($col[0]));
            $name = $col[4];
            $location = trim($col[2]);
            $size = $col[5];
            $latitude = 0;
            $longitude = 0;
            if ($col[9] != "") 
            {
                $latLong = explode(',',$col[9]);
                $latitude = trim($latLong[0]);
                $longitude = trim($latLong[1]);
            }
            
            $billboards[$slug] = [
                'name' => $name,
                'location' => $location,
                'size' => $size,
                'price_per_month' => 1500,
                'status' => 'available',
                'traffic_data' => json_encode(['cars_per_day' => 8000]),
                'image' => 'billboard2.jpg',
                'longitude' => $latitude,
                'latitude' => $longitude,
                'billboard_type_id' => 2,
                'city_id' => $city->id,
                'billboard_structure_id' => $billboardStructures->random()->id,
                'entity_status' => 'active',
                'advertiser_id' => $advertisers->random()->id,
            ];
        }
        Billboard::insert($billboards);
    }
}
