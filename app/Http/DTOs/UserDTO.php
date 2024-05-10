<?php

namespace App\Http\DTOs;

class UserDTO
{
    public function __construct(
        public ?int $id,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $name,
        public ?string $email,
        public ?string $password,
        public ?string $phone,
        public ?string $designation,
        public ?string $address,
        public ?string $isActive,
        public mixed $roles,
    ) {
    }
}
