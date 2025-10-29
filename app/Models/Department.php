<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('department_log')
            ->submitEmptyLogs(false)
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName))
            ->logOnly(['name', 'description']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user() ?? 'System';
        $name = $this->name ?? '(nama tidak diketahui)';

        return match ($eventName) {
            'created' => "Department {$name} has been added by {$user->name} - {$user->role->role_name}.",
            'updated' => "Data Department {$name} has been updated caused by {$user->name} - {$user->role->role_name}.",
            'deleted' => "Department {$name} has been deleted {$user->name} - {$user->role->role_name}.",
            default   => "Activity {$eventName} caused by {$user->name} to Role {$name}."
        };
    }


    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relasi ke Employees.
     * Satu department bisa punya banyak employees.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
