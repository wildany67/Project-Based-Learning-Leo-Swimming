<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/** Satu foto galeri. File fisik ada di public/images/gallery/. */
class GalleryItem extends Model
{
    /** Bentuk kotak di grid galeri. */
    public const SIZES = [
        'big'   => 'Besar (2 baris)',
        'wide'  => 'Lebar',
        'tall'  => 'Tinggi (2 baris)',
        'small' => 'Kecil',
    ];

    /** Warna blok kalau belum ada foto. */
    public const TONES = [
        'sky'  => 'Biru muda',
        'navy' => 'Biru tua',
        'gold' => 'Emas',
    ];

    protected $fillable = ['caption', 'size', 'tone', 'image', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** URL foto, atau null kalau belum ada / file hilang. */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->image) {
                return null;
            }

            $path = 'images/gallery/' . basename($this->image);

            return file_exists(public_path($path)) ? asset($path) : null;
        });
    }
}
