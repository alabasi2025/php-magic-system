<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\User $resource
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Basic user information
        $data = [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'email_verified_at' => $this->resource->email_verified_at,
            'created_at' => $this->resource->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at->format('Y-m-d H:i:s'),
        ];

        // Conditionally load roles and permissions using a nested resource collection
        // This assumes the User model has 'roles' and 'permissions' relationships loaded.
        // RoleResource and PermissionResource must be created separately.
        $data['roles'] = RoleResource::collection($this->whenLoaded('roles'));
        $data['permissions'] = PermissionResource::collection($this->whenLoaded('permissions'));

        // Add a simple status field based on email verification
        $data['status'] = $this->resource->email_verified_at ? 'active' : 'pending';

        return $data;
    }
}