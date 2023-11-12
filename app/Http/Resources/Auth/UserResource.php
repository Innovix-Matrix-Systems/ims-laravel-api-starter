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
            'id' => $this->id,
            'user_name' => $this->user_name,
            'name' => $this->name,
            'phone' => $this->phone,
            'phone_verified_at' => $this->phone_verified_at,
            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'last_active_device' => $this->last_active_device,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => RoleResource::collection($this->roles),
            'roleNames' => $this->getRoleNames(),
            'permissions' => PermissionResource::collection($this->getAllPermissions()),
        ];
    }
}
