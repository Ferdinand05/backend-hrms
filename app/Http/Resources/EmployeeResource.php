<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'full_name' => $this->full_name,
            'employee_code' => $this->employee_code,
            'date_of_birth' => $this->date_of_birth,
            'phone' => $this->phone,
            'address' => $this->address,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'department' => [
                'id' => $this->department->id,
                'name' => $this->department->name,
                'description' => $this->department->description,
                'created_at' => $this->department->created_at,
            ],
            'position' => $this->position,
            'status' => $this->status,
            'hire_date' => $this->hire_date,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
