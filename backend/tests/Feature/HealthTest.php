<?php

it('returns a healthy status response', function () {
    $response = $this->getJson('/api/health');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'ok',
            'service' => 'pulsedesk',
        ]);
});
