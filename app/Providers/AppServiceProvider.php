<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Nomor WhatsApp dan Instagram yang diisi di Dashboard → Pengaturan
     * menggantikan nilai di config/leo.php untuk seluruh website.
     */
    public function boot(): void
    {
        $values = Setting::allValues();

        if (! $values) {
            return;
        }

        $display = $values['wa_main'] ?? null;

        if ($display && ($number = Setting::toWhatsappNumber($display))) {
            config(['leo.whatsapp' => $number, 'leo.whatsapp_display' => $display]);
        }

        if (array_key_exists('wa_second', $values)) {
            config(['leo.whatsapp_second' => $values['wa_second'] ?: null]);
        }

        if (! empty($values['instagram'])) {
            config(['leo.instagram' => ltrim($values['instagram'], '@')]);
        }
    }
}
