<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Pengaturan kunci → nilai. Dibaca sekali per permintaan.
 * Kalau tabel belum ada (belum `php artisan migrate`), semua pembacaan mengembalikan nilai bawaan.
 */
class Setting extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'key';
    protected $keyType = 'string';
    protected $fillable = ['key', 'value'];

    /** Pesan WhatsApp bawaan ke orang tua. */
    public const DEFAULT_TEMPLATE = 'Halo {nama_ortu}, terima kasih sudah mendaftarkan {nama_anak} di Leo Swimming Course. '
        . 'Untuk konfirmasi, mohon transfer sesuai paket ({paket}). Salam, Pak Leo.';

    /** Ukuran tulisan panel admin. */
    public const FONT_SIZES = ['normal' => 'Normal', 'big' => 'Besar', 'xl' => 'Sangat besar'];

    private static ?array $cache = null;

    /** @return array<string,string|null> */
    public static function allValues(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        try {
            self::$cache = Schema::hasTable('settings') ? static::query()->pluck('value', 'key')->all() : [];
        } catch (\Throwable) {
            self::$cache = [];
        }

        return self::$cache;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = self::allValues()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function bool(string $key, bool $default = true): bool
    {
        $value = self::allValues()[$key] ?? null;

        return $value === null ? $default : $value === '1';
    }

    /** @param array<string,string|null> $values */
    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        self::$cache = null;
    }

    /** "0831 9173 7789" → "6283191737789" (format wa.me). */
    public static function toWhatsappNumber(?string $input): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $input);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        return str_starts_with($digits, '62') ? $digits : '62' . $digits;
    }
}
