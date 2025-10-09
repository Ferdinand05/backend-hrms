<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
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
