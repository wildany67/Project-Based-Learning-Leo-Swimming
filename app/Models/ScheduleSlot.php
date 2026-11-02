<?php

namespace App\Models;

use App\Support\Schedule;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Satu slot latihan: kolam + hari + jam + batas murid. */
class ScheduleSlot extends Model
{
    protected $fillable = ['location_id', 'weekday', 'time', 'capacity', 'week_start', 'is_active'];

    protected $casts = [
        'weekday'    => 'integer',
        'capacity'   => 'integer',
        'week_start' => 'date',
        'is_active'  => 'boolean',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** "Senin" */
    protected function dayName(): Attribute
    {
        return Attribute::get(fn () => Schedule::DAYS[$this->weekday] ?? '-');
    }

    /** "16.00" */
    protected function timeLabel(): Attribute
    {
        return Attribute::get(fn () => Schedule::timeLabel($this->time));
    }

    /** Slot "minggu ini saja" hanya muncul di minggu yang dipilih. */
    public function scopeForWeek($query, \Carbon\CarbonInterface $monday)
    {
        return $query->where(function ($q) use ($monday) {
            $q->whereNull('week_start')->orWhereDate('week_start', $monday->toDateString());
        });
    }
}
