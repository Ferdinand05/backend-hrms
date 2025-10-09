<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
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
    // public function getNetSalaryAttribute()
    // {
    //     return ($this->base_salary + $this->allowance) - $this->deduction;
    // }
}
