<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        'organization_id' => $this->organization->id,
        'role' => 'admin',
    ]);
});

it('logs out and invalidates the token', function () {
    $tokenModel = $this->user->createToken('test-token');
    $plainTextToken = $tokenModel->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $plainTextToken)
        ->postJson('/api/logout')
        ->assertStatus(204);

    // Verify token is deleted from DB
    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenModel->accessToken->id,
    ]);
});

it('requires authentication to logout', function () {
    $this->postJson('/api/logout')
        ->assertStatus(401);
});
