<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Kelola foto galeri. File disimpan di public/images/gallery/ (tanpa storage:link). */
class GalleryController extends Controller
{
    private const FOLDER = 'images/gallery';

    public function index(): View
    {
        return view('admin.gallery.index', [
            'items' => GalleryItem::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.gallery.form', [
            'item' => new GalleryItem([
                'size'       => 'small',
                'tone'       => 'sky',
                'is_active'  => true,
                'sort_order' => (int) GalleryItem::max('sort_order') + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, imageRequired: true);
        $data['image'] = $this->storeImage($request);

        GalleryItem::create($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Foto ditambahkan.');
    }

    public function edit(GalleryItem $item): View
    {
        return view('admin.gallery.form', ['item' => $item]);
    }

    public function update(Request $request, GalleryItem $item): RedirectResponse
    {
        $data = $this->validated($request, imageRequired: false);

        if ($request->hasFile('image')) {
            $this->deleteImage($item->image);
            $data['image'] = $this->storeImage($request);
        }

        $item->update($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Foto diperbarui.');
    }

    public function destroy(GalleryItem $item): RedirectResponse
    {
        $this->deleteImage($item->image);
        $item->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Foto dihapus.');
    }

    private function validated(Request $request, bool $imageRequired): array
    {
        $data = $request->validate([
            'caption'    => ['required', 'string', 'max:150'],
            'size'       => ['required', Rule::in(array_keys(GalleryItem::SIZES))],
            'tone'       => ['required', Rule::in(array_keys(GalleryItem::TONES))],
            'image'      => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'required'   => ':attribute wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format yang boleh: JPG, PNG, atau WEBP.',
            'image.max'   => 'Ukuran foto maksimal 4 MB.',
        ], [
            'caption' => 'Keterangan',
            'image'   => 'Foto',
        ]);

        unset($data['image']); // file diproses terpisah di storeImage()

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = $request->boolean('is_active');

        return $data;
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'foto')
              . '-' . Str::lower(Str::random(6))
              . '.' . $file->extension();

        File::ensureDirectoryExists(public_path(self::FOLDER));
        $file->move(public_path(self::FOLDER), $name);

        return $name;
    }

    /** basename() mencegah nama file diarahkan keluar dari folder galeri. */
    private function deleteImage(?string $name): void
    {
        if ($name) {
            File::delete(public_path(self::FOLDER . '/' . basename($name)));
        }
    }
}
