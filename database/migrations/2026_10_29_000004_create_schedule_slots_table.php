<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Slot jadwal latihan: satu kolam, satu hari (1 = Senin ... 5 = Jumat), satu jam, dengan batas murid. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->string('time', 5);
            $table->unsignedTinyInteger('capacity')->default(3);
            $table->date('week_start')->nullable();   // kosong = berlaku setiap minggu
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['location_id', 'weekday', 'time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_slots');
    }
};
