<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data =  [
            'id'    => $this->id,
            'name'  => $this->name,
            'guard' => $this->guard_name,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions'))
        ];

        return $data;
    }
}
