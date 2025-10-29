<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaryResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'employee' => EmployeeResource::make($this->whenLoaded('employee')),
            'base_salary' => intval($this->base_salary),
            'allowance' => intval($this->allowance),
            'deduction' => intval($this->deduction),
            'overtime_rate' => intval($this->overtime_rate)
        ];
    }
}
