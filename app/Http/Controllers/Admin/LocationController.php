<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Kelola lokasi kolam. */
class LocationController extends Controller
{
    public function index(): View
    {
        return view('admin.locations.index', [
            'locations' => Location::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.locations.form', [
            'location' => new Location([
                'is_active'  => true,
                'sort_order' => (int) Location::max('sort_order') + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Location::create($this->validated($request));

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi ditambahkan.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.form', ['location' => $location]);
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $location->update($this->validated($request));

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'area'       => ['required', 'string', 'max:80'],
            'pool'       => ['required', 'string', 'max:150'],
            'address'    => ['required', 'string', 'max:250'],
            'maps_url'   => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'required'     => ':attribute wajib diisi.',
            'maps_url.url' => 'Link Google Maps harus diawali https://',
        ], [
            'area'    => 'Wilayah',
            'pool'    => 'Nama kolam',
            'address' => 'Alamat',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = $request->boolean('is_active');

        return $data;
    }
}
