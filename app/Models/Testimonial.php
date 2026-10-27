<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Testimoni peserta. Hanya yang is_published = true tampil di website. */
class Testimonial extends Model
{
    protected $fillable = ['quote', 'name', 'program', 'initial', 'sort_order', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    /** "Ibu Sari Dewi" → "IS". Dipakai untuk lingkaran avatar. */
    public static function makeInitial(string $name): string
    {
        $words = preg_split('/\s+/', trim(preg_replace('/[^\p{L}\s]/u', '', $name)), -1, PREG_SPLIT_NO_EMPTY);
        $initial = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initial .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return $initial !== '' ? $initial : 'P';
    }
}
