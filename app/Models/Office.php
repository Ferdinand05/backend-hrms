<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = ['latitude', 'longitude', 'app_name', 'radius', 'max_accuracy', 'start_time', 'end_time'];
}
