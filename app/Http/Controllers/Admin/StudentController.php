<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Registration;
use App\Support\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Murid = pendaftar yang sudah terkonfirmasi (aktif) atau selesai.
 * Halaman ini menampilkan sisa pertemuan, pengingat perpanjang paket, dan kehadiran hari ini.
 */
class StudentController extends Controller
{
    /** Sisa pertemuan sampai segini dianggap "hampir habis". */
    private const ALMOST_DONE = 1;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $filter = (string) $request->query('filter');

        $all = Registration::whereIn('status', [Registration::CONFIRMED, Registration::DONE])
            ->search($search)
            ->withMeetings()
            ->orderByRaw("case status when 'terkonfirmasi' then 0 else 1 end")
            ->orderBy('child_name')
            ->get();

        $almost = $all->filter(fn ($r) => $r->status === Registration::CONFIRMED && $r->meetings_left <= self::ALMOST_DONE);

        $counts = [
            'all'    => $all->count(),
            'active' => $all->where('status', Registration::CONFIRMED)->count(),
            'almost' => $almost->count(),
            'done'   => $all->where('status', Registration::DONE)->count(),
        ];

        $students = match ($filter) {
            'aktif'  => $all->where('status', Registration::CONFIRMED),
            'habis'  => $almost,
            'selesai' => $all->where('status', Registration::DONE),
            default  => $all,
        };

        return view('admin.students.index', [
            'students'   => $students->values(),
            'almost'     => $almost->values(),
            'attendance' => $this->todayAttendance(),
            'counts'     => $counts,
            'filter'     => in_array($filter, ['aktif', 'habis', 'selesai'], true) ? $filter : '',
            'search'     => $search,
        ]);
    }

    /** Catat hadir / tidak hadir untuk hari ini. */
    public function attend(Request $request, Registration $registration): RedirectResponse
    {
        $data = $request->validate(['present' => ['required', 'boolean']]);

        Attendance::updateOrCreate(
            ['registration_id' => $registration->id, 'date' => now()->toDateString()],
            ['present' => (bool) $data['present']]
        );

        return back()->with('success', "{$registration->child_name}: " . ($data['present'] ? 'hadir' : 'tidak hadir') . ' hari ini.');
    }

    /**
     * Murid yang berlatih hari ini, per jam, beserta catatan kehadirannya.
     *
     * @return array<string,array{label:string,students:array<int,array{student:Registration,present:?bool}>}>
     */
    private function todayAttendance(): array
    {
        $day = Schedule::DAYS[(int) now()->isoWeekday()] ?? null;

        if (! $day) {
            return [];
        }

        $students = Registration::activeStudents()->whereNotNull('schedule_time')->get()
            ->filter(fn ($r) => in_array($day, $r->schedule ?? [], true));

        $marks = Attendance::whereDate('date', now()->toDateString())
            ->whereIn('registration_id', $students->pluck('id'))
            ->pluck('present', 'registration_id');

        $groups = [];

        foreach ($students->sortBy('schedule_time') as $student) {
            $groups[$student->schedule_time]['label'] = Schedule::timeLabel($student->schedule_time);
            $groups[$student->schedule_time]['students'][] = [
                'student' => $student,
                'present' => $marks->has($student->id) ? (bool) $marks[$student->id] : null,
            ];
        }

        ksort($groups);

        return $groups;
    }
}
