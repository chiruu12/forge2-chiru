<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('registers a user and returns a sanctum token', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test Admin',
        'email' => 'admin@example.com',
        'password' => 'password123',
        'organization_name' => 'Test Org',
        'organization_slug' => 'test-org',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role', 'organization']])
        ->assertJsonPath('user.role', 'admin');

    $this->assertDatabaseHas('organizations', ['name' => 'Test Org']);
    $this->assertDatabaseHas('users', ['email' => 'admin@example.com', 'role' => 'admin']);
});

it('logs in and returns a sanctum token', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('password123'),
        'organization_id' => $organization->id,
        'role' => 'agent',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'user@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role', 'organization']])
        ->assertJsonPath('user.role', 'agent');
});

it('returns 401 for unauthenticated /me', function () {
    $this->getJson('/api/me')->assertStatus(401);
});

it('returns authenticated user with organization and role', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'organization_id' => $organization->id,
        'role' => 'customer',
    ]);

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.role', 'customer')
        ->assertJsonPath('data.organization.id', $organization->id);
});
