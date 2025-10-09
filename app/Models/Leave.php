<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
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
