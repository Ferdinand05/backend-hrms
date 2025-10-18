<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
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
