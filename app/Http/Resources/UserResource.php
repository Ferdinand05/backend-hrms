<?php

namespace App\Http\Resources;

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
            'name' => $this->name,
            'email' => $this->email,
            'employee_id' => $this->employee_id,
            'role_id' => $this->role_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'role' => $this->role->role_name,
            'employee' => $this->whenLoaded('employee'),
        ];
    }
}
