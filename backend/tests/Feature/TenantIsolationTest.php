<?php

use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('returns 200 when a user reads their own organization ticket', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->for($organization)->create();
    $ticket = Ticket::factory()
        ->forOrganization($organization)
        ->withRequester($user)
        ->create();

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/tickets/{$ticket->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $ticket->id);
});

it('returns 404 when a user requests a ticket from another organization', function () {
    $organizationA = Organization::factory()->create();
    $userA = User::factory()->for($organizationA)->create();
    $ticketA = Ticket::factory()
        ->forOrganization($organizationA)
        ->withRequester($userA)
        ->create();

    $organizationB = Organization::factory()->create();
    $userB = User::factory()->for($organizationB)->create();

    Sanctum::actingAs($userB);

    $response = $this->getJson("/api/tickets/{$ticketA->id}");

    $response->assertStatus(404);
});

it('auto-sets organization_id from the authenticated user when creating a ticket', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->for($organization)->create();

    Sanctum::actingAs($user);

    $ticket = Ticket::factory()
        ->withRequester($user)
        ->create([
            'organization_id' => null,
        ]);

    expect($ticket->fresh()->organization_id)->toBe($organization->id);
});

it('scopes ticket queries to the authenticated user organization', function () {
    $organizationA = Organization::factory()->create();
    $userA = User::factory()->for($organizationA)->create();
    Ticket::factory()
        ->forOrganization($organizationA)
        ->withRequester($userA)
        ->count(2)
        ->create();

    $organizationB = Organization::factory()->create();
    $userB = User::factory()->for($organizationB)->create();
    Ticket::factory()
        ->forOrganization($organizationB)
        ->withRequester($userB)
        ->count(3)
        ->create();

    Sanctum::actingAs($userA);

    expect(Ticket::count())->toBe(2);
});
