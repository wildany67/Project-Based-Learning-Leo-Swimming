<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Riwayat perubahan satu pendaftar ("Pendaftaran masuk dari website", "Status diubah ..."). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_logs');
    }
};
