<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $subDirectories = Storage::disk('public')->allDirectories('/');
        foreach ($subDirectories as $value) 
        {
            Storage::disk('public')->deleteDirectory($value);
        }
        $this->call([
            RolSeeder::class,
            UserSeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            DigitalBillboardPlanSeeder::class,
            // OrganizationSeeder::class,
            // BillboardMasterSeeder::class
        ]);
    }
}
