<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Data pendaftar dari form /daftar.
 * Label paket disimpan sebagai teks supaya riwayat tidak berubah saat harga diubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('parent_name');
            $table->string('phone', 30);
            $table->string('child_name');
            $table->string('gender', 20);
            $table->string('age', 30);
            $table->string('package_key', 60);
            $table->string('package_label');
            $table->string('location');
            $table->json('schedule')->nullable();
            $table->date('start_date');
            $table->string('status', 20)->default('baru')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
