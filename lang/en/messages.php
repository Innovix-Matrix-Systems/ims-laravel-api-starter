<?php

declare(strict_types=1);


return [
    'phone.exists' => 'This phone number is not available in our database.',
    'phone.max'    => 'The phone number cannot be bigger than 11 digits.',
    'otp.size'     => 'OTP cannot be larger than 6 digits.',
    'password.size'     => 'The password must be at least 6 characters.',
    'password.required' => 'password is required',
    'otp.sent'     => 'Your OTP has been sent.',
    'login.general'=> 'It is not possible to login.',
    'login.invalid.password' => 'The password is incorrect',
    'login.invalid.otp' => 'OTPT is not correct!',
    'login.expired.otp' => 'The OTP has expired!',
    'login.deactive' => 'Your account is not active! It is not possible to login.',
    'login.unverified' => 'Your phone number has not been verified! It is not possible to login.',
    'login.lockout'  => 'I have tried to login with wrong information many times! Try again after a while.',
    'login.success'  => 'Login is successful.',
];
