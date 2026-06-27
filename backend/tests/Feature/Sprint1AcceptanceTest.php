<?php

use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);

    $this->orgA = Organization::where('slug', 'org-a')->first();
    $this->orgB = Organization::where('slug', 'org-b')->first();

    $this->adminA = User::where('email', 'admin@orga.test')->first();
    $this->agentA = User::where('email', 'agent1@orga.test')->first();
    $this->agent2A = User::where('email', 'agent2@orga.test')->first();
    $this->customer1A = User::where('email', 'customer1@orga.test')->first();
    $this->customer2A = User::where('email', 'customer2@orga.test')->first();

    $this->adminB = User::where('email', 'admin@orgb.test')->first();
    $this->agentB = User::where('email', 'agent1@orgb.test')->first();
    $this->customerB = User::where('email', 'customer1@orgb.test')->first();
});

// ───────────────────────────────────────────
// A. Cross-Tenant Isolation
// ───────────────────────────────────────────

it('returns 404 when org A admin GETs an org B ticket', function () {
    $orgBTicket = Ticket::where('organization_id', $this->orgB->id)->first();

    $this->actingAs($this->adminA, 'sanctum')
        ->getJson("/api/tickets/{$orgBTicket->id}")
        ->assertStatus(404);
});

it('returns 404 when org A agent PATCHes an org B ticket', function () {
    $orgBTicket = Ticket::where('organization_id', $this->orgB->id)->first();

    $this->actingAs($this->agentA, 'sanctum')
        ->putJson("/api/tickets/{$orgBTicket->id}", ['subject' => 'nope'])
        ->assertStatus(404);
});

it('returns 404 when org A customer DELETEs an org B ticket', function () {
    $orgBTicket = Ticket::where('organization_id', $this->orgB->id)->first();

    $this->actingAs($this->customer1A, 'sanctum')
        ->deleteJson("/api/tickets/{$orgBTicket->id}")
        ->assertStatus(404);
});

it('lists zero org B tickets for an org A user', function () {
    $response = $this->actingAs($this->adminA, 'sanctum')
        ->getJson('/api/tickets');

    $response->assertStatus(200);

    $orgBTicketIds = Ticket::where('organization_id', $this->orgB->id)
        ->pluck('id')
        ->toArray();

    $data = $response->json('data');
    $returnedIds = array_column($data, 'id');

    foreach ($orgBTicketIds as $bId) {
        expect(in_array($bId, $returnedIds))->toBeFalse();
    }
});

it('rejects creating a ticket with an org B requester_id', function () {
    $this->actingAs($this->adminA, 'sanctum')
        ->postJson('/api/tickets', [
            'subject' => 'Hijacked',
            'description' => 'Trying to use org B user',
            'status' => 'open',
            'priority' => 'high',
            'requester_id' => $this->customerB->id,
        ])
        ->assertStatus(403);
});

it('rejects creating a ticket with an org B assignee_id', function () {
    $this->actingAs($this->adminA, 'sanctum')
        ->postJson('/api/tickets', [
            'subject' => 'Hijacked',
            'description' => 'Trying to assign org B user',
            'status' => 'open',
            'priority' => 'high',
            'assignee_id' => $this->agentB->id,
        ])
        ->assertStatus(403);
});

// ───────────────────────────────────────────
// B. Role Enforcement
// ───────────────────────────────────────────

it('customer sees only their own tickets', function () {
    $response = $this->actingAs($this->customer1A, 'sanctum')
        ->getJson('/api/tickets');

    $response->assertStatus(200);

    $customer1Tickets = Ticket::where('requester_id', $this->customer1A->id)->pluck('id')->toArray();
    $customer2Tickets = Ticket::where('requester_id', $this->customer2A->id)->pluck('id')->toArray();

    $data = $response->json('data');
    $returnedIds = array_column($data, 'id');

    foreach ($customer1Tickets as $id) {
        expect(in_array($id, $returnedIds))->toBeTrue();
    }
    foreach ($customer2Tickets as $id) {
        expect(in_array($id, $returnedIds))->toBeFalse();
    }
});

