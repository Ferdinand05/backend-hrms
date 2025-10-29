<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payroll extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('payroll_log')
            ->submitEmptyLogs(false)
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName))
            ->logOnly(['employee.full_name', 'status', 'period', 'base_salary', 'allowance', 'overtime_hours', 'overtime_pay', 'total_salary', 'deduction']);
        // Chain fluent methods for configuration options
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user() ?? 'System';
        $name = $this->employee->full_name ?? '(nama tidak diketahui)';

        return match ($eventName) {
            'created' => "Payroll {$name} has been added by {$user->name} - {$user->role->role_name}.",
            'updated' => "Data Payroll {$name} has been updated caused by {$user->name} - {$user->role->role_name}.",
            'deleted' => "Payroll {$name} has been deleted {$user->name} - {$user->role->role_name}.",
            default   => "Activity {$eventName} caused by {$user->name} to Payroll {$name}."
        };
    }


    protected $fillable = [
        'employee_id',
        'salary_id',
        'period',
        'base_salary',
        'allowance',
        'overtime_hours',
        'overtime_pay',
        'deduction',
        'total_salary',
        'status',
    ];

    /**
     * Relasi ke Employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relasi ke Salary (struktur gaji dasar).
     */
    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    /**
     * Hitung total gaji secara dinamis jika mau.
     */
    public function getCalculatedTotalAttribute()
    {
        return ($this->base_salary + $this->allowance + $this->overtime_pay) - $this->deduction;
    }
}
