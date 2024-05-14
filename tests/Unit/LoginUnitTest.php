<?php

use App\Enums\UserStatus;
use App\Exceptions\Auth\LoginErrorException;
use App\Http\Services\Auth\AuthService;
use App\Http\Services\Misc\OtpService;
use App\Http\Services\Misc\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$authService;
$testUser;

beforeEach(function () {
    $smsService = new SmsService();
    $otpService = new OtpService($smsService);
    $this->authService = new AuthService($otpService);
    $this->testUser = generateUser();
});

it('should login a user with correct credentials', function () {

    $result = $this->authService->login($this->testUser, '123456');
    expect($result)->toHaveKeys(['user', 'token']);
});

it('should not login a user with inorrect credentials', function () {

    $this->expectException(LoginErrorException::class);
    $this->authService->login($this->testUser, 'wrongPassword');
});

it('should not login a inactive user', function () {
    $this->testUser->is_active = UserStatus::DEACTIVE->value;
    $this->testUser->save();
    $this->expectException(LoginErrorException::class);
    $this->authService->login($this->testUser, '123456');
});
