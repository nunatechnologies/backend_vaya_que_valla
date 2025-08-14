<?php

namespace Database\Seeders;

use App\Models\DigitalBillboardPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DigitalBillboardPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
}
