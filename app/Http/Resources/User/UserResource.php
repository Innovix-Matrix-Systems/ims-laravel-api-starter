<?php

namespace App\Http\Resources\User;

use App\Enums\MediaCollection;
use App\Http\Resources\Permission\PermissionResource;
use App\Http\Resources\Role\RoleSlimResource;
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
        $isPermissionLoaded = ! $this->whenLoaded('permissions') instanceof MissingValue;
        $isMediaLoaded = ! $this->whenLoaded('media') instanceof MissingValue;
        $data = [
            ...parent::toArray($request),
            'is_deleted' => $this->deleted_at ? true : false,
            'roleNames' => $this->whenLoaded('roles') ? $this->getRoleNames() : [],
            'roles' => RoleSlimResource::collection($this->whenLoaded('roles')),
            'creator' => UserSlimResource::make($this->whenLoaded('creator')),

        ];
        if ($isPermissionLoaded) {
            $data['permissions'] = PermissionResource::collection($this->getPermissionsViaRoles());
        }
        if ($isMediaLoaded) {
            // remove media data from response, add photo url
            unset($data['media']);
            $data['photo'] = $this->getFirstMedia(MediaCollection::PROFILE->value)?->getUrl();
        }

        return $data;
    }
}
