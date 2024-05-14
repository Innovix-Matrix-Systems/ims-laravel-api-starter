<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\DTOs\UserDTO;
use App\Http\Services\User\UserService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$userService;
$adminUser;
$superAdminUser;

beforeEach(function () {
    $this->userService = new UserService();
    $this->adminUser = generateAdmin();
    $this->actingAs($this->adminUser);

});

it('should create a User', function () {
    $userData = new UserDTO(
        null,
        null,
        null,
        'test name',
        'test@test.com',
        '123456',
        '012378723200',
        'manager',
        'test address',
        UserStatus::ACTIVE->value,
        [UserRole::USER]
    );
    $user = $this->userService->insertUserData($userData)->load('roles');
    // dump($user);
    expect($user)->toBeInstanceOf(User::class);
    expect(User::where('name', 'test name')->exists())->toBeTrue();
    expect($user->email)->toBe('test@test.com');
    expect($user->phone)->toBe('012378723200');
    expect($user->is_active)->toBe(UserStatus::ACTIVE);
    expect($user->roles()->pluck('name')->toArray())->toContain(UserRole::USER->value);
});


it('should update a user', function () {
    $userData = new UserDTO(
        null,
        null,
        null,
        'test name',
        'test@test.com',
        '123456',
        '012378723200',
        'manager',
        'test address',
        UserStatus::ACTIVE->value,
        [UserRole::USER]
    );
    $user = $this->userService->insertUserData($userData);

    $userData = new UserDTO(
        $user->id,
        null,
        null,
        'test name update',
        'test@2test.com',
        '123456',
        '012872320010',
        'manager',
        'test addresss update',
        UserStatus::DEACTIVE->value,
        null
    );

    $updatedUser = $this->userService->updateUserData($userData, $user);
    expect($updatedUser)->toBeInstanceOf(User::class);
    expect(User::where('name', 'test name update')->exists())->toBeTrue();
    expect($updatedUser->email)->toBe('test@2test.com');
    expect($updatedUser->phone)->toBe('012872320010');
    expect($updatedUser->address)->toBe('test addresss update');
    expect($updatedUser->is_active)->toBe(UserStatus::DEACTIVE);
});

it('should delete a user', function () {
    $userData = new UserDTO(
        null,
        null,
        null,
        'test user delete',
        'test@test.com',
        '123456',
        '012378723200',
        'manager',
        'test address',
        UserStatus::ACTIVE->value,
        [UserRole::ADMIN, UserRole::USER]
    );
    $user = $this->userService->insertUserData($userData);
    $user->delete();
    expect(User::where('name', 'test user delete')->exists())->toBeFalsy();
});

it('should assign a role to a user', function () {
    $userData = new UserDTO(
        null,
        null,
        null,
        'test user delete',
        'test@test.com',
        '123456',
        '012378723200',
        'manager',
        'test address',
        UserStatus::ACTIVE->value,
        [UserRole::USER]
    );
    $user = $this->userService->insertUserData($userData);
    $this->userService->assignUserRole($user, [2,3]);
    expect($user->roles()->pluck('name')->toArray())->toContain(UserRole::ADMIN->value);
});

it('should update user password', function () {
    $userData = new UserDTO(
        null,
        null,
        null,
        'test user delete',
        'test@test.com',
        '123456',
        '012378723200',
        'manager',
        'test address',
        UserStatus::ACTIVE->value,
        [UserRole::USER]
    );
    $user = $this->userService->insertUserData($userData);
    $this->userService->updateUserPassword($user, 'password', null);
    expect(Hash::check('password', $user->password))->toBeTrue();
});
