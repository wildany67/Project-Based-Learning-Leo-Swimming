<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Satu baris riwayat perubahan pendaftar. */
class RegistrationLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['registration_id', 'text'];

    protected $casts = ['created_at' => 'datetime'];
}
