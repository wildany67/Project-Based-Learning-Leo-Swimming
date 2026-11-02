<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Alamat kolam boleh kosong dulu (muncul tanda "Alamat belum diisi" di dashboard). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
        });
    }
};
