<?php

use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
    ]);
});

it('lists tickets scoped to the user organization', function () {
    Ticket::factory()->count(3)->forOrganization($this->organization)->create();
    Ticket::factory()->count(2)->create(); // different org

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/tickets');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('creates a ticket for the user organization', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/tickets', [
            'subject' => 'Printer broken',
            'description' => 'The office printer is not working.',
            'status' => 'open',
            'priority' => 'high',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.subject', 'Printer broken')
        ->assertJsonPath('data.organization_id', $this->organization->id);

    $this->assertDatabaseHas('tickets', [
        'subject' => 'Printer broken',
        'organization_id' => $this->organization->id,
    ]);
});

it('shows a ticket from the same organization', function () {
    $ticket = Ticket::factory()->forOrganization($this->organization)->create();

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/tickets/{$ticket->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $ticket->id);
});

it('updates a ticket from the same organization', function () {
    $ticket = Ticket::factory()->forOrganization($this->organization)->create([
        'status' => 'open',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->putJson("/api/tickets/{$ticket->id}", [
            'subject' => 'Updated subject',
            'status' => 'resolved',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.subject', 'Updated subject')
        ->assertJsonPath('data.status', 'resolved');
});

it('deletes a ticket from the same organization', function () {
    $ticket = Ticket::factory()->forOrganization($this->organization)->create();

    $response = $this->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/tickets/{$ticket->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
});

it('returns 404 for a ticket from another organization', function () {
    $otherOrg = Organization::factory()->create();
    $ticket = Ticket::factory()->forOrganization($otherOrg)->create();

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/tickets/{$ticket->id}");

    $response->assertStatus(404);
});

it('allows agents to create and view tickets', function () {
    $agent = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'agent',
    ]);

    $response = $this->actingAs($agent, 'sanctum')
        ->postJson('/api/tickets', [
            'subject' => 'Agent ticket',
            'description' => 'Created by agent',
            'status' => 'open',
            'priority' => 'medium',
        ]);

    $response->assertStatus(201);
});

it('allows customers to create tickets', function () {
    $customer = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'customer',
    ]);

    $response = $this->actingAs($customer, 'sanctum')
        ->postJson('/api/tickets', [
            'subject' => 'Customer ticket',
            'description' => 'Created by customer',
            'status' => 'open',
            'priority' => 'low',
        ]);

    $response->assertStatus(201);
});
