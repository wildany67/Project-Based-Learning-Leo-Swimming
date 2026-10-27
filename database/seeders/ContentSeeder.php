<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use App\Models\Location;
use App\Models\Package;
use Illuminate\Database\Seeder;

/**
 * Menyalin isi awal dari config/leo.php ke database.
 * Aman dijalankan ulang: tabel yang sudah berisi tidak disentuh.
 *
 *   php artisan db:seed --class=ContentSeeder
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Package::count() === 0) {
            foreach (config('leo.packages') as $i => $p) {
                Package::create([
                    'slug'        => $p['key'],
                    'name'        => $p['name'],
                    'tagline'     => $p['tagline'],
                    'highlight'   => $p['highlight'],
                    'ghost'       => $p['ghost'],
                    'theme'       => $p['theme'],
                    'price_label' => $p['price_label'],
                    'prices'      => $p['prices'],
                    'features'    => $p['features'],
                    'cta'         => $p['cta'],
                    'sort_order'  => $i + 1,
                    'is_active'   => true,
                ]);
            }
        }

        if (Location::count() === 0) {
            foreach (config('leo.locations') as $i => $l) {
                Location::create([
                    'area'       => $l['area'],
                    'pool'       => $l['pool'],
                    'address'    => $l['address'],
                    'sort_order' => $i + 1,
                    'is_active'  => true,
                ]);
            }
        }

        if (GalleryItem::count() === 0) {
            foreach (config('leo.gallery') as $i => $g) {
                GalleryItem::create([
                    'caption'    => $g['caption'],
                    'size'       => $g['size'],
                    'tone'       => $g['tone'],
                    'image'      => $g['img'],
                    'sort_order' => $i + 1,
                    'is_active'  => true,
                ]);
            }
        }

        // Testimoni sengaja tidak diisi: tambahkan testimoni asli lewat dashboard.
    }
}
