<?php

namespace App\Models;

/**
 * Модель пользователя
 */
class User extends Model
{
    protected $table = 'users';

    /**
     * Поиск пользователя по email
     */
    public function findByEmail($email)
    {
        return $this->query()
            ->where('email', '=', $email)
            ->first();
    }

    /**
     * Проверка пароля
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Хеширование пароля
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}