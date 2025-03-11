<?php

namespace Database\Seeders;

use App\Models\BillboardType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillboardTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            [
                'name' => 'Digital',
                'category' => 'A'
            ],
            [
                'name' => 'Digital',
                'category' => 'B'
            ],
            [
                'name' => 'Digital',
                'category' => 'C'
            ],
            [
                'name' => 'Estatia',
                'category' => 'A'
            ],
            [
                'name' => 'Estatica',
                'category' => 'B'
            ],
            [
                'name' => 'Estatica',
                'category' => 'C'
            ],
        ];
        BillboardType::insert($rows);
    }
}
