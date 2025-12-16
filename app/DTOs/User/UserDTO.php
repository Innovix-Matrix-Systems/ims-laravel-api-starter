<?php

namespace App\DTOs\User;

use App\Models\User;
use Illuminate\Http\Request;

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
        public readonly ?bool $isActive,
        public readonly ?array $roles,
    ) {}

    public static function fromRequest(Request $request, ?User $existing = null, ?array $roles = null): self
    {
        $roles = $roles ?? $request->input('roles');

        return new self(
            $existing?->id,
            $request->input('first_name'),
            $request->input('last_name'),
            $request->input('name'),
            $request->input('email'),
            $request->input('password'),
            $request->input('phone'),
            $request->has('is_active') ? $request->boolean('is_active') : null,
            is_array($roles) ? $roles : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'is_active' => $this->isActive,
            'roles' => $this->roles,
        ];
    }
}
