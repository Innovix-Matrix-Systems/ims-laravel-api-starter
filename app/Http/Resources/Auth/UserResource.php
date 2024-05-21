<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isPermissionLoaded = !$this->whenLoaded('permissions') instanceof MissingValue;
        $data = [
            ...parent::toArray($request),
            'roles' => $this->whenLoaded('roles') ? $this->getRoleNames() : [],
        ];
        if ($isPermissionLoaded) {
            $data['permissions'] = PermissionResource::collection($this->getPermissionsViaRoles());
        }
        return $data;
    }
}
