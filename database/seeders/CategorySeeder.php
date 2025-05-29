<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Textil'], 
            ['name' => 'Alimenticio'], 
            ['name' => 'Automotriz'], 
            ['name' => 'Turístico'], 
            ['name' => 'Industrial'], 
            ['name' => 'Calzado'], 
            ['name' => 'Electrodomésticos'], 
            ['name' => 'Tecnología'], 
            ['name' => 'Servicio'], 
            ['name' => 'Agro'], 
            ['name' => 'Financiero'], 
            ['name' => 'Publicidad'], 
            ['name' => 'Construcción']
        ];

        Category::insert($data);
    }
}
