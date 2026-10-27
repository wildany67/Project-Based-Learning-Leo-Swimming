<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Membuat akun admin dashboard. Kalau email sudah ada, password-nya diganti.
 *
 *   php artisan leo:admin
 *   php artisan leo:admin --name="Pak Leo" --email=leo@gmail.com --password=leo12345
 *
 * Opsi --password berguna kalau terminal (mis. Windows) tidak bisa menerima ketikan
 * saat password disembunyikan.
 */
class CreateAdmin extends Command
{
    protected $signature = 'leo:admin {--name=} {--email=} {--password=}';

    protected $description = 'Buat atau perbarui akun admin dashboard';

    public function handle(): int
    {
        $name     = $this->option('name') ?: $this->ask('Nama admin', 'Pak Leo');
        $email    = $this->option('email') ?: $this->ask('Email admin');

        if ($this->option('password')) {
            $password = $confirm = $this->option('password');
        } else {
            $password = $this->secret('Password (minimal 8 karakter)');
            $confirm  = $this->secret('Ulangi password');
        }

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirm],
            [
                'name'     => ['required', 'string', 'max:100'],
                'email'    => ['required', 'email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'password.confirmed' => 'Kedua password tidak sama.',
                'password.min'       => 'Password minimal 8 karakter.',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password)]
        );

        $this->info($user->wasRecentlyCreated ? "Admin dibuat: {$email}" : "Password admin diperbarui: {$email}");
        $this->line('Login di: ' . url('/admin/login'));

        return self::SUCCESS;
    }
}
