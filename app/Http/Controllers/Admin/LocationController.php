<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Registration;
use App\Support\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Kolam renang. Saklar "Menerima pendaftar" (is_active) menutup kolam sementara:
 * kolam tidak tampil di website dan form daftar, tetapi datanya tetap ada.
 */
class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::withCount(['slots as active_slots' => fn ($q) => $q->where('is_active', true)])
            ->with(['slots' => fn ($q) => $q->orderBy('time')])
            ->orderBy('sort_order')->orderBy('id')->get();

        // Jumlah murid aktif per nama kolam
        $students = Registration::activeStudents()->selectRaw('location, count(*) as total')
            ->groupBy('location')->pluck('total', 'location');

        return view('admin.locations.index', [
            'locations' => $locations,
            'students'  => $students,
            'days'      => Schedule::DAYS,
            'occupancy' => Schedule::occupancy(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, 'kolam-new');
        $data['sort_order'] = (int) Location::max('sort_order') + 1;

        $location = Location::create($data);

        return back()->with('success', "Kolam {$location->pool} ditambahkan.");
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $data    = $this->validated($request, 'kolam-' . $location->id);
        $oldName = $location->pool;

        $location->update($data);

        // Nama kolam disimpan sebagai teks di data pendaftar; ikut diperbarui supaya jadwal tetap cocok.
        if ($oldName !== $location->pool) {
            Registration::where('location', $oldName)->update(['location' => $location->pool]);
        }

        return back()->with('success', 'Data kolam disimpan.');
    }

    /** Saklar cepat di kartu kolam. */
    public function toggle(Request $request, Location $location): RedirectResponse
    {
        $location->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('success', $location->is_active
            ? "{$location->pool} menerima pendaftar lagi."
            : "{$location->pool} ditutup sementara.");
    }

    public function destroy(Location $location): RedirectResponse
    {
        $name = $location->pool;
        $location->delete();   // slot jadwalnya ikut terhapus

        return back()->with('success', "Kolam {$name} dihapus.");
    }

    private function validated(Request $request, string $bag): array
    {
        $data = $request->validateWithBag($bag, [
            'pool'     => ['required', 'string', 'max:150'],
            'area'     => ['required', Rule::in(array_keys(Location::AREAS))],
            'address'  => ['nullable', 'string', 'max:250'],
            'maps_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['required', 'boolean'],
        ], [
            'required' => ':attribute wajib diisi.',
            'in'       => 'Pilih salah satu wilayah.',
            'url'      => 'Link Google Maps harus diawali https://',
        ], [
            'pool' => 'Nama kolam', 'area' => 'Wilayah',
        ]);

        return [
            'pool'      => $data['pool'],
            'area'      => $data['area'],
            'address'   => $data['address'] ?? null,
            'maps_url'  => $data['maps_url'] ?? null,
            'is_active' => (bool) $data['is_active'],
        ];
    }
}
