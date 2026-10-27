<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Kolam latihan. */
class Location extends Model
{
    protected $fillable = ['area', 'pool', 'address', 'maps_url', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
