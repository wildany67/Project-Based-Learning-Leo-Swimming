<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/** Paket dan harga. Baris harga dan keunggulan disimpan sebagai JSON. */
class Package extends Model
{
    protected $fillable = [
        'slug', 'name', 'tagline', 'highlight', 'ghost', 'theme',
        'price_label', 'prices', 'features', 'cta', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'prices'    => 'array',
        'features'  => 'array',
        'is_active' => 'boolean',
    ];

    /* ---------- Parser teks → array (untuk form admin) ---------- */

    /**
     * Satu baris = satu harga, format: "4× pertemuan | Rp300.000 | +1× gratis"
     * Kolom ketiga (catatan) boleh dikosongkan.
     */
    public static function parsePrices(string $text): array
    {
        $rows = [];

        foreach (preg_split('/\R/', $text) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            [$label, $price, $note] = array_pad(array_map('trim', explode('|', $line)), 3, '');

            if ($label === '' || $price === '') {
                continue;
            }

            $rows[] = ['label' => $label, 'price' => $price, 'note' => $note !== '' ? $note : null];
        }

        return $rows;
    }

    /** Satu baris = satu keunggulan. */
    public static function parseLines(?string $text): array
    {
        return array_values(array_filter(
            array_map('trim', preg_split('/\R/', (string) $text)),
            fn ($line) => $line !== ''
        ));
    }

    /* ---------- Properti turunan (array → teks, untuk mengisi form) ---------- */

    protected function pricesText(): Attribute
    {
        return Attribute::get(fn () => collect($this->prices ?? [])
            ->map(fn ($row) => trim($row['label'] . ' | ' . $row['price'] . (! empty($row['note']) ? ' | ' . $row['note'] : '')))
            ->implode("\n"));
    }

    protected function featuresText(): Attribute
    {
        return Attribute::get(fn () => implode("\n", $this->features ?? []));
    }
}
