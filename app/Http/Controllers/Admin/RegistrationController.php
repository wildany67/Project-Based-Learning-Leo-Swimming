<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Registration;
use App\Support\RegistrationForm;
use App\Support\Schedule;
use App\Support\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Pendaftar: daftar + filter, detail, tambah manual, ubah status,
 * jadwal, catatan, hapus, dan unduh Excel (CSV).
 */
class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $status = (string) $request->query('status');

        $registrations = Registration::search($search)
            ->withStatus($status)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Jumlah per status untuk chip (tidak ikut terfilter pencarian).
        $counts = Registration::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.registrations.index', [
            'registrations' => $registrations,
            'search'        => $search,
            'status'        => array_key_exists($status, Registration::STATUSES) ? $status : '',
            'counts'        => $counts,
            'allCount'      => $counts->sum(),
            'form'          => RegistrationForm::options(),
        ]);
    }

    public function show(Registration $registration): View
    {
        $registration->load('logs');

        return view('admin.registrations.show', [
            'registration' => $registration,
            'days'         => Schedule::DAYS,
            'times'        => RegistrationForm::timeChoices($registration->location),
        ]);
    }

    /** Tambah pendaftar manual (orang tua mendaftar lewat WhatsApp atau datang langsung). */
    public function store(Request $request): RedirectResponse
    {
        $packages = SiteContent::packageOptions();
        $pools    = Location::pluck('pool')->all();

        $data = $request->validateWithBag('manual', [
            'child_name'    => ['required', 'string', 'max:100'],
            'gender'        => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'age'           => ['required', 'string', 'max:30'],
            'parent_name'   => ['required', 'string', 'max:100'],
            'phone'         => ['required', 'regex:/^[0-9+\s\-]{8,16}$/'],
            'package'       => ['required', Rule::in(array_keys($packages))],
            'location'      => ['required', Rule::in($pools)],
            'schedule'      => ['nullable', 'array'],
            'schedule.*'    => [Rule::in(Schedule::DAYS)],
            'schedule_time' => ['nullable', 'regex:/^\d{1,2}[.:]\d{2}$/'],
            'status'        => ['required', Rule::in([Registration::WAITING, Registration::CONFIRMED])],
            'start_date'    => ['nullable', 'date'],
        ], [
            'required'            => ':attribute wajib diisi.',
            'phone.regex'         => 'Nomor WhatsApp tidak valid.',
            'schedule_time.regex' => 'Format jam salah. Contoh: 16.00',
        ], [
            'child_name' => 'Nama anak', 'gender' => 'Jenis kelamin', 'age' => 'Usia',
            'parent_name' => 'Nama orang tua', 'phone' => 'Nomor WhatsApp', 'package' => 'Paket',
            'location' => 'Kolam', 'status' => 'Status',
        ]);

        $confirmed = $data['status'] === Registration::CONFIRMED;

        $registration = Registration::create([
            'parent_name'     => $data['parent_name'],
            'phone'           => $data['phone'],
            'child_name'      => $data['child_name'],
            'gender'          => $data['gender'],
            'age'             => $data['age'],
            'package_key'     => $data['package'],
            'package_label'   => $packages[$data['package']],
            'location'        => $data['location'],
            'schedule'        => $data['schedule'] ?? [],
            'schedule_time'   => Schedule::normalizeTime($data['schedule_time'] ?? null),
            'start_date'      => $data['start_date'] ?? now()->toDateString(),
            'status'          => $data['status'],
            'payment_checked' => $confirmed,
            'confirmed_at'    => $confirmed ? now() : null,
        ]);

        $registration->log('Ditambahkan manual oleh ' . $request->user()->name);

        if ($confirmed) {
            $registration->log('Status diubah menjadi Terkonfirmasi');
        }

        return redirect()->route('admin.registrations.show', $registration)
            ->with('success', "{$registration->child_name} ditambahkan ({$registration->code}).");
    }

    /**
     * Ubah status. Menandai "terkonfirmasi" mensyaratkan bukti transfer sudah dicek,
     * karena itu yang membuat anak menjadi murid aktif dan mengisi slot jadwal.
     */
    public function status(Request $request, Registration $registration): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Registration::STATUSES))],
        ]);

        if ($data['status'] === Registration::CONFIRMED && ! $registration->payment_checked) {
            return back()->with('error', 'Centang dulu "Bukti transfer sudah saya cek" sebelum menandai terkonfirmasi.');
        }

        $registration->status = $data['status'];

        if ($data['status'] === Registration::CONFIRMED && ! $registration->confirmed_at) {
            $registration->confirmed_at = now();
        }

        $registration->save();
        $registration->log('Status diubah menjadi ' . Registration::STATUSES[$data['status']] . ' oleh ' . $request->user()->name);

        return back()->with('success', "Status {$registration->child_name}: " . Registration::STATUSES[$data['status']] . '.');
    }

    /** Centang / lepas "Bukti transfer sudah saya cek". */
    public function payment(Request $request, Registration $registration): RedirectResponse
    {
        $registration->update(['payment_checked' => $request->boolean('payment_checked')]);

        if ($registration->payment_checked) {
            $registration->log('Bukti transfer sudah dicek oleh ' . $request->user()->name);
        }

        return back();
    }

    /** Hari dan jam latihan. Jam ini yang menentukan slot mana yang terisi. */
    public function schedule(Request $request, Registration $registration): RedirectResponse
    {
        $data = $request->validate([
            'schedule'      => ['nullable', 'array'],
            'schedule.*'    => [Rule::in(Schedule::DAYS)],
            'schedule_time' => ['nullable', 'regex:/^\d{1,2}[.:]\d{2}$/'],
        ], ['schedule_time.regex' => 'Format jam salah. Contoh: 16.00']);

        $registration->update([
            'schedule'      => array_values($data['schedule'] ?? []),
            'schedule_time' => Schedule::normalizeTime($data['schedule_time'] ?? null),
        ]);
        $registration->log('Jadwal diubah menjadi ' . $registration->schedule_text);

        return back()->with('success', 'Jadwal disimpan.');
    }

    public function notes(Request $request, Registration $registration): RedirectResponse
    {
        $data = $request->validate(['notes' => ['nullable', 'string', 'max:2000']]);

        $registration->update(['notes' => $data['notes'] ?? null]);

        return back()->with('success', 'Catatan disimpan.');
    }

    public function destroy(Registration $registration): RedirectResponse
    {
        $name = $registration->child_name;
        $registration->delete();

        return redirect()->route('admin.registrations.index')->with('success', "Data {$name} dihapus.");
    }

    /** Unduh pendaftar (ikut filter aktif) sebagai CSV yang langsung terbuka di Excel. */
    public function export(Request $request): StreamedResponse
    {
        $query = Registration::search(trim((string) $request->query('q')))
            ->withStatus((string) $request->query('status'))
            ->latest();

        $filename = 'pendaftar-leo-swimming-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM supaya Excel membaca UTF-8 dengan benar

            fputcsv($out, ['Kode', 'Tanggal daftar', 'Nama anak', 'Jenis kelamin', 'Usia', 'Nama orang tua', 'No. HP',
                'Paket', 'Kolam', 'Jadwal', 'Mulai', 'Status', 'Catatan']);

            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->code, $r->created_label, $r->child_name, $r->gender, $r->age, $r->parent_name, $r->phone,
                        $r->package_label, $r->location, $r->schedule_text, $r->start_label,
                        $r->status_label, $r->notes,
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
