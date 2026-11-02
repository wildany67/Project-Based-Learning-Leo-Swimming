<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Testimoni. "Sudah izin" (is_published) = boleh tampil di website. */
class TestimonialController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, 'testi-new');
        $data['sort_order'] = (int) Testimonial::max('sort_order') + 1;

        Testimonial::create($data);

        return redirect()->route('admin.content.index', ['tab' => 'testimoni'])->with('success', 'Testimoni ditambahkan.');
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->validated($request, 'testi-' . $testimonial->id));

        return redirect()->route('admin.content.index', ['tab' => 'testimoni'])->with('success', 'Testimoni disimpan.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('admin.content.index', ['tab' => 'testimoni'])->with('success', 'Testimoni dihapus.');
    }

    private function validated(Request $request, string $bag): array
    {
        $data = $request->validateWithBag($bag, [
            'quote'   => ['required', 'string', 'max:600'],
            'name'    => ['required', 'string', 'max:100'],
            'program' => ['nullable', 'string', 'max:100'],
            'allowed' => ['required', 'boolean'],
        ], [
            'required' => ':attribute wajib diisi.',
        ], [
            'quote' => 'Kata-kata orang tua',
            'name'  => 'Nama',
        ]);

        return [
            'quote'        => $data['quote'],
            'name'         => $data['name'],
            'program'      => $data['program'] ?? null,
            'initial'      => Testimonial::makeInitial($data['name']),
            'is_published' => (bool) $data['allowed'],
        ];
    }
}
