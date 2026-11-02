<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/** Halaman Bantuan: panduan singkat untuk Pak Leo. */
class HelpController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.help.index');
    }
}
