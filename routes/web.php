<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\RegistrationController;
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

    Route::get('/', DashboardController::class)->name('dashboard');

    // Pendaftar. "export" harus di atas resource supaya tidak dianggap ID.
    Route::get('pendaftar/export', [RegistrationController::class, 'export'])->name('registrations.export');
    Route::patch('pendaftar/{registration}/status', [RegistrationController::class, 'status'])->name('registrations.status');
    Route::resource('pendaftar', RegistrationController::class)
        ->only(['index', 'show', 'update', 'destroy'])
        ->names('registrations')
        ->parameters(['pendaftar' => 'registration']);

    // Konten website
    Route::resource('paket', PackageController::class)->except('show')
        ->names('packages')->parameters(['paket' => 'package']);

    Route::resource('lokasi', LocationController::class)->except('show')
        ->names('locations')->parameters(['lokasi' => 'location']);

    Route::resource('testimoni', TestimonialController::class)->except('show')
        ->names('testimonials')->parameters(['testimoni' => 'testimonial']);

    Route::resource('galeri', GalleryController::class)->except('show')
        ->names('gallery')->parameters(['galeri' => 'item']);
});
