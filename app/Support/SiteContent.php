<?php

namespace App\Support;

use App\Models\GalleryItem;
use App\Models\Location;
use App\Models\Package;
use App\Models\Testimonial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sumber isi website publik.
 *
 * Aturan: kalau tabelnya sudah ada dan berisi, pakai database (yang diedit admin).
 * Kalau belum (mis. belum menjalankan `php artisan migrate`), pakai config/leo.php.
 * Bentuk array yang dikembalikan sama persis, jadi view publik tidak perlu tahu sumbernya.
 */
class SiteContent
{
    /** @var array<string,bool> */
    private static array $cache = [];

    /* ---------- Paket ---------- */

    public static function packages(): array
    {
        if (! self::hasRows('packages')) {
            return array_map(function ($p) {
                $p['cta_package'] = self::optionKeys($p['key'], $p['prices'])[0] ?? '';

                return $p;
            }, config('leo.packages'));
        }

        return Package::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn (Package $p) => [
                'key'         => $p->slug,
                'name'        => $p->name,
                'tagline'     => $p->tagline,
                'highlight'   => $p->highlight,
                'ghost'       => $p->ghost,
                'theme'       => $p->theme,
                'price_label' => $p->price_label,
                'prices'      => $p->prices ?? [],
                'features'    => $p->features ?? [],
                'cta'         => $p->cta,
                'cta_package' => self::optionKeys($p->slug, $p->prices ?? [])[0] ?? '',
            ])
            ->all();
    }

    /**
     * Pilihan paket untuk dropdown form daftar.
     * Bentuk: ['privat-4' => 'Kelas Privat/Grup · 4× pertemuan (Rp300.000)', ...]
     */
    public static function packageOptions(): array
    {
        $options = [];

        foreach (self::packages() as $package) {
            $keys = self::optionKeys($package['key'], $package['prices']);

            foreach ($package['prices'] as $i => $row) {
                $label = $package['name'] . ' · ' . $row['label'] . ' (' . $row['price'] . ')';

                if (! empty($row['note'])) {
                    $label .= ', ' . $row['note'];
                }

                $options[$keys[$i]] = $label;
            }
        }

        return $options;
    }

    /** Kunci unik tiap baris harga: slug + angka pertama di label (4× → "privat-4"). */
    private static function optionKeys(string $slug, array $prices): array
    {
        $keys = [];

        foreach ($prices as $i => $row) {
            $number = preg_match('/\d+/', $row['label'], $m) ? $m[0] : (string) ($i + 1);
            $key = $slug . '-' . $number;

            while (in_array($key, $keys, true)) {
                $key .= 'b';
            }

            $keys[$i] = $key;
        }

        return $keys;
    }

    /* ---------- Lokasi ---------- */

    public static function locations(): array
    {
        if (! self::hasRows('locations')) {
            return config('leo.locations');
        }

        return Location::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn (Location $l) => [
                'area'     => $l->area,
                'pool'     => $l->pool,
                'address'  => $l->address,
                'maps_url' => $l->maps_url,
            ])
            ->all();
    }

    /* ---------- Galeri ---------- */

    public static function gallery(): array
    {
        if (! self::hasRows('gallery_items')) {
            return config('leo.gallery');
        }

        return GalleryItem::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn (GalleryItem $g) => [
                'caption' => $g->caption,
                'size'    => $g->size,
                'tone'    => $g->tone,
                'img'     => $g->image,
            ])
            ->all();
    }

    /* ---------- Testimoni ---------- */

    /** Tabel sudah ada → hanya yang tayang (kosong = bagian testimoni disembunyikan). */
    public static function testimonials(): array
    {
        if (! self::tableExists('testimonials')) {
            return config('leo.testimonials');
        }

        return Testimonial::where('is_published', true)->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn (Testimonial $t) => [
                'quote'   => $t->quote,
                'name'    => $t->name,
                'program' => $t->program,
                'initial' => $t->initial,
            ])
            ->all();
    }

    /* ---------- Util ---------- */

    private static function tableExists(string $table): bool
    {
        return self::$cache["exists:$table"] ??= self::safe(fn () => Schema::hasTable($table));
    }

    private static function hasRows(string $table): bool
    {
        return self::$cache["rows:$table"] ??= self::tableExists($table)
            && self::safe(fn () => DB::table($table)->exists());
    }

    /** Database belum siap tidak boleh membuat website error. */
    private static function safe(callable $check): bool
    {
        try {
            return (bool) $check();
        } catch (\Throwable) {
            return false;
        }
    }
}
