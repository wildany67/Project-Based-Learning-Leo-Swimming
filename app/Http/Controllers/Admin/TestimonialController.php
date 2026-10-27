<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Kelola testimoni. Yang belum "tayang" tidak muncul di website. */
class TestimonialController extends Controller
{
    public function index(): View
    {
        return view('admin.testimonials.index', [
            'testimonials' => Testimonial::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.testimonials.form', [
            'testimonial' => new Testimonial([
                'is_published' => false,
                'sort_order'   => (int) Testimonial::max('sort_order') + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Testimonial::create($this->validated($request));

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni ditambahkan.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.form', ['testimonial' => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->validated($request));

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni diperbarui.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'quote'      => ['required', 'string', 'max:600'],
            'name'       => ['required', 'string', 'max:100'],
            'program'    => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'required' => ':attribute wajib diisi.',
        ], [
            'quote' => 'Isi testimoni',
            'name'  => 'Nama',
        ]);

        return [
            'quote'        => $data['quote'],
            'name'         => $data['name'],
            'program'      => $data['program'] ?? null,
            'initial'      => Testimonial::makeInitial($data['name']),
            'sort_order'   => $data['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published'),
        ];
    }
}
