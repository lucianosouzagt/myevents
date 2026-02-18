<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\AdminUser;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:password-reset {--email=admin@myevents.com.br} {--password=}', function () {
    $email = (string) $this->option('email');
    $password = (string) $this->option('password');
    if ($password === '') {
        $this->error('Informe --password="NovaSenha"');
        return 1;
    }
    $valid =
        strlen($password) >= 8
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[a-z]/', $password)
        && preg_match('/[0-9]/', $password)
        && preg_match('/[^A-Za-z0-9]/', $password);
    if (!$valid) {
        $this->error('Senha não atende à política: min 8, maiúscula, minúscula, número e símbolo.');
        return 1;
    }
    $admin = AdminUser::where('email', $email)->first();
    if (!$admin) {
        $this->error("Admin não encontrado: {$email}");
        return 1;
    }
    $hash = Hash::make($password);
    DB::table('admin_users')->where('id', $admin->id)->update([
        'password' => $hash,
        'must_change_password' => true,
        'updated_at' => now(),
    ]);
    $this->info("Senha de {$email} redefinida e marcada para troca no próximo login.");
    return 0;
})->purpose('Redefine a senha do usuário admin (guard admin)');
