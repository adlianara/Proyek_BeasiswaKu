<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'beasiswaku:admin {email : Email admin} {--name=Administrator : Nama admin}';
    protected $description = 'Buat akun administrator BeasiswaKu';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) { $this->error('Email tidak valid.'); return self::FAILURE; }
        $password = $this->secret('Kata sandi admin (minimal 12 karakter)');
        if (strlen((string) $password) < 12) { $this->error('Kata sandi minimal 12 karakter.'); return self::FAILURE; }
        User::updateOrCreate(['email' => $email], ['name' => $this->option('name'), 'password' => Hash::make($password), 'is_admin' => true]);
        $this->info('Akun admin siap digunakan melalui /pengelola/masuk.');
        return self::SUCCESS;
    }
}
