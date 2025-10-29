<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Salary extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('salary_log')
            ->submitEmptyLogs(false)
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName))
            ->logOnly(['employee.full_name', 'base_salary', 'allowance', 'deduction', 'overtime_rate']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user() ?? 'System';
        $name = $this->employee->full_name ?? '(nama tidak diketahui)';

        return match ($eventName) {
            'created' => "Salary {$name} has been added by {$user->name} - {$user->role->role_name}.",
            'updated' => "Data Salary {$name} has been updated caused by {$user->name} - {$user->role->role_name}.",
            'deleted' => "Salary {$name} has been deleted {$user->name} - {$user->role->role_name}.",
            default   => "Activity {$eventName} caused by {$user->name} to Salary {$name}."
        };
    }


    protected $fillable = [
        'employee_id',
        'base_salary',
        'allowance',
        'deduction',
        'overtime_rate',
    ];

    /**
     * Relasi ke Employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Hitung total gaji bersih.
     */
    public function getNetSalaryAttribute()
    {
        return ($this->base_salary + $this->allowance) - $this->deduction;
    }
}
