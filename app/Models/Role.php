<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('role_log')
            ->submitEmptyLogs(false)
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName))
            ->logOnly(['role_name']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $user = auth()->user() ?? 'System';
        $name = $this->role_name ?? '(nama tidak diketahui)';

        return match ($eventName) {
            'created' => "Role {$name} has been added by {$user->name} - {$user->role->role_name}.",
            'updated' => "Data Role {$name} has been updated caused by {$user->name} - {$user->role->role_name}.",
            'deleted' => "Role {$name} has been deleted {$user->name} - {$user->role->role_name}.",
            default   => "Activity {$eventName} caused by {$user->name} to Role {$name}."
        };
    }


    protected $fillable = [
        'role_name',
    ];

    /**
     * Relasi ke User.
     * Satu role bisa dipakai banyak user.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
