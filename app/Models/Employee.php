<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('employee_log')
            ->submitEmptyLogs(false)
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName))
            ->logOnly(['employee_code', 'full_name', 'gender', 'date_of_birth', 'address', 'phone', 'position', 'status']);
        // Chain fluent methods for configuration options
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user() ?? 'System';
        $name = $this->full_name ?? '(nama tidak diketahui)';

        return match ($eventName) {
            'created' => "Employee {$name} has been added by {$user->name} - {$user->role->role_name}.",
            'updated' => "Data Employee {$name} has been updated caused by {$user->name}.",
            'deleted' => "Employee {$name} has been deleted {$user->name}.",
            default   => "Activity {$eventName} caused by {$user->name} to Employee {$name}."
        };
    }



    protected $fillable = [
        'department_id',
        'employee_code',
        'full_name',
        'avatar',
        'gender',
        'date_of_birth',
        'address',
        'phone',
        'hire_date',
        'position',
        'status',
    ];

    /**
     * Relasi ke Department.
     * Setiap employee hanya milik 1 department.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relasi ke User.
     * Employee bisa punya 1 akun user (opsional).
     */
    public function user()
    {
        return $this->hasOne(User::class, 'employee_id', 'id');
    }

    public function salary()
    {
        return $this->hasOne(Salary::class);
    }


    // bisa punya banyak payroll (1 untk tiap bulan)
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'employee_id');
    }
}
