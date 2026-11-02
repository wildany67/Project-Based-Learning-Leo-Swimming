<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pendaftar versi dashboard baru:
 *  - kode LSC-001, jam latihan, centang bukti transfer, waktu dikonfirmasi
 *  - status baru: menunggu → terkonfirmasi → selesai (atau dibatalkan)
 * Data lama dipindahkan ke status yang paling mirip.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique();
            $table->string('schedule_time', 5)->nullable();
            $table->boolean('payment_checked')->default(false);
            $table->timestamp('confirmed_at')->nullable();
        });

        foreach (['baru' => 'menunggu', 'dihubungi' => 'menunggu', 'aktif' => 'terkonfirmasi', 'batal' => 'dibatalkan'] as $old => $new) {
            DB::table('registrations')->where('status', $old)->update(['status' => $new]);
        }

        DB::table('registrations')->where('status', 'terkonfirmasi')->update([
            'payment_checked' => true,
            'confirmed_at'    => DB::raw('updated_at'),
        ]);

        foreach (DB::table('registrations')->whereNull('code')->orderBy('id')->pluck('id') as $id) {
            DB::table('registrations')->where('id', $id)->update(['code' => 'LSC-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT)]);
        }
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'schedule_time', 'payment_checked', 'confirmed_at']);
        });
    }
};
