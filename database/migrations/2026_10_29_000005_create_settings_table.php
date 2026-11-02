<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Pengaturan sederhana (kunci → nilai): nomor WhatsApp, Instagram, pesan otomatis, dll. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 60)->primary();
            $table->text('value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