it('customer gets 403 or 404 when GETing another customers ticket', function () {
    $otherTicket = Ticket::where('requester_id', $this->customer2A->id)->first();

    $response = $this->actingAs($this->customer1A, 'sanctum')
        ->getJson("/api/tickets/{$otherTicket->id}");

    $response->assertStatus(403);
});

it('customer cannot create a ticket on behalf of another user', function () {
    $this->actingAs($this->customer1A, 'sanctum')
        ->postJson('/api/tickets', [
            'subject' => 'On behalf',
            'description' => 'Using agent as requester',
            'status' => 'open',
            'priority' => 'low',
            'requester_id' => $this->agentA->id,
        ])
        ->assertStatus(403);
});

it('agent can list all tickets within org A', function () {
    $response = $this->actingAs($this->agentA, 'sanctum')
        ->getJson('/api/tickets');

    $response->assertStatus(200);

    $orgATicketCount = Ticket::where('organization_id', $this->orgA->id)->count();
    $data = $response->json('data');

    expect(count($data))->toBe($orgATicketCount);
});

it('agent can be assigned to a ticket and update status', function () {
    $ticket = Ticket::where('organization_id', $this->orgA->id)
        ->whereNull('assignee_id')
        ->first();

    $this->actingAs($this->agentA, 'sanctum')
        ->putJson("/api/tickets/{$ticket->id}", [
            'assignee_id' => $this->agentA->id,
            'status' => 'in_progress',
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.assignee_id', $this->agentA->id)
        ->assertJsonPath('data.status', 'in_progress');
});

it('admin can perform full CRUD on any ticket within org A', function () {
    $ticket = Ticket::where('organization_id', $this->orgA->id)->first();

    // Update
    $this->actingAs($this->adminA, 'sanctum')
        ->putJson("/api/tickets/{$ticket->id}", ['subject' => 'Admin updated'])
        ->assertStatus(200)
        ->assertJsonPath('data.subject', 'Admin updated');

    // Delete
    $this->actingAs($this->adminA, 'sanctum')
        ->deleteJson("/api/tickets/{$ticket->id}")
        ->assertStatus(204);
});

// ───────────────────────────────────────────
// C. Full Ticket CRUD Lifecycle
// ───────────────────────────────────────────

it('runs the full ticket CRUD lifecycle as an admin', function () {
    // 1. Create
    $createResponse = $this->actingAs($this->adminA, 'sanctum')
        ->postJson('/api/tickets', [
            'subject' => 'Lifecycle test',
            'description' => 'Created in acceptance test',
            'status' => 'open',
            'priority' => 'high',
        ]);

    $createResponse->assertStatus(201);
    $ticketId = $createResponse->json('data.id');

    // 2. List → ticket appears
    $this->actingAs($this->adminA, 'sanctum')
        ->getJson('/api/tickets')
        ->assertStatus(200)
        ->assertJsonFragment(['id' => $ticketId]);

    // 3. GET single
    $this->actingAs($this->adminA, 'sanctum')
        ->getJson("/api/tickets/{$ticketId}")
        ->assertStatus(200)
        ->assertJsonPath('data.subject', 'Lifecycle test')
        ->assertJsonPath('data.status', 'open')
        ->assertJsonPath('data.priority', 'high');

    // 4. PATCH update title, description, status, priority
    $this->actingAs($this->adminA, 'sanctum')
        ->putJson("/api/tickets/{$ticketId}", [
            'subject' => 'Updated lifecycle',
            'description' => 'Updated description',
            'status' => 'pending',
            'priority' => 'medium',
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.subject', 'Updated lifecycle')
        ->assertJsonPath('data.description', 'Updated description')
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonPath('data.priority', 'medium');

    // 5. PATCH assign to agent
    $this->actingAs($this->adminA, 'sanctum')
        ->putJson("/api/tickets/{$ticketId}", [
            'assignee_id' => $this->agent2A->id,
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.assignee_id', $this->agent2A->id);

    // 6. PATCH resolve/close
    $this->actingAs($this->adminA, 'sanctum')
        ->putJson("/api/tickets/{$ticketId}", [
            'status' => 'resolved',
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.status', 'resolved');

    // 7. PATCH close
    $this->actingAs($this->adminA, 'sanctum')
        ->putJson("/api/tickets/{$ticketId}", [
            'status' => 'closed',
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.status', 'closed');
});
