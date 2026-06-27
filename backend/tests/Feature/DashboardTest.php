<?php

use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->admin = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
    ]);
    $this->agent = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'agent',
    ]);
    $this->customer = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'customer',
    ]);

    Ticket::factory()->forOrganization($this->organization)->create(['status' => 'open', 'priority' => 'low']);
    Ticket::factory()->forOrganization($this->organization)->create(['status' => 'open', 'priority' => 'urgent']);
    Ticket::factory()->forOrganization($this->organization)->create(['status' => 'pending', 'priority' => 'medium']);
    Ticket::factory()->forOrganization($this->organization)->create(['status' => 'resolved', 'priority' => 'high']);
    Ticket::factory()->forOrganization($this->organization)->create(['status' => 'closed', 'priority' => 'low']);
});

it('returns metrics for admin', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/dashboard/metrics');

    $response->assertStatus(200)
        ->assertJsonPath('data.open_count', 2)
        ->assertJsonPath('data.pending_count', 1)
        ->assertJsonPath('data.resolved_count', 1)
        ->assertJsonPath('data.closed_count', 1)
        ->assertJsonPath('data.urgent_open_count', 1)
        ->assertJsonPath('data.total_tickets', 5);
});

it('returns metrics for agent', function () {
    $response = $this->actingAs($this->agent, 'sanctum')
        ->getJson('/api/dashboard/metrics');

    $response->assertStatus(200)
        ->assertJsonPath('data.total_tickets', 5);
});

it('returns 403 for customer', function () {
    $response = $this->actingAs($this->customer, 'sanctum')
        ->getJson('/api/dashboard/metrics');

    $response->assertStatus(403);
});
