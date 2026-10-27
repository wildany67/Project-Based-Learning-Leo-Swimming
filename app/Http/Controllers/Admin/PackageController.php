<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Kelola paket dan harga. */
class PackageController extends Controller
{
    public function index(): View
    {
        return view('admin.packages.index', [
            'packages' => Package::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.packages.form', [
            'package' => new Package([
                'theme'       => 'navy',
                'price_label' => 'Biaya per paket',
                'is_active'   => true,
                'sort_order'  => (int) Package::max('sort_order') + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Package::create($this->validated($request));

        return redirect()->route('admin.packages.index')->with('success', 'Paket ditambahkan.');
    }

    public function edit(Package $package): View
    {
        return view('admin.packages.form', ['package' => $package]);
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $package->update($this->validated($request, $package));

        return redirect()->route('admin.packages.index')->with('success', 'Paket diperbarui.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', 'Paket dihapus.');
    }

    private function validated(Request $request, ?Package $package = null): array
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'alpha_dash', 'max:40', Rule::unique('packages', 'slug')->ignore($package?->id)],
            'tagline'     => ['nullable', 'string', 'max:150'],
            'highlight'   => ['nullable', 'string', 'max:100'],
            'ghost'       => ['nullable', 'string', 'max:8'],
            'theme'       => ['required', Rule::in(['navy', 'blue'])],
            'price_label' => ['required', 'string', 'max:60'],
            'prices_text' => ['required', 'string', 'max:2000'],
            'features_text' => ['nullable', 'string', 'max:2000'],
            'cta'         => ['required', 'string', 'max:60'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'slug.unique'      => 'Kode paket sudah dipakai paket lain.',
            'slug.alpha_dash'  => 'Kode paket hanya boleh huruf, angka, tanda hubung, dan garis bawah.',
            'required'         => ':attribute wajib diisi.',
        ], [
            'name'          => 'Nama paket',
            'slug'          => 'Kode paket',
            'price_label'   => 'Judul kotak harga',
            'prices_text'   => 'Daftar harga',
            'cta'           => 'Tulisan tombol',
        ]);

        $prices = Package::parsePrices($data['prices_text']);

        if ($prices === []) {
            throw ValidationException::withMessages([
                'prices_text' => 'Isi minimal satu harga dengan format: 4× pertemuan | Rp300.000',
            ]);
        }

        return [
            'name'        => $data['name'],
            'slug'        => strtolower($data['slug']),
            'tagline'     => $data['tagline'] ?? null,
            'highlight'   => $data['highlight'] ?? null,
            'ghost'       => $data['ghost'] ?? null,
            'theme'       => $data['theme'],
            'price_label' => $data['price_label'],
            'prices'      => $prices,
            'features'    => Package::parseLines($data['features_text'] ?? ''),
            'cta'         => $data['cta'],
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => $request->boolean('is_active'),
        ];
    }
}
