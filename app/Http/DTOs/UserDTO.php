<?php

namespace App\Http\DTOs;

class UserDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?string $password,
        public readonly ?string $phone,
        public readonly ?string $designation,
        public readonly ?string $address,
        public readonly ?string $isActive,
        public readonly mixed $roles,
    ) {
    }
}
