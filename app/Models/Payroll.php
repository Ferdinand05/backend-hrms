<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'salary_id',
        'period',
        'base_salary',
        'allowance',
        'overtime_hours',
        'overtime_pay',
        'deduction',
        'total_salary',
        'status',
    ];

    /**
     * Relasi ke Employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relasi ke Salary (struktur gaji dasar).
     */
    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    /**
     * Hitung total gaji secara dinamis jika mau.
     */
    public function getCalculatedTotalAttribute()
    {
        return ($this->base_salary + $this->allowance + $this->overtime_pay) - $this->deduction;
    }
}
