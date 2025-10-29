<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Attendance extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('attendance_log')
            ->submitEmptyLogs(false)
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName))
            ->logOnly(['employee.full_name', 'date', 'clock_in', 'clock_out', 'latitude', 'longitude', 'accuracy', 'status']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user() ?? 'System';
        $name = $this->employee->full_name ?? '(nama tidak diketahui)';

        return match ($eventName) {
            'created' => "Attendance {$name} has been added by {$user->name} - {$user->role->role_name}.",
            'updated' => "Data Attendance {$name} has been updated caused by {$user->name} - {$user->role->role_name}.",
            'deleted' => "Attendance {$name} has been deleted {$user->name} - {$user->role->role_name}.",
            default   => "Activity {$eventName} caused by {$user->name} to Attendance {$name}."
        };
    }


    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'image_path',
        'latitude',
        'longitude',
        'accuracy',
        'status',
    ];

    /**
     * Relasi ke Employee.
     * Satu absensi dimiliki oleh satu employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
