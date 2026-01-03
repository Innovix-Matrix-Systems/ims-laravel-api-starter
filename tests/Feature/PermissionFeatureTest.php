<?php

use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $authData = generateSuperAdminUserAndAuthToken();
    $this->superAdminToken = $authData['token'];
    $this->superAdminUser = $authData['user'];
});

test('can get all permissions', function () {
    Permission::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->getJson('/api/v1/permission');

    $response->assertStatus(200)
        ->assertJsonStructure(['data']);
});

test('can create a new permission', function () {
    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->postJson('/api/v1/permission', [
        'name' => 'New Permission',
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'New Permission']);
});

test('can delete permission', function () {
    $permission = Permission::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->deleteJson("/api/v1/permission/{$permission->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
});

test('can get user permissions', function () {
    $permission = Permission::factory()->create();
    // Assign permission to Super Admin Role because getUserPermissions gets permissions via roles
    $role = Role::find(UserRole::SUPER_ADMIN->id());
    $role->givePermissionTo($permission);

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->getJson('/api/v1/permission/user');

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => $permission->name]);
});
