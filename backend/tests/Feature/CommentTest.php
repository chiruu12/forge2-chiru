<?php

use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
    ]);
    $this->ticket = Ticket::factory()->forOrganization($this->organization)->create([
        'requester_id' => $this->user->id,
    ]);
});

it('creates a comment on a ticket', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/tickets/{$this->ticket->id}/comments", [
            'body' => 'This is a comment',
            'is_internal' => false,
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.body', 'This is a comment')
        ->assertJsonPath('data.author_id', $this->user->id);

    $this->assertDatabaseHas('comments', [
        'ticket_id' => $this->ticket->id,
        'body' => 'This is a comment',
        'author_id' => $this->user->id,
    ]);
});

it('returns 404 when commenting on a cross-org ticket', function () {
    $otherOrg = Organization::factory()->create();
    $otherTicket = Ticket::factory()->forOrganization($otherOrg)->create();

    $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/tickets/{$otherTicket->id}/comments", [
            'body' => 'Should not work',
        ])
        ->assertStatus(404);
});

it('includes comments when fetching a ticket', function () {
    $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/tickets/{$this->ticket->id}/comments", [
            'body' => 'First comment',
        ]);

    $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/tickets/{$this->ticket->id}/comments", [
            'body' => 'Second comment',
            'is_internal' => true,
        ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/tickets/{$this->ticket->id}");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data.comments');
});
