<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\ScheduleSlot;
use App\Support\Schedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Jadwal mingguan: grid hari × jam, tambah slot, hentikan, dan hapus. */
class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $monday    = $this->monday($request->query('minggu'));
        $locations = Location::orderBy('sort_order')->orderBy('id')->get();
        $locationId = (int) $request->query('kolam');
        $locationId = $locations->contains('id', $locationId) ? $locationId : 0;

        $slots = ScheduleSlot::with('location')
            ->forWeek($monday)
            ->when($locationId, fn ($q) => $q->where('location_id', $locationId))
            ->orderBy('time')->orderBy('weekday')
            ->get();

        $occupancy = Schedule::occupancy();

        // Baris grid: jam → hari → daftar slot
        $grid = [];
        foreach ($slots as $slot) {
            $filled = count(Schedule::studentsOf($slot, $occupancy));
            $slot->setAttribute('filled', $filled);
            $slot->setAttribute('state', Schedule::state($slot, $filled));
            $slot->setAttribute('students', Schedule::studentsOf($slot, $occupancy));
            $grid[$slot->time][$slot->weekday][] = $slot;
        }
        ksort($grid);

        return view('admin.schedule.index', [
            'grid'       => $grid,
            'days'       => Schedule::DAYS,
            'locations'  => $locations,
            'locationId' => $locationId,
            'monday'     => $monday,
            'weekLabel'  => $this->weekLabel($monday),
            'prevWeek'   => $monday->copy()->subWeek()->toDateString(),
            'nextWeek'   => $monday->copy()->addWeek()->toDateString(),
            'quickTimes' => Schedule::QUICK_TIMES,
            'hasSlots'   => $slots->isNotEmpty(),
        ]);
    }

    /** Tambah slot untuk kombinasi kolam × hari × jam. Yang sudah ada diperbarui, bukan digandakan. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('slot', [
            'location_id' => ['required', 'exists:locations,id'],
            'days'        => ['required', 'array', 'min:1'],
            'days.*'      => [Rule::in(array_keys(Schedule::DAYS))],
            'times'       => ['nullable', 'array'],
            'times.*'     => ['string'],
            'time_extra'  => ['nullable', 'string', 'max:5'],
            'capacity'    => ['required', 'integer', 'min:1', 'max:30'],
            'scope'       => ['required', Rule::in(['week', 'always'])],
            'minggu'      => ['nullable', 'date'],
        ], [
            'location_id.required' => 'Pilih kolamnya dulu.',
            'days.required'        => 'Pilih minimal satu hari.',
            'capacity.required'    => 'Isi maksimal murid.',
        ]);

        $times = collect($data['times'] ?? [])
            ->push($data['time_extra'] ?? null)
            ->map(fn ($t) => Schedule::normalizeTime($t))
            ->filter()->unique()->values();

        if ($times->isEmpty()) {
            return back()->withErrors(['times' => 'Pilih atau isi minimal satu jam. Contoh: 16.00'], 'slot')->withInput();
        }

        $weekStart = $data['scope'] === 'week' ? $this->monday($data['minggu'] ?? null)->toDateString() : null;
        $saved = 0;

        foreach ($data['days'] as $day) {
            foreach ($times as $time) {
                ScheduleSlot::updateOrCreate(
                    ['location_id' => $data['location_id'], 'weekday' => (int) $day, 'time' => $time],
                    ['capacity' => $data['capacity'], 'week_start' => $weekStart, 'is_active' => true]
                );
                $saved++;
            }
        }

        return back()->with('success', "{$saved} slot disimpan.");
    }

    /** Hentikan / aktifkan slot, atau ubah batas murid. */
    public function update(Request $request, ScheduleSlot $slot): RedirectResponse
    {
        $data = $request->validate([
            'capacity'  => ['nullable', 'integer', 'min:1', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slot->update(array_filter($data, fn ($v) => $v !== null));

        return back()->with('success', 'Slot diperbarui.');
    }

    public function destroy(ScheduleSlot $slot): RedirectResponse
    {
        $slot->delete();

        return back()->with('success', 'Slot dihapus.');
    }

    private function monday(?string $date): Carbon
    {
        try {
            return Schedule::monday($date ? Carbon::parse($date) : now());
        } catch (\Throwable) {
            return Schedule::monday(now());
        }
    }

    /** "5 – 9 Oktober 2026" */
    private function weekLabel(Carbon $monday): string
    {
        $friday = $monday->copy()->addDays(4);

        $start = $monday->month === $friday->month
            ? (string) $monday->day
            : $monday->locale('id')->translatedFormat('j F');

        return $start . ' – ' . $friday->locale('id')->translatedFormat('j F Y');
    }
}
