<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\Location;
use App\Models\Package;
use App\Models\Registration;
use App\Models\Testimonial;
use Illuminate\View\View;

/** Halaman ringkasan: angka penting, grafik pendaftar, dan pendaftar terbaru. */
class DashboardController extends Controller
{
    /** Jumlah hari pada grafik. */
    private const CHART_DAYS = 14;

    public function __invoke(): View
    {
        $byStatus = Registration::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusTotals = [];
        foreach (Registration::STATUSES as $key => $label) {
            $statusTotals[$key] = ['label' => $label, 'total' => (int) ($byStatus[$key] ?? 0)];
        }

        $total = array_sum(array_column($statusTotals, 'total'));

        return view('admin.dashboard', [
            'stats' => [
                'total'      => $total,
                'thisMonth'  => Registration::where('created_at', '>=', now()->startOfMonth())->count(),
                'thisWeek'   => Registration::where('created_at', '>=', now()->startOfWeek())->count(),
                'new'        => $statusTotals['baru']['total'],
                'active'     => $statusTotals['aktif']['total'],
            ],
            'chart'        => $this->chart(),
            'statusTotals' => $statusTotals,
            'byPackage'    => $this->topBy('package_label'),
            'byLocation'   => $this->topBy('location'),
            'latest'       => Registration::latest()->limit(6)->get(),
            'content'      => [
                'packages'     => Package::where('is_active', true)->count(),
                'locations'    => Location::where('is_active', true)->count(),
                'testimonials' => Testimonial::where('is_published', true)->count(),
                'gallery'      => GalleryItem::where('is_active', true)->count(),
            ],
        ]);
    }

    /** Pendaftar per hari untuk CHART_DAYS hari terakhir (termasuk hari ini). */
    private function chart(): array
    {
        $from = now()->subDays(self::CHART_DAYS - 1)->startOfDay();

        $perDay = Registration::where('created_at', '>=', $from)
            ->get(['created_at'])
            ->countBy(fn ($r) => $r->created_at->format('Y-m-d'));

        $days = [];
        for ($i = 0; $i < self::CHART_DAYS; $i++) {
            $date = $from->copy()->addDays($i);
            $days[] = [
                'day'   => $date->format('j'),
                'label' => $date->locale('id')->translatedFormat('d M'),
                'count' => (int) ($perDay[$date->format('Y-m-d')] ?? 0),
                'today' => $date->isToday(),
            ];
        }

        $max = max(1, max(array_column($days, 'count')));

        foreach ($days as &$day) {
            $day['pct'] = $day['count'] > 0 ? max(8, round($day['count'] / $max * 100)) : 0;
        }
        unset($day);

        return ['days' => $days, 'total' => array_sum(array_column($days, 'count')), 'max' => $max];
    }

    /** 5 nilai terbanyak untuk satu kolom, mis. paket atau lokasi, lengkap dengan persen. */
    private function topBy(string $column): array
    {
        $rows = Registration::selectRaw("$column as name, count(*) as total")
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $max = max(1, (int) $rows->max('total'));

        return $rows->map(fn ($r) => [
            'name'  => $r->name,
            'total' => (int) $r->total,
            'pct'   => round($r->total / $max * 100),
        ])->all();
    }
}
