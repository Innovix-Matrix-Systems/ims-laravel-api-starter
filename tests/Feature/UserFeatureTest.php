<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Mock\UserMockData;

uses(RefreshDatabase::class);

beforeEach(function () {
    $authData = generateAdminUserAndAuthToken();
    $this->adminUser = $authData['user'];
    $this->authToken = $authData['token'];
});

test('can get all users', function () {
    User::factory()->count(3)->create();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson('/api/v1/user');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'roles',
                ],
            ],
            'links',
            'meta',
        ]);
});

test('can create a new user', function () {
    $userData = UserMockData::getUserData();
    // Use a unique email to avoid conflict with admin user
    $userData['email'] = 'newuser@example.com';
    $userData['password_confirmation'] = $userData['password'];
    $userData['is_active'] = true;
    $roleId = $this->adminUser->roles->first()->id;
    $userData['roles'] = [$roleId];

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->postJson('/api/v1/user', $userData);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', $userData['name'])
        ->assertJsonPath('data.email', $userData['email']);
});

test('can show user details', function () {
    $user = User::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson("/api/v1/user/{$user->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);
});

test('can update user details', function () {
    $user = User::factory()->create();
    $updateData = UserMockData::getUpdateUserData();

    $updateData['id'] = $user->id;
    $updateData['is_active'] = true;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->patchJson("/api/v1/user/{$user->id}", $updateData);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', $updateData['name'])
        ->assertJsonPath('data.email', $updateData['email']);
});

test('can delete user', function () {
    $user = User::factory()->create();

    // Use Super Admin because Admin might not have delete permission
    $authData = generateSuperAdminUserAndAuthToken();
    $superAdminToken = $authData['token'];

    $response = $this->withHeaders([
        'Authorization' => "Bearer $superAdminToken",
        'Accept' => 'application/json',
    ])->deleteJson("/api/v1/user/{$user->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('can assign role to user', function () {
    $user = User::factory()->create();
    $roleId = UserRole::USER->id();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->postJson('/api/v1/user/assign-role', [
        'id' => $user->id,
        'roles' => [$roleId],
    ]);

    $response->assertStatus(200);
    expect($user->fresh()->roles->contains('id', $roleId))->toBeTrue();
});

test('can change user password', function () {
    $user = User::factory()->create();
    $newPassword = 'newpassword123';

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->postJson('/api/v1/user/change-password', [
        'user_id' => $user->id,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    $response->assertStatus(200);
    expect(Hash::check($newPassword, $user->fresh()->password))->toBeTrue();
});

test('can get profile data', function () {
    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->getJson('/api/v1/user/profile');

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $this->adminUser->id);
});

test('can update profile', function () {
    $updateData = UserMockData::getUpdateUserData();
    $updateData['email'] = 'adminupdated@example.com';
    $updateData['is_active'] = true; // Required by validation

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->postJson('/api/v1/user/profile/update', $updateData);

    $response->assertStatus(200)
        ->assertJsonPath('data.email', $updateData['email']);
});

test('can change profile password', function () {
    // Default factory password is '123456'
    $currentPassword = '123456';
    $newPassword = 'newpassword123';

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->postJson('/api/v1/user/profile/change-password', [
        'current_password' => $currentPassword,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    $response->assertStatus(200);
    expect(Hash::check($newPassword, $this->adminUser->fresh()->password))->toBeTrue();
});

test('can update profile avatar', function () {
    $file = Illuminate\Http\UploadedFile::fake()->image('avatar.jpg');

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->authToken",
        'Accept' => 'application/json',
    ])->postJson('/api/v1/user/profile/update-avatar', [
        'id' => $this->adminUser->id,
        'avatar' => $file,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $this->adminUser->id);
    // You might want to assert media attached if checking DB/Storage
});

test('can export user data', function () {
    $authData = generateSuperAdminUserAndAuthToken();
    $superAdminToken = $authData['token'];

    $response = $this->withHeaders([
        'Authorization' => "Bearer $superAdminToken",
        'Accept' => 'application/json',
    ])->postJson('/api/v1/user/export');

    $response->assertStatus(200);
    // Assert content type or download headers
    expect($response->headers->get('content-type'))->toBe('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
