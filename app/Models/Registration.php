<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Satu pendaftar dari form /daftar.
 * Properti turunan (status_label, wa_url, dll.) dipakai langsung di Blade.
 */
class Registration extends Model
{
    /** Daftar status dan labelnya. Urutan = alur kerja admin. */
    public const STATUSES = [
        'baru'      => 'Baru',
        'dihubungi' => 'Dihubungi',
        'aktif'     => 'Aktif',
        'selesai'   => 'Selesai',
        'batal'     => 'Batal',
    ];

    protected $fillable = [
        'parent_name', 'phone', 'child_name', 'gender', 'age',
        'package_key', 'package_label', 'location', 'schedule',
        'start_date', 'status', 'notes',
    ];

    protected $casts = [
        'schedule'   => 'array',
        'start_date' => 'date',
    ];

    /* ---------- Filter ---------- */

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        $like = '%' . $term . '%';

        return $query->where(function ($q) use ($like) {
            $q->where('child_name', 'like', $like)
              ->orWhere('parent_name', 'like', $like)
              ->orWhere('phone', 'like', $like);
        });
    }

    public function scopeWithStatus($query, ?string $status)
    {
        return array_key_exists((string) $status, self::STATUSES)
            ? $query->where('status', $status)
            : $query;
    }

    /* ---------- Properti turunan ---------- */

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status] ?? ucfirst((string) $this->status));
    }

    /** Link WhatsApp ke orang tua, pesan pembuka sudah terisi. */
    protected function waUrl(): Attribute
    {
        return Attribute::get(function () {
            $digits = preg_replace('/\D+/', '', (string) $this->phone);

            if (str_starts_with($digits, '0')) {
                $digits = '62' . substr($digits, 1);
            } elseif (str_starts_with($digits, '8')) {
                $digits = '62' . $digits;
            }

            $text = "Halo, kami dari Leo Swimming Course. Terima kasih sudah mendaftarkan {$this->child_name}. "
                  . 'Kami ingin mengonfirmasi jadwal dan lokasi latihan.';

            return 'https://wa.me/' . $digits . '?text=' . rawurlencode($text);
        });
    }

    protected function scheduleText(): Attribute
    {
        return Attribute::get(fn () => empty($this->schedule) ? 'Belum dipilih' : implode(', ', $this->schedule));
    }

    protected function startLabel(): Attribute
    {
        return Attribute::get(fn () => $this->start_date?->locale('id')->translatedFormat('d M Y') ?? '-');
    }

    protected function createdLabel(): Attribute
    {
        return Attribute::get(fn () => $this->created_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-');
    }

    protected function createdShort(): Attribute
    {
        return Attribute::get(fn () => $this->created_at?->locale('id')->translatedFormat('d M') ?? '-');
    }
}
