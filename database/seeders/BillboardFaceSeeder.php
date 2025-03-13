<?php

namespace Database\Seeders;

use App\Imports\BillboardsImport;
use App\Models\Billboard;
use App\Models\BillboardFace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class BillboardFaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $import = new BillboardsImport();
        $sheet = Excel::toArray($import, public_path('billboards.xlsx'))[0];  // Obtiene la primera hoja directamente.
        array_shift($sheet);
        $billboards = Billboard::all();
        $billboardFaces = [];
        foreach ($sheet as $col) 
        {
            $location = $col[2];
            // dd($location, $billboards);
            $billboard = $billboards->firstWhere('location',trim($location));
            $face = $col[6];
            $locationDetail = $col[3];

            $billboardFaces[] = [
                'face' => $face,
                'location_detail' => $locationDetail,
                'billboard_id' => $billboard->id,
            ];
        }
        BillboardFace::insert($billboardFaces);
    }
}
