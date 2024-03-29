<?php

use App\Exceptions\Auth\LoginErrorException;
use App\Http\Services\Auth\AuthService;
use App\Http\Services\Misc\OtpService;
use App\Http\Services\Misc\SmsService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$authService;
$testUser;

beforeEach(function () {
    $smsService = new SmsService();
    $otpService = new OtpService($smsService);
    $this->authService = new AuthService($otpService);
    $this->testUser = $this->testUser = User::factory()->create();
});

it('should login a user with correct credentials', function () {

    $result = $this->authService->login($this->testUser, 'password');
    expect($result)->toHaveKeys(['user', 'token']);
});

it('should not login a user with inorrect credentials', function () {

    $this->expectException(LoginErrorException::class);
    $result = $this->authService->login($this->testUser, 'wrongPassword');
});
