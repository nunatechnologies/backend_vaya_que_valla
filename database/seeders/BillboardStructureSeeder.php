<?php

namespace Database\Seeders;

use App\Models\BillboardStructure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillboardStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Unilateral'
            ],
            [
                'name' => 'Bilateral'
            ],
            [
                'name' => 'Gigantografia'
            ],
            [
                'name' => 'Mural'
            ]
        ];

        BillboardStructure::insert($data);
    }
}
