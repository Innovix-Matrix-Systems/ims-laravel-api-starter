<?php

use App\Enums\UserRole;
use App\Enums\UserRoleID;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$adminUser;
$authToken;

beforeEach(function () {
    $authData = generateAdminUserAndAuthToken();
    $this->adminUser = $authData['user'];
    $this->authToken = $authData['token'];
});

it('should returns a successful response after inserting a User', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'authorization' => "Bearer $this->authToken",
    ])->postJson('/api/v1/user', [
                'name' => 'test name',
                'email' => 'test@test.com',
                'password' => '123456',
                'password_confirmation' => '123456',
                'phone' => '1234567890',
                'designation' => 'manager',
                'address' => 'test address',
                'is_active' => UserStatus::ACTIVE->value,
                'roles' => [UserRoleID::USER_ID->value],
            ]);
    $response->assertStatus(201);
    $data = $response->json();

    $this->assertNotEmpty($data['data']['id']);
    $this->assertTrue($data['data']['name'] === 'test name');
    $this->assertTrue($data['data']['email'] === 'test@test.com');
    $this->assertTrue($data['data']['phone'] === '1234567890');
    $this->assertTrue($data['data']['address'] === 'test address');
    $this->assertTrue($data['data']['designation'] === 'manager');
    $this->assertTrue($data['data']['is_active'] === UserStatus::ACTIVE->value);
    $this->assertTrue($data['data']['created_by'] === $this->adminUser->id);
    $this->assertTrue($data['data']['roles'][0] === UserRole::USER->value);


});

it('should returns a successful response after updating a User', function () {

    $userCreateResponse = $this->withHeaders([
        'Accept' => 'application/json',
        'authorization' => "Bearer $this->authToken",
    ])->postJson('/api/v1/user', [
                'name' => 'test name',
                'email' => 'test@test.com',
                'password' => '123456',
                'password_confirmation' => '123456',
                'phone' => '1234567890',
                'designation' => 'manager',
                'address' => 'test address',
                'roles' => [UserRoleID::USER_ID->value],
            ]);

    $newUserData = $userCreateResponse->json();
    $userId = $newUserData['data']['id'];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'authorization' => "Bearer $this->authToken",
    ])->postJson('/api/v1/user/update', [
                'id' => $userId,
                'name' => 'test user update',
                'email' => 'test@2211test.com',
                'phone' => '1234567890',
                'designation' => 'manager',
                'address' => 'test address update',
                'is_active' => UserStatus::DEACTIVE->value,
            ]);
    $response->assertStatus(200);
    $data = $response->json();
    $this->assertTrue($data['data']['id'] === $userId);
    $this->assertTrue($data['data']['name'] === 'test user update');
    $this->assertTrue($data['data']['email'] === 'test@2211test.com');
    $this->assertTrue($data['data']['phone'] === '1234567890');
    $this->assertTrue($data['data']['address'] === 'test address update');
    $this->assertTrue($data['data']['is_active'] === UserStatus::DEACTIVE->value);
    $this->assertTrue($data['data']['updated_by'] === $this->adminUser->id);

});

it('should returns a error response when admin try to delete a user', function () {

    $userCreateResponse = $this->withHeaders([
        'Accept' => 'application/json',
        'authorization' => "Bearer $this->authToken",
    ])->postJson('/api/v1/user', [
                'name' => 'test name',
                'email' => 'test@test.com',
                'password' => '123456',
                'password_confirmation' => '123456',
                'phone' => '1234567890',
                'designation' => 'manager',
                'address' => 'test address',
                'roles' => [UserRoleID::USER_ID->value],
            ]);

    $newUserData = $userCreateResponse->json();
    $userId = $newUserData['data']['id'];

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'authorization' => "Bearer $this->authToken",
    ])->deleteJson("/api/v1/user/$userId");
    $response->assertStatus(403);
});
