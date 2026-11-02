<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Registration;
use App\Models\ScheduleSlot;
use App\Support\RegistrationForm;
use App\Support\Schedule;
use Illuminate\View\View;

/** Beranda: sapaan, angka ringkas, jadwal hari ini, dan pendaftar terbaru. */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $now       = now();
        $occupancy = Schedule::occupancy();

        $stats = [
            'students'  => Registration::activeStudents()->count(),
            'month'     => Registration::whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
            'free'      => $this->freeSlotsThisWeek($occupancy),
            'confirmed' => Registration::whereBetween('confirmed_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
        ];

        return view('admin.dashboard', [
            'greeting' => $this->greeting((int) $now->format('G')),
            'stats'    => $stats,
            'today'    => $this->todaySchedule($occupancy),
            'latest'   => Registration::latest()->limit(5)->get(),
            'waiting'  => Registration::where('status', Registration::WAITING)->count(),
            'form'     => RegistrationForm::options(),
        ]);
    }

    private function greeting(int $hour): string
    {
        return match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default    => 'Selamat malam',
        };
    }

    /** Total tempat kosong di semua slot aktif minggu ini. */
    private function freeSlotsThisWeek(array $occupancy): int
    {
        $monday = Schedule::monday(now());

        return ScheduleSlot::with('location')->where('is_active', true)->forWeek($monday)->get()
            ->sum(fn (ScheduleSlot $s) => max($s->capacity - count(Schedule::studentsOf($s, $occupancy)), 0));
    }

    /**
     * Jadwal hari ini, dikelompokkan per jam dan kolam.
     * Satu murid → "Nama (privat)", lebih dari satu → "Grup N murid".
     *
     * @return array<int,array{time:string,text:string}>
     */
    private function todaySchedule(array $occupancy): array
    {
        $day = Schedule::DAYS[(int) now()->isoWeekday()] ?? null;

        if (! $day) {
            return [];
        }

        $rows = [];

        foreach ($occupancy as $key => $students) {
            [$pool, $slotDay, $time] = explode('|', $key);

            if ($slotDay !== $day) {
                continue;
            }

            $rows[] = [
                'sort' => $time,
                'time' => Schedule::timeLabel($time),
                'text' => Location::shortName($pool) . ' · ' . (count($students) === 1
                    ? $students[0]->child_name . ' (privat)'
                    : 'Grup ' . count($students) . ' murid'),
            ];
        }

        usort($rows, fn ($a, $b) => strcmp($a['sort'], $b['sort']));

        return $rows;
    }
}
