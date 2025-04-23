<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => 1,
                'social_reason' => 'Coca cola',
                'nit' => '121213131',
                'name_contact' => 'cocacolacontact',
                'phone_contact' => '1234567',
                'commision_percentage' => 5,
            ],
            [
                'user_id' => 2,
                'social_reason' => 'Fridolin',
                'nit' => '344432323',
                'name_contact' => 'fridolincontact',
                'phone_contact' => '654789',
                'commision_percentage' => 6,
            ],
        ];

        Organization::insert($data);
    }
}
