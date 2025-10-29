<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResource extends JsonResource
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
            'salary_id' => $this->salary_id,
            'period' => $this->period,
            'base_salary' => $this->base_salary,
            'allowance' => $this->allowance,
            'overtime_hours' => $this->overtime_hours,
            'overtime_pay' => $this->overtime_pay,
            'deduction' => $this->deduction,
            'total_salary' => $this->total_salary,
            'status' => $this->status,
            'calculated_total' => $this->calculated_total, // dari accessor getCalculatedTotalAttribute()

            // relasi
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'salary' => new SalaryResource($this->whenLoaded('salary')),

            // tambahan
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
