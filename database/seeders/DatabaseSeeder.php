<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $subDirectories = Storage::allDirectories('public');
        foreach ($subDirectories as $value) 
        {
            Storage::deleteDirectory($value);
        }

        $this->call([
            RolSeeder::class,
            UserSeeder::class,
            OrganizationSeeder::class,
            BillboardMasterSeeder::class
        ]);
    }
}
