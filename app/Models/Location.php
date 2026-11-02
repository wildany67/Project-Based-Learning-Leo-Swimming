<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kolam latihan.
 * is_active = "Menerima pendaftar": kolam yang ditutup tidak tampil di website dan form daftar.
 */
class Location extends Model
{
    /** Wilayah dan contoh kecamatannya (untuk kartu pilihan di dashboard). */
    public const AREAS = [
        'Surabaya Timur'   => 'Rungkut, Sukolilo',
        'Surabaya Selatan' => 'Wonokromo, Jambangan',
        'Surabaya Barat'   => 'Lakarsantri, Sambikerep',
        'Surabaya Utara'   => 'Kenjeran, Bulak',
    ];

    protected $fillable = ['area', 'pool', 'address', 'maps_url', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function slots(): HasMany
    {
        return $this->hasMany(ScheduleSlot::class);
    }

    /** "Kolam Renang Nirwana Eksekutif" → "Nirwana" (untuk teks ringkas di jadwal). */
    public static function shortName(?string $pool): string
    {
        $name = trim((string) preg_replace('/^(kolam renang|kolam)\s+/i', '', (string) $pool));

        return $name === '' ? '-' : (explode(' ', $name)[0]);
    }

    protected function short(): Attribute
    {
        return Attribute::get(fn () => self::shortName($this->pool));
    }
}
