<?php

namespace App\Support;

use App\Models\Location;
use App\Models\ScheduleSlot;

/** Pilihan untuk form tambah pendaftar manual dan pengaturan jadwal pendaftar. */
class RegistrationForm
{
    /** @return array{packages:array,locations:\Illuminate\Support\Collection,days:array,times:array} */
    public static function options(): array
    {
        return [
            'packages'  => SiteContent::packageOptions(),
            'locations' => Location::orderBy('sort_order')->orderBy('id')->get(),
            'days'      => Schedule::DAYS,
            'times'     => self::timeChoices(null),
        ];
    }

    /** Jam yang sudah dipakai di slot (kolam tertentu atau semua) ditambah jam cepat. Format 16:00. */
    public static function timeChoices(?string $pool): array
    {
        $used = ScheduleSlot::query()
            ->when($pool, fn ($q) => $q->whereHas('location', fn ($l) => $l->where('pool', $pool)))
            ->pluck('time')->all();

        $times = array_values(array_unique(array_merge(Schedule::QUICK_TIMES, $used)));
        sort($times);

        return $times;
    }
}
