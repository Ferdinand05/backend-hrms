<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Office extends Model
{
    use LogsActivity;
    protected $fillable = ['latitude', 'longitude', 'app_name', 'radius', 'max_accuracy', 'start_time', 'end_time'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('office_log')
            ->submitEmptyLogs(false)
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName))
            ->logOnly(['app_name', 'latitude', 'longitude', 'radius', 'max_accuracy', 'start_time', 'end_time']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user() ?? 'System';

        return match ($eventName) {
            'created' => "Office has been added by {$user->name} - {$user->role->role_name}.",
            'updated' => "Data Office has been updated caused by {$user->name} - {$user->role->role_name}.",
            'deleted' => "Office Data has been deleted {$user->name} - {$user->role->role_name}.",
            default   => "Activity {$eventName} caused by {$user->name} to ."
        };
    }
}
