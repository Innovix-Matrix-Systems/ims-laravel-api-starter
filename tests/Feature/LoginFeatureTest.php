<?php

use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$testUser;

beforeEach(function () {
    $this->testUser = generateUser();
});

it('should returns a successful login response with correct credentials', function () {

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->postJson('/api/v1/login', [
        'email'     => $this->testUser->email,
        'password'  => '123456',
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

it('should not returns a successful login response for inactive user', function () {

    $this->testUser->update(['is_active' => UserStatus::DEACTIVE]);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->postJson('/api/v1/login', [
        'email'     => $this->testUser->email,
        'password'  => 'wrongPassword',
        'device'    => 'testDevice',
    ]);

    $response->assertStatus(400);
});
