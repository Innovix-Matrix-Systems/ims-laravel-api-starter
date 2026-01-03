<?php

declare(strict_types=1);

return [
    'phone.exists' => 'This phone number is not available in our database.',
    'phone.max' => 'The phone number cannot be bigger than 11 digits.',
    'otp.size' => 'OTP cannot be larger than 6 digits.',
    'password.size' => 'The password must be at least 6 characters.',
    'password.required' => 'password is required.',
    'otp.sent' => 'Your OTP has been sent.',
    'login.fail.general' => 'Login failed.',
    'login.invalid.password' => 'The password is incorrect.',
    'login.invalid.otp' => 'OTP is not correct.',
    'login.expired.otp' => 'The OTP has expired.',
    'login.inactive' => 'Your account is not active! It is not possible to login.',
    'login.unverified' => 'Your phone number has not been verified! It is not possible to login.',
    'login.lockout' => 'You have tried to login with wrong information many times! Try again after a while.',
    'login.success' => 'Login is successful.',
    'password.current.wrong' => 'Wrong current password! Please try again with the correct password.',
    'permission.delete.success' => 'Permission deleted successfully.',
    'permission.delete.fail' => 'Permission delete failed.',
    'role.delete.success' => 'Role deleted successfully.',
    'role.delete.fail' => 'Role delete failed.',
    'role.delete.unalterable.fail' => 'System role cannot be deleted.',
    'user.delete.failed' => 'Failed to delete the user.',
    'user.delete.unalterable.fail' => 'System user cannot be deleted.',
];
