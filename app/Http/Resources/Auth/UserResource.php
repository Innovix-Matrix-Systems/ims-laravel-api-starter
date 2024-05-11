<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'roles' => $this->whenLoaded('roles') ? $this->getRoleNames() : [],
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
