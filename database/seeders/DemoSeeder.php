<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\Registration;
use App\Models\ScheduleSlot;
use App\Support\SiteContent;
use Illuminate\Database\Seeder;

/**
 * DATA CONTOH untuk mencoba dashboard (nama dan nomor di sini fiktif).
 * Jangan dijalankan di website yang sudah dipakai sungguhan.
 *
 *   php artisan db:seed --class=DemoSeeder
 *
 * Aman: kalau sudah ada pendaftar, seeder ini tidak melakukan apa-apa.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Registration::exists()) {
            $this->command?->warn('Sudah ada data pendaftar, DemoSeeder dilewati.');

            return;
        }

        $this->call(ContentSeeder::class);

        $locations = Location::orderBy('sort_order')->get();
        $packages  = SiteContent::packageOptions();

        // Slot contoh: tiap kolam punya jadwal Senin-Rabu-Jumat dan Selasa-Kamis.
        foreach ($locations as $location) {
            foreach ([1, 3, 5] as $day) {
                foreach (['15:30', '16:00'] as $time) {
                    ScheduleSlot::firstOrCreate(
                        ['location_id' => $location->id, 'weekday' => $day, 'time' => $time],
                        ['capacity' => 3, 'is_active' => true]
                    );
                }
            }
            foreach ([2, 4] as $day) {
                ScheduleSlot::firstOrCreate(
                    ['location_id' => $location->id, 'weekday' => $day, 'time' => '17:00'],
                    ['capacity' => 3, 'is_active' => true]
                );
            }
        }

        $pool = fn (int $i) => $locations[$i % $locations->count()]->pool;

        // [anak, orang tua, hp, jenis kelamin, usia, paket, kolam, hari, jam, status, hari lalu, jumlah hadir]
        $rows = [
            ['Raka Pratama',   'Bapak Dimas',  '0857 1122 3344', 'Laki-laki', '8 tahun',  'privat-8', 0, ['Senin', 'Rabu'],    '16:00', 'menunggu',      1, 0],
            ['Aisyah Putri',   'Ibu Sari',     '0812 3456 7890', 'Perempuan', '6 tahun',  'privat-4', 0, ['Selasa'],           '17:00', 'menunggu',      2, 0],
            ['Nadia Safitri',  'Ibu Rina',     '0813 5566 7788', 'Perempuan', '5 tahun',  'privat-4', 1, ['Senin'],            '16:00', 'terkonfirmasi', 6, 3],
            ['Bintang Mahendra', 'Bapak Yusuf', '0822 3344 5566', 'Laki-laki', '10 tahun', 'khusus-4', 2, ['Jumat'],            '15:30', 'terkonfirmasi', 9, 2],
            ['Keisha Amelia',  'Ibu Wulan',    '0878 9900 1122', 'Perempuan', '7 tahun',  'privat-8', 1, ['Rabu', 'Jumat'],    '16:00', 'terkonfirmasi', 12, 8],
            ['Farel Ramadhan', 'Bapak Andi',   '0812 3400 9988', 'Laki-laki', '9 tahun',  'privat-4', 0, ['Kamis'],            '17:00', 'terkonfirmasi', 14, 3],
            ['Salsa Kirana',   'Ibu Lisa',     '0819 8877 6655', 'Perempuan', '8 tahun',  'privat-6', 2, ['Senin', 'Kamis'],   '15:30', 'selesai',       40, 6],
            ['Dimas Aji',      'Bapak Joko',   '0856 4433 2211', 'Laki-laki', '11 tahun', 'privat-4', 0, ['Selasa'],           '17:00', 'dibatalkan',    20, 0],
        ];

        foreach ($rows as $i => [$child, $parent, $phone, $gender, $age, $package, $loc, $days, $time, $status, $daysAgo, $present]) {
            $created = now()->subDays($daysAgo);

            $r = Registration::unguarded(fn () => Registration::create([
                'parent_name'     => $parent,
                'phone'           => $phone,
                'child_name'      => $child,
                'gender'          => $gender,
                'age'             => $age,
                'package_key'     => $package,
                'package_label'   => $packages[$package] ?? $package,
                'location'        => $pool($loc),
                'schedule'        => $days,
                'schedule_time'   => $time,
                'start_date'      => $created->copy()->addDays(3)->toDateString(),
                'status'          => $status,
                'payment_checked' => in_array($status, ['terkonfirmasi', 'selesai'], true),
                'confirmed_at'    => in_array($status, ['terkonfirmasi', 'selesai'], true) ? $created->copy()->addDay() : null,
                'created_at'      => $created,
                'updated_at'      => $created,
            ]));

            $r->log('Pendaftaran masuk dari website');

            if ($r->confirmed_at) {
                $r->log('Status diubah menjadi Terkonfirmasi');
            }

            for ($n = 1; $n <= $present; $n++) {
                Attendance::create(['registration_id' => $r->id, 'date' => now()->subDays($n * 3 + $i)->toDateString(), 'present' => true]);
            }
        }
    }
}
