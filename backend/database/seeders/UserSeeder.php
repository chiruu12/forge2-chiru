<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate();

        $orgA = Organization::where('slug', 'org-a')->first();
        $orgB = Organization::where('slug', 'org-b')->first();

        // Org A: 1 admin, 2 agents, 2 customers
        User::create([
            'name' => 'Admin A',
            'email' => 'admin@orga.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgA->id,
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Agent 1A',
            'email' => 'agent1@orga.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgA->id,
            'role' => 'agent',
        ]);
        User::create([
            'name' => 'Agent 2A',
            'email' => 'agent2@orga.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgA->id,
            'role' => 'agent',
        ]);
        User::create([
            'name' => 'Customer 1A',
            'email' => 'customer1@orga.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgA->id,
            'role' => 'customer',
        ]);
        User::create([
            'name' => 'Customer 2A',
            'email' => 'customer2@orga.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgA->id,
            'role' => 'customer',
        ]);

        // Org B: 1 admin, 2 agents, 2 customers
        User::create([
            'name' => 'Admin B',
            'email' => 'admin@orgb.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgB->id,
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Agent 1B',
            'email' => 'agent1@orgb.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgB->id,
            'role' => 'agent',
        ]);
        User::create([
            'name' => 'Agent 2B',
            'email' => 'agent2@orgb.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgB->id,
            'role' => 'agent',
        ]);
        User::create([
            'name' => 'Customer 1B',
            'email' => 'customer1@orgb.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgB->id,
            'role' => 'customer',
        ]);
        User::create([
            'name' => 'Customer 2B',
            'email' => 'customer2@orgb.test',
            'password' => Hash::make('password'),
            'organization_id' => $orgB->id,
            'role' => 'customer',
        ]);
    }
}
