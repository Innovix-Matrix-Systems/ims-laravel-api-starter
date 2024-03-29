<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$testUser;

beforeEach(function () {
    $this->testUser = User::factory()->create();
});

it('should returns a successful login response with correct credentials', function () {

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->postJson('/api/v1/login', [
        'email'     => $this->testUser->email,
        'password'  => 'password',
        'device'    => 'testDevice',
    ]);

    $response->assertStatus(200);
});

it('should not returns a successful login response with in correct credentials', function () {

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->postJson('/api/v1/login', [
        'email'     => $this->testUser->email,
        'password'  => 'wrongPassword',
        'device'    => 'testDevice',
    ]);

    $response->assertStatus(400);
});
