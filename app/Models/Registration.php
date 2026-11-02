<?php

namespace App\Models;

use App\Support\Schedule;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu pendaftar. Pendaftar yang sudah "terkonfirmasi" otomatis menjadi murid aktif.
 * Properti turunan (status_label, wa_url, dll.) dipakai langsung di Blade.
 */
class Registration extends Model
{
    public const WAITING   = 'menunggu';
    public const CONFIRMED = 'terkonfirmasi';
    public const DONE      = 'selesai';
    public const CANCELLED = 'dibatalkan';

    /** Status dan labelnya. Urutan = alur kerja admin. */
    public const STATUSES = [
        self::WAITING   => 'Menunggu',
        self::CONFIRMED => 'Terkonfirmasi',
        self::DONE      => 'Selesai',
        self::CANCELLED => 'Dibatalkan',
    ];

    protected $attributes = ['status' => self::WAITING];

    protected $fillable = [
        'code', 'parent_name', 'phone', 'child_name', 'gender', 'age',
        'package_key', 'package_label', 'location', 'schedule', 'schedule_time',
        'start_date', 'status', 'payment_checked', 'confirmed_at', 'notes',
    ];

    protected $casts = [
        'schedule'        => 'array',
        'start_date'      => 'date',
        'payment_checked' => 'boolean',
        'confirmed_at'    => 'datetime',
    ];

    protected static function booted(): void
    {
        // Kode LSC-001 dibuat dari nomor ID begitu pendaftar tersimpan.
        static::created(function (Registration $r) {
            if (! $r->code) {
                $r->forceFill(['code' => 'LSC-' . str_pad((string) $r->id, 3, '0', STR_PAD_LEFT)])->saveQuietly();
            }
        });
    }

    /* ---------- Relasi ---------- */

    public function logs(): HasMany
    {
        return $this->hasMany(RegistrationLog::class)->latest('created_at')->latest('id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /** Tambah satu baris ke riwayat perubahan. */
    public function log(string $text): void
    {
        $this->logs()->create(['text' => $text]);
    }

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
              ->orWhere('code', 'like', $like)
              ->orWhere('phone', 'like', $like);
        });
    }

    public function scopeWithStatus($query, ?string $status)
    {
        return array_key_exists((string) $status, self::STATUSES)
            ? $query->where('status', $status)
            : $query;
    }

    /** Murid yang sedang aktif belajar. */
    public function scopeActiveStudents($query)
    {
        return $query->where('status', self::CONFIRMED);
    }

    /** Tambahkan jumlah kehadiran sebagai meetings_used (hindari query berulang di tabel). */
    public function scopeWithMeetings($query)
    {
        return $query->withCount(['attendances as meetings_used' => fn ($q) => $q->where('present', true)]);
    }

    /* ---------- Properti turunan ---------- */

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status] ?? ucfirst((string) $this->status));
    }

    /** Nomor WhatsApp orang tua format internasional, mis. 62812xxxx. */
    protected function waNumber(): Attribute
    {
        return Attribute::get(fn () => Setting::toWhatsappNumber($this->phone) ?? '');
    }

    /** Link WhatsApp ke orang tua. Isi pesan diambil dari Pengaturan → Pesan WhatsApp otomatis. */
    protected function waUrl(): Attribute
    {
        return Attribute::get(function () {
            $text = strtr(Setting::get('wa_template', Setting::DEFAULT_TEMPLATE), [
                '{nama_ortu}' => $this->parent_name,
                '{nama_anak}' => $this->child_name,
                '{paket}'     => $this->package_label,
            ]);

            return 'https://wa.me/' . $this->wa_number . '?text=' . rawurlencode($text);
        });
    }

    /** Pesan pengingat perpanjangan paket. */
    protected function renewUrl(): Attribute
    {
        return Attribute::get(function () {
            $text = "Halo {$this->parent_name}, paket latihan {$this->child_name} tinggal {$this->meetings_left} pertemuan lagi. "
                  . 'Kalau ingin lanjut, silakan kabari kami untuk paket berikutnya. Terima kasih. Salam, Pak Leo.';

            return 'https://wa.me/' . $this->wa_number . '?text=' . rawurlencode($text);
        });
    }

    /** "Privat 8" / "Khusus 4" dari kunci paket (privat-8). */
    protected function packageShort(): Attribute
    {
        return Attribute::get(function () {
            [$slug, $number] = array_pad(explode('-', (string) $this->package_key, 2), 2, '');

            return trim(ucfirst($slug) . ' ' . preg_replace('/\D+/', '', $number));
        });
    }

    /** Total pertemuan: "8× pertemuan ... +1× gratis" → 9. */
    protected function meetingsTotal(): Attribute
    {
        return Attribute::get(function () {
            $label = (string) $this->package_label;
            $base  = preg_match('/(\d+)\s*×/u', $label, $m) ? (int) $m[1] : 0;
            $bonus = preg_match('/\+\s*(\d+)\s*×/u', $label, $b) ? (int) $b[1] : 0;

            return $base + $bonus;
        });
    }

    protected function meetingsUsed(): Attribute
    {
        return Attribute::get(function ($value) {
            return $value !== null ? (int) $value : (int) $this->attendances()->where('present', true)->count();
        });
    }

    protected function meetingsLeft(): Attribute
    {
        return Attribute::get(fn () => max($this->meetings_total - $this->meetings_used, 0));
    }

    /** "Nirwana" */
    protected function locationShort(): Attribute
    {
        return Attribute::get(fn () => Location::shortName($this->location));
    }

    /** "Senin, Rabu · 16.00" */
    protected function scheduleText(): Attribute
    {
        return Attribute::get(function () {
            $days = empty($this->schedule) ? 'Hari belum dipilih' : implode(', ', $this->schedule);

            return $this->schedule_time ? $days . ' · ' . Schedule::timeLabel($this->schedule_time) : $days;
        });
    }

    /** "Sen-Rab 16.00" (ringkas untuk tabel) */
    protected function scheduleShort(): Attribute
    {
        return Attribute::get(function () {
            $days = collect($this->schedule ?? [])->map(fn ($d) => Schedule::dayShort($d))->implode('-');
            $days = $days !== '' ? $days : '-';

            return $this->schedule_time ? $days . ' ' . Schedule::timeLabel($this->schedule_time) : $days;
        });
    }

    protected function startLabel(): Attribute
    {
        return Attribute::get(fn () => $this->start_date?->locale('id')->translatedFormat('d M Y') ?? '-');
    }

    protected function createdLabel(): Attribute
    {
        return Attribute::get(fn () => $this->created_at?->locale('id')->translatedFormat('j M Y, H.i') ?? '-');
    }

    protected function createdShort(): Attribute
    {
        return Attribute::get(fn () => $this->created_at?->locale('id')->translatedFormat('d M') ?? '-');
    }
}
