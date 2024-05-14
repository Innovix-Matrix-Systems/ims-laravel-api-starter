<?php

use App\Exceptions\BasicValidationErrorException;
use App\Http\DTOs\UserDTO;
use App\Http\Services\User\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$userService;
$testUser;
$superAdminUser;

beforeEach(function () {
    $this->userService = new UserService();
    $this->testUser = generateUser();
    $this->actingAs($this->testUser);
});

it('should update user profile password', function () {
    $this->userService->updateUserPassword($this->testUser, 'password', '123456');
    expect(Hash::check('password', $this->testUser->password))->toBeTrue();
});

it('should not update user profile password for incorrect current password', function () {

    $this->expectException(BasicValidationErrorException::class);
    $this->userService->updateUserPassword($this->testUser, '123456', '123455');
    expect(Hash::check('123456', $this->testUser->password))->toBeTrue();
});

it('should update user profile', function () {
    $userData = new UserDTO(
        $this->testUser->id,
        null,
        null,
        'test name update',
        'test@2test.com',
        '123456',
        '012872320010',
        'manager',
        'test addresss update',
        null,
        null
    );

    $this->userService->updateUserData($userData, $this->testUser, true);

    expect($this->testUser->name)->toBe('test name update');
    expect($this->testUser->email)->toBe('test@2test.com');
    expect($this->testUser->address)->toBe('test addresss update');
    expect($this->testUser->phone)->toBe('012872320010');
});
