<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $authData = generateSuperAdminUserAndAuthToken();
    $this->superAdminToken = $authData['token'];
});

test('can get all roles', function () {
    Role::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->getJson('/api/v1/role');

    $response->assertStatus(200)
        ->assertJsonStructure(['data']);
});

test('can create a new role', function () {
    $permission = Permission::factory()->create();
    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->postJson('/api/v1/role', [
        'name' => 'New Role',
        'permissions' => [$permission->id],
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'New Role']);
});

test('can show role details', function () {
    $role = Role::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->getJson("/api/v1/role/{$role->id}");

    $response->assertStatus(200)
        ->assertJsonFragment(['id' => $role->id]);
});

test('can update role', function () {
    $role = Role::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->patchJson("/api/v1/role/{$role->id}", [
        'name' => 'Updated Role Name',
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Updated Role Name']);
});

test('can delete role', function () {
    $role = Role::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->deleteJson("/api/v1/role/{$role->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

test('can assign permissions to role', function () {
    $role = Role::factory()->create();
    $permission = Permission::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => "Bearer $this->superAdminToken",
    ])->postJson('/api/v1/role/assign-permission', [
        'id' => $role->id,
        'permissions' => [$permission->id],
    ]);

    $response->assertStatus(200);
    expect($role->refresh()->hasPermissionTo($permission->name))->toBeTrue();
});
