<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'packages'     => config('leo.packages'),
            'locations'    => config('leo.locations'),
            'gallery'      => config('leo.gallery'),
            'testimonials' => config('leo.testimonials'),
        ]);
    }

    public function daftar(Request $request): View
    {
        return view('daftar', [
            'packageOptions'  => config('leo.package_options'),
            'locations'       => config('leo.locations'),
            'days'            => config('leo.days'),
            'selectedPackage' => $request->query('paket'),
        ]);
    }

    /**
     * Validasi form lalu arahkan ke WhatsApp Pak Leo dengan pesan yang sudah terisi.
     */
    public function kirim(Request $request): RedirectResponse
    {
        $packages  = config('leo.package_options');
        $locations = collect(config('leo.locations'))->pluck('pool')->all();
        $days      = config('leo.days');

        $data = $request->validate([
            'parent_name' => ['required', 'string', 'max:100'],
            'phone'       => ['required', 'regex:/^[0-9+\s\-]{8,16}$/'],
            'child_name'  => ['required', 'string', 'max:100'],
            'gender'      => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'age'         => ['required', 'string', 'max:30'],
            'package'     => ['required', Rule::in(array_keys($packages))],
            'location'    => ['required', Rule::in($locations)],
            'schedule'    => ['nullable', 'array'],
            'schedule.*'  => [Rule::in($days)],
            'start_date'  => ['required', 'date', 'after_or_equal:today'],
        ], [
            'required'            => ':attribute wajib diisi.',
            'phone.regex'         => 'Nomor HP tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
        ], [
            'parent_name' => 'Nama orang tua',
            'phone'       => 'Nomor HP',
            'child_name'  => 'Nama anak',
            'gender'      => 'Jenis kelamin',
            'age'         => 'Usia anak',
            'package'     => 'Paket',
            'location'    => 'Lokasi kolam',
            'schedule'    => 'Jadwal',
            'start_date'  => 'Tanggal mulai',
        ]);

        $jadwal = empty($data['schedule']) ? '-' : implode(', ', $data['schedule']);

        $text = implode("\n", [
            'Halo Pak Leo, saya ingin mendaftar les renang.',
            '',
            '*Data Orang Tua*',
            'Nama: ' . $data['parent_name'],
            'No. HP: ' . $data['phone'],
            '',
            '*Data Anak*',
            'Nama: ' . $data['child_name'],
            'Jenis kelamin: ' . $data['gender'],
            'Usia: ' . $data['age'],
            'Paket: ' . $packages[$data['package']],
            '',
            '*Pilihan Kelas*',
            'Lokasi kolam: ' . $data['location'],
            'Jadwal: ' . $jadwal,
            'Mulai: ' . \Carbon\Carbon::parse($data['start_date'])->translatedFormat('d F Y'),
        ]);

        return redirect()->away(
            'https://wa.me/' . config('leo.whatsapp') . '?text=' . rawurlencode($text)
        );
    }
}
