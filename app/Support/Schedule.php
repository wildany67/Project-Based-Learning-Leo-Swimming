<?php

namespace App\Support;

use App\Models\Registration;
use App\Models\ScheduleSlot;

/**
 * Hitungan jadwal: hari/jam, serta berapa murid aktif yang mengisi tiap slot.
 *
 * Murid mengisi slot kalau: statusnya terkonfirmasi, kolamnya sama,
 * hari latihannya mencakup hari slot, dan jam latihannya sama dengan jam slot.
 */
class Schedule
{
    /** Nomor hari (ISO) → nama. */
    public const DAYS = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat'];

    /** Jam yang tersedia sebagai pilihan cepat. */
    public const QUICK_TIMES = ['15:30', '16:00', '17:00', '18:00'];

    /** "16.00" / "16:00" / "9.5" → "16:00"; salah format → null. */
    public static function normalizeTime(?string $input): ?string
    {
        if (! preg_match('/^\s*(\d{1,2})\s*[.:]\s*(\d{2})\s*$/', (string) $input, $m)) {
            return null;
        }

        [$h, $min] = [(int) $m[1], (int) $m[2]];

        return ($h > 23 || $min > 59) ? null : sprintf('%02d:%02d', $h, $min);
    }

    /** "16:00" → "16.00" */
    public static function timeLabel(?string $time): string
    {
        return $time ? str_replace(':', '.', $time) : '-';
    }

    /** "Senin" → "Sen" */
    public static function dayShort(string $day): string
    {
        return mb_substr($day, 0, 3);
    }

    /** Senin minggu yang memuat tanggal ini. */
    public static function monday(\Carbon\CarbonInterface $date): \Carbon\CarbonInterface
    {
        return $date->copy()->startOfWeek(\Carbon\CarbonInterface::MONDAY)->startOfDay();
    }

    /**
     * Murid aktif per slot.
     * Bentuk: ['Nama Kolam|Senin|16:00' => [Registration, ...], ...]
     *
     * @return array<string,array<int,Registration>>
     */
    public static function occupancy(): array
    {
        $map = [];

        Registration::activeStudents()
            ->whereNotNull('schedule_time')
            ->get(['id', 'child_name', 'parent_name', 'package_key', 'package_label', 'location', 'schedule', 'schedule_time'])
            ->each(function (Registration $r) use (&$map) {
                foreach ($r->schedule ?? [] as $day) {
                    $map[self::key($r->location, $day, $r->schedule_time)][] = $r;
                }
            });

        return $map;
    }

    public static function key(string $pool, string $day, string $time): string
    {
        return $pool . '|' . $day . '|' . $time;
    }

    /** Murid yang mengisi satu slot. */
    public static function studentsOf(ScheduleSlot $slot, array $occupancy): array
    {
        return $occupancy[self::key((string) $slot->location?->pool, $slot->day_name, $slot->time)] ?? [];
    }

    /**
     * Status tampilan slot: off (dihentikan), full (penuh), almost (hampir penuh), free (masih kosong).
     */
    public static function state(ScheduleSlot $slot, int $filled): string
    {
        return match (true) {
            ! $slot->is_active                  => 'off',
            $filled >= $slot->capacity          => 'full',
            $slot->capacity - $filled <= 1      => 'almost',
            default                             => 'free',
        };
    }

    public const STATE_LABELS = [
        'free'   => 'Masih kosong',
        'almost' => 'Hampir penuh',
        'full'   => 'Penuh',
        'off'    => 'Dihentikan',
    ];
}
