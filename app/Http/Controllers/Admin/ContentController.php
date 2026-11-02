<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Konten website: tab Galeri foto dan Testimoni. Simpan/hapus ada di controller masing-masing. */
class ContentController extends Controller
{
    /** Program yang bisa dipilih cepat di form testimoni. */
    public const PROGRAMS = ['Privat 4 pertemuan', 'Privat 6 pertemuan', 'Privat 8 pertemuan', 'Grup', 'Khusus SMKN 1'];

    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'testimoni' ? 'testimoni' : 'galeri';

        $gallery      = GalleryItem::orderBy('sort_order')->orderBy('id')->get();
        $testimonials = Testimonial::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.content.index', [
            'tab'          => $tab,
            'gallery'      => $gallery,
            'testimonials' => $testimonials,
            'programs'     => self::PROGRAMS,
            'galleryMax'   => 6,
            'pending'      => $testimonials->where('is_published', false)->count(),
        ]);
    }
}
