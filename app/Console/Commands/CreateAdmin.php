<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {email? : Электронная почта администратора}';
    protected $description = 'Создать администратора панели управления Сливкино';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Электронная почта администратора');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Укажите корректный адрес электронной почты.');
            return self::FAILURE;
        }

        $name = $this->ask('Имя администратора', 'Администратор');
        $password = $this->secret('Пароль (не менее 10 символов)');
        if (strlen((string) $password) < 10) {
            $this->error('Пароль должен содержать не менее 10 символов.');
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password), 'is_admin' => true]
        );

        $this->info("Администратор {$user->email} готов к входу в панель управления.");
        return self::SUCCESS;
    }
}
