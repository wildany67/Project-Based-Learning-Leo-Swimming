<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Support\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Halaman publik: beranda, form daftar, dan pengiriman form ke WhatsApp. */
class PageController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'packages'     => SiteContent::packages(),
            'locations'    => SiteContent::locations(),
            'gallery'      => SiteContent::gallery(),
            'testimonials' => SiteContent::testimonials(),
        ]);
    }

    public function daftar(Request $request): View
    {
        return view('daftar', [
            'packageOptions'  => SiteContent::packageOptions(),
            'locations'       => SiteContent::locations(),
            'days'            => config('leo.days'),
            'selectedPackage' => $request->query('paket'),
        ]);
    }

    /**
     * Validasi form, simpan ke database (supaya muncul di dashboard admin),
     * lalu arahkan ke WhatsApp Pak Leo dengan pesan yang sudah terisi.
     */
    public function kirim(Request $request): RedirectResponse
    {
        $packages  = SiteContent::packageOptions();
        $locations = collect(SiteContent::locations())->pluck('pool')->all();
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
            'required'                  => ':attribute wajib diisi.',
            'phone.regex'               => 'Nomor HP tidak valid.',
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

        $schedule = $data['schedule'] ?? [];

        $this->saveRegistration($data, $packages[$data['package']], $schedule);

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
            'Jadwal: ' . ($schedule ? implode(', ', $schedule) : '-'),
            'Mulai: ' . \Carbon\Carbon::parse($data['start_date'])->locale('id')->translatedFormat('d F Y'),
        ]);

        return redirect()->away(
            'https://wa.me/' . config('leo.whatsapp') . '?text=' . rawurlencode($text)
        );
    }

    /**
     * Gagal menyimpan tidak boleh menghalangi pendaftar mengirim WhatsApp,
     * jadi error hanya dicatat di storage/logs.
     */
    private function saveRegistration(array $data, string $packageLabel, array $schedule): void
    {
        try {
            Registration::create([
                'parent_name'   => $data['parent_name'],
                'phone'         => $data['phone'],
                'child_name'    => $data['child_name'],
                'gender'        => $data['gender'],
                'age'           => $data['age'],
                'package_key'   => $data['package'],
                'package_label' => $packageLabel,
                'location'      => $data['location'],
                'schedule'      => $schedule,
                'start_date'    => $data['start_date'],
                'status'        => 'baru',
            ]);
        } catch (\Throwable $e) {
            Log::error('Pendaftaran gagal disimpan ke database: ' . $e->getMessage());
        }
    }
}
