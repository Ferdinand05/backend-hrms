<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAllPayrollJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $currentPeriod = now()->format('Y-m'); // contoh: 2025-10

        $employees = Employee::whereHas('salary') // ✅ hanya yang punya salary
            ->whereDoesntHave('payrolls', function ($query) use ($currentPeriod) {
                $query->where('period', $currentPeriod); // ❌ belum punya payroll bulan ini
            })
            ->with(['salary', 'department'])
            ->get();

        foreach ($employees as $employee) {
            // Buat payroll baru untuk setiap karyawan
            Payroll::create([
                'employee_id' => $employee->id,
                'salary_id' => $employee->salary->id,
                'period' => $currentPeriod,
                'base_salary' => $employee->salary->base_salary,
                'status' => 'pending',
                'allowance' => $employee->salary->allowance,
                'deduction' => $employee->salary->deduction,
                'overtime_pay' => $employee->salary->overtime_rate * 0, // Asumsikan 0 jam lembur awalnya
                'total_salary' => $employee->salary->base_salary + $employee->salary->allowance - $employee->salary->deduction + ($employee->salary->overtime_rate * 0),
                'overtime_hours' => 0,
            ]);
        }
    }
}
