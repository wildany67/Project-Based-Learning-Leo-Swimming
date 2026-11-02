<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Foto galeri. File disimpan di public/images/gallery/ (tanpa storage:link). */
class GalleryController extends Controller
{
    private const FOLDER = 'images/gallery';

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, 'foto-new', imageRequired: true);

        $data['image']      = $this->storeImage($request);
        $data['sort_order'] = (int) GalleryItem::max('sort_order') + 1;

        GalleryItem::create($data);

        return $this->done('Foto ditambahkan.');
    }

    public function update(Request $request, GalleryItem $item): RedirectResponse
    {
        $data = $this->validated($request, 'foto-' . $item->id, imageRequired: false);

        if ($request->hasFile('image')) {
            $this->deleteImage($item->image);
            $data['image'] = $this->storeImage($request);
        }

        $item->update($data);

        return $this->done('Foto disimpan.');
    }

    public function destroy(GalleryItem $item): RedirectResponse
    {
        $this->deleteImage($item->image);
        $item->delete();

        return $this->done('Foto dihapus.');
    }

    private function done(string $message): RedirectResponse
    {
        return redirect()->route('admin.content.index', ['tab' => 'galeri'])->with('success', $message);
    }

    private function validated(Request $request, string $bag, bool $imageRequired): array
    {
        $data = $request->validateWithBag($bag, [
            'caption'   => ['required', 'string', 'max:150'],
            'size'      => ['required', Rule::in(array_keys(GalleryItem::SIZES))],
            'image'     => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_active' => ['required', 'boolean'],
        ], [
            'required'    => ':attribute wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format yang boleh: JPG, PNG, atau WEBP.',
            'image.max'   => 'Ukuran foto maksimal 4 MB.',
        ], [
            'caption' => 'Keterangan',
            'image'   => 'Foto',
        ]);

        return [
            'caption'   => $data['caption'],
            'size'      => $data['size'],
            'is_active' => (bool) $data['is_active'],
        ];
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
