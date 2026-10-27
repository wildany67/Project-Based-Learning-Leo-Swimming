<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Foto galeri. File gambar disimpan di public/images/gallery/. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('caption');
            $table->string('size', 10)->default('small');   // big | wide | tall | small
            $table->string('tone', 10)->default('sky');     // warna blok kalau belum ada foto
            $table->string('image')->nullable();            // nama file saja
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
