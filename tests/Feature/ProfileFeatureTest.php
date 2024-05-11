<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$testUser;
$authToken;

beforeEach(function () {
    $authData = generateUserAndAuthToken();
    $this->testUser = $authData['user'];
    $this->authToken = $authData['token'];
});


it('should returns a successful response for viewing profile', function () {
    $userId = $this->testUser->id;
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'authorization' => "Bearer $this->authToken",
    ])->getJson("/api/v1/user/$userId");
    $response->assertStatus(200);
    $data = $response->json();
    $this->assertTrue($data['data']['id'] === $this->testUser->id);
    $this->assertTrue($data['data']['name'] === $this->testUser->name);
});

it('should returns a error response for not submitting required data', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'authorization' => "Bearer $this->authToken",
    ])->postJson("/api/v1/user/profile/update", [
        'name' => 'test',
    ]);
    $response->assertStatus(422);
});

it('should returns a successful response for updating user profile', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'authorization' => "Bearer $this->authToken",
    ])->postJson("/api/v1/user/profile/update", [
        'name' => 'test',
        'email' => 'test@2test.com',
        'phone' => '1234567890',
        'designation' => 'manager',
        'address' => 'test address',
    ]);
    $data = $response->json();
    $this->assertTrue($data['data']['id'] === $this->testUser->id);
    $this->assertTrue($data['data']['name'] === 'test');
    $this->assertTrue($data['data']['email'] === 'test@2test.com');
    $this->assertTrue($data['data']['phone'] === '1234567890');
    $this->assertTrue($data['data']['designation'] === 'manager');
    $this->assertTrue($data['data']['address'] === 'test address');
});
