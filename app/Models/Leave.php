<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('leave_log')
            ->submitEmptyLogs(false)
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName))
            ->logOnly(['employee.full_name', 'leave_type', 'start_date', 'end_date', 'reason', 'status', 'approved_by']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user() ?? 'System';
        $name = $this->employee->full_name ?? '(nama tidak diketahui)';

        return match ($eventName) {
            'created' => "Leave {$name} has been added by {$user->name} - {$user->role->role_name}.",
            'updated' => "Data Leave {$name} has been updated caused by {$user->name} - {$user->role->role_name}.",
            'deleted' => "Leave {$name} has been deleted {$user->name} - {$user->role->role_name}.",
            default   => "Activity {$eventName} caused by {$user->name} to Leave {$name}."
        };
    }


    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
    ];

    /**
     * Relasi ke Employee.
     * Leave dimiliki oleh seorang karyawan.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relasi ke User (yang menyetujui).
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
