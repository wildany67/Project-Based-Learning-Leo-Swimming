<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Kehadiran murid per tanggal. Jumlah hadir = pertemuan yang sudah terpakai. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('present')->default(true);
            $table->timestamps();

            $table->unique(['registration_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
