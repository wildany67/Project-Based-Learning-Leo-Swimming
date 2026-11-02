<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/* ---------- Website publik ---------- */

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/daftar', [PageController::class, 'daftar'])->name('daftar');
Route::post('/daftar', [PageController::class, 'kirim'])->middleware('throttle:10,1')->name('daftar.kirim');

/* ---------- Admin: login ---------- */
// Nama route "login" dipakai Laravel untuk mengarahkan tamu yang belum login.

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->middleware('auth')->name('admin.logout');

/* ---------- Admin: dashboard (wajib login) ---------- */

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // Beranda
    Route::get('/', DashboardController::class)->name('dashboard');

    // Pendaftar. "export" harus di atas {registration} supaya tidak dianggap ID.
    Route::get('pendaftar', [RegistrationController::class, 'index'])->name('registrations.index');
    Route::get('pendaftar/export', [RegistrationController::class, 'export'])->name('registrations.export');
    Route::post('pendaftar', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::get('pendaftar/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
    Route::patch('pendaftar/{registration}/status', [RegistrationController::class, 'status'])->name('registrations.status');
    Route::patch('pendaftar/{registration}/bukti', [RegistrationController::class, 'payment'])->name('registrations.payment');
    Route::patch('pendaftar/{registration}/jadwal', [RegistrationController::class, 'schedule'])->name('registrations.schedule');
    Route::patch('pendaftar/{registration}/catatan', [RegistrationController::class, 'notes'])->name('registrations.notes');
    Route::delete('pendaftar/{registration}', [RegistrationController::class, 'destroy'])->name('registrations.destroy');

    // Murid dan kehadiran
    Route::get('murid', [StudentController::class, 'index'])->name('students.index');
    Route::post('murid/{registration}/kehadiran', [StudentController::class, 'attend'])->name('students.attend');

    // Jadwal
    Route::get('jadwal', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('jadwal', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::patch('jadwal/{slot}', [ScheduleController::class, 'update'])->name('schedule.update');
    Route::delete('jadwal/{slot}', [ScheduleController::class, 'destroy'])->name('schedule.destroy');

    // Kolam
    Route::get('kolam', [LocationController::class, 'index'])->name('locations.index');
    Route::post('kolam', [LocationController::class, 'store'])->name('locations.store');
    Route::put('kolam/{location}', [LocationController::class, 'update'])->name('locations.update');
    Route::patch('kolam/{location}/buka-tutup', [LocationController::class, 'toggle'])->name('locations.toggle');
    Route::delete('kolam/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

    // Paket dan harga
    Route::get('paket', [PackageController::class, 'index'])->name('packages.index');
    Route::put('paket', [PackageController::class, 'update'])->name('packages.update');
    Route::post('paket/pilihan', [PackageController::class, 'storeOption'])->name('packages.option');

    // Konten website (galeri + testimoni)
    Route::get('konten', [ContentController::class, 'index'])->name('content.index');
    Route::post('konten/testimoni', [TestimonialController::class, 'store'])->name('testimonials.store');
    Route::put('konten/testimoni/{testimonial}', [TestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('konten/testimoni/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');
    Route::post('konten/galeri', [GalleryController::class, 'store'])->name('gallery.store');
    Route::put('konten/galeri/{item}', [GalleryController::class, 'update'])->name('gallery.update');
    Route::delete('konten/galeri/{item}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    // Pengaturan dan bantuan
    Route::get('pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::put('pengaturan', [SettingController::class, 'update'])->name('settings.update');
    Route::put('pengaturan/sandi', [SettingController::class, 'password'])->name('settings.password');
    Route::get('bantuan', HelpController::class)->name('help.index');
});
