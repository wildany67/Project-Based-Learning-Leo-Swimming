<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Kehadiran satu murid pada satu tanggal. */
class Attendance extends Model
{
    protected $fillable = ['registration_id', 'date', 'present'];

    protected $casts = ['date' => 'date', 'present' => 'boolean'];
}
