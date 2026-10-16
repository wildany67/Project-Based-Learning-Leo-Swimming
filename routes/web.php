<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/daftar', [PageController::class, 'daftar'])->name('daftar');
Route::post('/daftar', [PageController::class, 'kirim'])->name('daftar.kirim');
