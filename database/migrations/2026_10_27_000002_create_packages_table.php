<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Paket dan harga yang tampil di bagian "Program dan Harga". */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->string('highlight')->nullable();
            $table->string('ghost', 8)->nullable();
            $table->string('theme', 10)->default('navy');
            $table->string('price_label', 60)->default('Biaya per paket');
            $table->json('prices');
            $table->json('features')->nullable();
            $table->string('cta', 60);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
