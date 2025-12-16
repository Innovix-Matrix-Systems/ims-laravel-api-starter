<?php

namespace App\DTOs\User;

use Illuminate\Http\Request;

class UserFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?bool $isActive = null,
        public readonly ?string $roleName = null,
        public readonly string $orderBy = 'created_at',
        public readonly string $orderDirection = 'desc',
        public readonly int $perPage = 10,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search', null),
            isActive: $request->input('is_active', null),
            roleName: $request->input('role_name', null),
            orderBy: $request->input('order_by', 'created_at'),
            orderDirection: $request->input('order_direction', 'desc'),
            perPage: $request->input('per_page', config('constant.DEFAULT_PAGINATION_ITEM_COUNT', 10)),
        );
    }
}
