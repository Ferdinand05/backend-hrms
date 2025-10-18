<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
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
}
