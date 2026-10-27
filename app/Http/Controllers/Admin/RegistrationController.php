<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Daftar pendaftar: cari, filter status, ubah status, catatan, hapus, export CSV. */
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

        // Jumlah per status untuk tab (tidak ikut terfilter pencarian).
        $counts = Registration::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.registrations.index', [
            'registrations' => $registrations,
            'search'        => $search,
            'status'        => array_key_exists($status, Registration::STATUSES) ? $status : '',
            'counts'        => $counts,
            'allCount'      => $counts->sum(),
        ]);
    }

    public function show(Registration $registration): View
    {
        return view('admin.registrations.show', ['registration' => $registration]);
    }

    /** Simpan status dan catatan dari halaman detail. */
    public function update(Request $request, Registration $registration): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Registration::STATUSES))],
            'notes'  => ['nullable', 'string', 'max:2000'],
        ]);

        $registration->update($data);

        return back()->with('success', 'Data pendaftar disimpan.');
    }

    /** Ubah status cepat dari daftar. */
    public function status(Request $request, Registration $registration): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Registration::STATUSES))],
        ]);

        $registration->update($data);

        return back()->with('success', "Status {$registration->child_name} diubah menjadi " . Registration::STATUSES[$data['status']] . '.');
    }

    public function destroy(Registration $registration): RedirectResponse
    {
        $name = $registration->child_name;
        $registration->delete();

        return redirect()->route('admin.registrations.index')->with('success', "Data {$name} dihapus.");
    }

    /** Unduh semua pendaftar (ikut filter yang sedang aktif) sebagai CSV yang bisa dibuka di Excel. */
    public function export(Request $request): StreamedResponse
    {
        $query = Registration::search(trim((string) $request->query('q')))
            ->withStatus((string) $request->query('status'))
            ->latest();

        $filename = 'pendaftar-leo-swimming-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM supaya Excel membaca UTF-8 dengan benar

            fputcsv($out, ['Tanggal daftar', 'Nama anak', 'Jenis kelamin', 'Usia', 'Nama orang tua', 'No. HP',
                'Paket', 'Lokasi', 'Jadwal', 'Mulai', 'Status', 'Catatan']);

            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->created_label, $r->child_name, $r->gender, $r->age, $r->parent_name, $r->phone,
                        $r->package_label, $r->location, $r->schedule_text, $r->start_label,
                        $r->status_label, $r->notes,
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
