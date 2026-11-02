<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Paket dan harga. Setiap kelas punya beberapa pilihan: jumlah pertemuan, harga, dan bonus gratis.
 * Di database disimpan sebagai teks siap tampil ("8× pertemuan", "Rp600.000", "+1× gratis"),
 * di dashboard ditampilkan sebagai angka supaya mudah diubah.
 */
class PackageController extends Controller
{
    public function index(): View
    {
        $packages = Package::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.packages.index', [
            'packages' => $packages,
            'rows'     => $packages->mapWithKeys(fn (Package $p) => [$p->id => $this->toRows($p->prices ?? [])]),
        ]);
    }

    /** Simpan semua kelas sekaligus (tombol "Simpan perubahan"). */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'packages'                    => ['required', 'array'],
            'packages.*.name'             => ['required', 'string', 'max:80'],
            'packages.*.tagline'          => ['nullable', 'string', 'max:150'],
            'packages.*.highlight'        => ['nullable', 'string', 'max:80'],
            'packages.*.price_label'      => ['nullable', 'string', 'max:60'],
            'packages.*.cta'              => ['nullable', 'string', 'max:60'],
            'packages.*.ghost'            => ['nullable', 'string', 'max:8'],
            'packages.*.theme'            => ['nullable', Rule::in(['navy', 'blue'])],
            'packages.*.features'         => ['nullable', 'string', 'max:800'],
            'packages.*.rows'             => ['required', 'array', 'min:1'],
            'packages.*.rows.*.meetings'  => ['required', 'integer', 'min:1', 'max:99'],
            'packages.*.rows.*.price'     => ['required', 'integer', 'min:0', 'max:99999999'],
            'packages.*.rows.*.bonus'     => ['nullable', 'integer', 'min:0', 'max:20'],
        ], [
            'required' => ':attribute wajib diisi.',
            'integer'  => ':attribute harus berupa angka.',
            'min'      => 'Setiap kelas minimal punya satu pilihan paket.',
        ], [
            'packages.*.name'            => 'Nama kelas',
            'packages.*.rows.*.meetings' => 'Jumlah pertemuan',
            'packages.*.rows.*.price'    => 'Harga',
        ]);

        foreach (Package::all() as $package) {
            $input = $request->input("packages.{$package->id}");

            if (! $input) {
                continue;
            }

            $package->update([
                'name'        => $input['name'],
                'tagline'     => $input['tagline'] ?? null,
                'highlight'   => $input['highlight'] ?? null,
                'price_label' => $input['price_label'] ?: $package->price_label,
                'cta'         => $input['cta'] ?: $package->cta,
                'ghost'       => $input['ghost'] ?? null,
                'theme'       => $input['theme'] ?? $package->theme,
                'features'    => Package::parseLines($input['features'] ?? ''),
                'is_active'   => ! empty($input['is_active']),
                'prices'      => $this->fromRows($input['rows']),
            ]);
        }

        return redirect()->route('admin.packages.index')->with('success', 'Paket dan harga disimpan. Website sudah memakai harga terbaru.');
    }

    /** Tambah satu pilihan paket ke sebuah kelas (modal "Tambah pilihan paket"). */
    public function storeOption(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('opsi', [
            'package_id' => ['required', 'exists:packages,id'],
            'meetings'   => ['required', 'integer', 'min:1', 'max:99'],
            'price'      => ['required', 'integer', 'min:0', 'max:99999999'],
            'bonus'      => ['nullable', 'integer', 'min:0', 'max:20'],
            'visible'    => ['required', 'boolean'],
        ], [
            'required' => ':attribute wajib diisi.',
            'integer'  => ':attribute harus berupa angka.',
        ], [
            'package_id' => 'Kelas', 'meetings' => 'Jumlah pertemuan', 'price' => 'Harga',
        ]);

        $package = Package::findOrFail($data['package_id']);
        $rows    = $this->toRows($package->prices ?? []);

        foreach ($rows as $row) {
            if ((int) $row['meetings'] === (int) $data['meetings']) {
                return back()->withErrors(['meetings' => "Pilihan {$data['meetings']}× pertemuan sudah ada di {$package->name}."], 'opsi')->withInput();
            }
        }

        $rows[] = ['meetings' => (int) $data['meetings'], 'price' => (int) $data['price'], 'bonus' => (int) ($data['bonus'] ?? 0), 'visible' => (bool) $data['visible']];

        $package->update(['prices' => $this->fromRows($rows)]);

        return redirect()->route('admin.packages.index')->with('success', 'Pilihan paket ditambahkan.');
    }

    /* ---------- Konversi: teks di database ⇄ angka di dashboard ---------- */

    /** @return array<int,array{meetings:int,price:int,bonus:int,visible:bool}> */
    private function toRows(array $prices): array
    {
        return array_values(array_map(fn ($p) => [
            'meetings' => preg_match('/\d+/', (string) ($p['label'] ?? ''), $m) ? (int) $m[0] : 1,
            'price'    => (int) preg_replace('/\D+/', '', (string) ($p['price'] ?? '0')),
            'bonus'    => preg_match('/\+\s*(\d+)/', (string) ($p['note'] ?? ''), $b) ? (int) $b[1] : 0,
            'visible'  => empty($p['hidden']),
        ], $prices));
    }

    private function fromRows(array $rows): array
    {
        $out = [];

        foreach ($rows as $row) {
            $bonus = (int) ($row['bonus'] ?? 0);

            $out[] = [
                'label'  => (int) $row['meetings'] . '× pertemuan',
                'price'  => 'Rp' . number_format((int) $row['price'], 0, ',', '.'),
                'note'   => $bonus > 0 ? "+{$bonus}× gratis" : null,
                'hidden' => array_key_exists('visible', $row) ? ! $row['visible'] : false,
            ];
        }

        usort($out, fn ($a, $b) => (int) $a['label'] <=> (int) $b['label']);

        return $out;
    }
}
