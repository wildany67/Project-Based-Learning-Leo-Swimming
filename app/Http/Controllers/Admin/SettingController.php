<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Pengaturan: kontak, penerimaan pendaftar, pesan WhatsApp otomatis, ukuran tulisan, dan akun. */
class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'wa_main'     => Setting::get('wa_main', config('leo.whatsapp_display')),
            'wa_second'   => Setting::get('wa_second', config('leo.whatsapp_second')),
            'instagram'   => Setting::get('instagram', config('leo.instagram')),
            'accepting'   => Setting::bool('accept_registrations', true),
            'template'    => Setting::get('wa_template', Setting::DEFAULT_TEMPLATE),
            'fontSize'    => Setting::get('font_size', 'normal'),
            'fontSizes'   => Setting::FONT_SIZES,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'wa_main'              => ['required', 'regex:/^[0-9+\s\-]{8,18}$/'],
            'wa_second'            => ['nullable', 'regex:/^[0-9+\s\-]{8,18}$/'],
            'instagram'            => ['nullable', 'regex:/^@?[A-Za-z0-9._]{1,30}$/'],
            'accept_registrations' => ['required', 'boolean'],
            'wa_template'          => ['required', 'string', 'max:600'],
            'font_size'            => ['required', Rule::in(array_keys(Setting::FONT_SIZES))],
        ], [
            'required'     => ':attribute wajib diisi.',
            'wa_main.regex'   => 'Nomor WhatsApp tidak valid. Contoh: 0831 9173 7789',
            'wa_second.regex' => 'Nomor WhatsApp kedua tidak valid.',
            'instagram.regex' => 'Nama Instagram tidak valid. Contoh: leo_aswandani',
        ], [
            'wa_main'     => 'WhatsApp utama',
            'wa_template' => 'Pesan WhatsApp otomatis',
        ]);

        Setting::put([
            'wa_main'              => trim($data['wa_main']),
            'wa_second'            => trim((string) ($data['wa_second'] ?? '')),
            'instagram'            => ltrim(trim((string) ($data['instagram'] ?? '')), '@'),
            'accept_registrations' => $data['accept_registrations'] ? '1' : '0',
            'wa_template'          => $data['wa_template'],
            'font_size'            => $data['font_size'],
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Semua pengaturan sudah tersimpan.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('sandi', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required'         => 'Kata sandi lama wajib diisi.',
            'current_password.current_password' => 'Kata sandi lama salah.',
            'password.required'                 => 'Kata sandi baru wajib diisi.',
            'password.min'                      => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed'                => 'Pengulangan kata sandi tidak sama.',
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('admin.settings.index')->with('success', 'Kata sandi diganti.');
    }
}
