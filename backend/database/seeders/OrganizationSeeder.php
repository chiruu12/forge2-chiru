<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organization::truncate();

        Organization::create([
            'name' => 'Org A',
            'slug' => 'org-a',
        ]);

        Organization::create([
            'name' => 'Org B',
            'slug' => 'org-b',
        ]);
    }
}
