<?php

namespace Database\Seeders;

use App\Enums\RolSpatie;
use App\Imports\BillboardsImport;
use App\Models\Billboard;
use App\Models\BillboardFace;
use App\Models\BillboardStructure;
use App\Models\BillboardType;
use App\Models\City;
use App\Models\Province;
use App\Models\User;
use App\Models\DigitalBillboardPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class BillboardMasterSeeder extends Seeder
{
    protected $sheet;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $import = new BillboardsImport();
        $this->sheet = Excel::toArray($import, public_path('UbicacionesVallasV2.xlsx'))[0];  // Obtiene la primera hoja directamente.
        array_shift($this->sheet);
        $this->provinces();
        $this->cities();
        $this->digitalBillboardPlans();
        $this->structures();
        $this->billboards();
        $this->faces();
    }

    public function provinces()
    {
        $provincesToSave = [];
        foreach ($this->sheet as $row) 
        {
            $provinceName = trim($row[1]);
            $provincesToSave[$provinceName] = ['name' => $provinceName];
        }

        if (count($provincesToSave) > 0) 
        {
            Province::insert($provincesToSave);
        }
    }

    public function cities()
    {
        $provinces = Province::all();
        $citiesToSave = [];
        foreach ($this->sheet as $row) 
        {
            $cityName = trim($row[3]);
            $departmentName = trim($row[2]);
            $province = $provinces->firstWhere('name', trim($row[1]));
            if ($departmentName != "") 
            {
                $citiesToSave[$cityName] = ['name' => $cityName, 'department' => $departmentName, 'province_id' => $province->id];
            }
        }

        if (count($citiesToSave) > 0) 
        {
            City::insert($citiesToSave);
        }
    }

    public function digitalBillboardPlans()
    {
        $plans = [
            ['name' => 'Plus', 'passes_per_hour' => 33],
            ['name' => 'Corporativo', 'passes_per_hour' => 54],
            ['name' => 'Premium', 'passes_per_hour' => 72],
        ];

        foreach ($plans as $plan) {
            DigitalBillboardPlan::create($plan);
        }
    }

    public function structures()
    {
        $strucuresToSave = [];
        foreach ($this->sheet as $row) 
        {
            $structureName = trim($row[0]);
            if ($structureName != "") 
            {
                $strucuresToSave[$structureName] = ['name' => $structureName];
            }
        }
        
        if (count($strucuresToSave) > 0) 
        {
            BillboardStructure::insert($strucuresToSave);
        }
    }

    public function billboards()
    {
        $advertisers = User::role(RolSpatie::ANUNCIANTE->name)->get();
        $billboardStructures = BillboardStructure::all();
        $cities = City::all();

        $billboards = [];
        foreach ($this->sheet as $key => $col) 
        {
            if ($col[0] == null && $col[1] == null && $col[2] == null) {
                continue;
            }
            $slug = Str::slug(trim($col[5]));
            $city = $cities->firstWhere('name', trim($col[3]));

            $billboardStructure = $billboardStructures->firstWhere('name',trim($col[0]));

            $location = trim($col[5]);
            $name = trim($col[7]) == ""?$location:trim($col[7]);
            $size = $col[8];
            $price = $col[10] == ""?0:floatval($col[10]);
            $latitude = 0;
            $longitude = 0;
            if ($col[12] != "") 
            {
                $latLong = explode(',',$col[12]);
                $latitude = trim($latLong[0]);
                $longitude = trim($latLong[1]);
            }
            
            $billboards[$slug] = [
                'name' => $name,
                'location' => $location,
                'size' => $size,
                'price_per_month' => $price,
                'status' => 'available',
                'traffic_data' => json_encode(['cars_per_day' => 8000]),
                'longitude' => $latitude,
                'latitude' => $longitude,
                'city_id' => $city->id,
                'billboard_structure_id' => $billboardStructure->id,
                'entity_status' => 'active',
                'advertiser_id' => $advertisers->random()->id,
            ];
        }
        Billboard::insert($billboards);
    }

    public function faces()
    {
        $billboards = Billboard::all();
    
        foreach ($this->sheet as $key => $col) {
            if ($col[0] == null && $col[1] == null && $col[2] == null) {
                continue;
            }
            $department = trim($col[2]);
            $department = strtolower($department);
            $department = str_replace(' ', '_',$department);
            $status = trim($col[13]) == "DISPONIBLE"?"VERDE":"ROJO";
            $code = trim($col[4]);
            $location = trim($col[5]);
            $billboard = $billboards->firstWhere('location', trim($location));
            if(is_null($billboard))
            {
                dd(count($this->sheet),$key, $col);
            }
            $face = trim($col[9]);
            $locationDetail = trim($col[6]);
    
            $billboardFace = BillboardFace::create([
                'code' => $code,
                'face' => $face,
                'location_detail' => $locationDetail,
                'billboard_id' => $billboard->id,
                'status' => $status
            ]);
    
            $relativePath = 'images/billboard_faces_photos/'.$department.'/' . $code . '.jpg';
            $fullPath = public_path($relativePath);
    
            if (file_exists($fullPath)) {
                $billboardFace
                    ->addMedia($fullPath)
                    ->preservingOriginal()
                    ->toMediaCollection();
            }
        }
    }
    

    public function faces_()
    {
        $billboards = Billboard::all();
        // $billboardFaces = [];
        foreach ($this->sheet as $col) 
        {
            $code = trim($col[4]);
            $location = trim($col[5]);
            $billboard = $billboards->firstWhere('location',trim($location));
            $face = trim($col[9]);
            $locationDetail = trim($col[6]);

            $billboardFace = BillboardFace::create([
                'code' => $code,
                'face' => $face,
                'location_detail' => $locationDetail,
                'billboard_id' => $billboard->id,
            ]);
            $relativePath = 'images/Santa_Cruz/' . $code . '.jpg';
            $fullPath = public_path($relativePath);

            if (file_exists($fullPath)) 
            {
                $billboardFace->addMediaFromUrl(asset($relativePath))->preservingOriginal()->toMediaCollection();
            } 
        }
    }
}
