<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

/**
 * Контроллер аутентификации
 */
class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();

        // Старт сессии если не запущена
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Страница регистрации
     */
    public function registerForm()
    {
        $this->view->render('auth.register');
    }

    /**
     * Обработка регистрации
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Валидация
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Имя обязательно';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный email';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Пароль должен быть не менее 6 символов';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Пароли не совпадают';
        }

        // Проверка существования пользователя
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Пользователь с таким email уже существует';
        }

        if (!empty($errors)) {
            $this->view->render('auth.register', ['errors' => $errors]);
            return;
        }

        // Создание пользователя
        $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $this->userModel->hashPassword($password)
        ]);

        $_SESSION['success'] = 'Регистрация успешна. Войдите в систему.';
        $this->redirect('/login');
    }

    /**
     * Страница входа
     */
    public function loginForm()
    {
        $this->view->render('auth.login');
    }

    /**
     * Обработка входа
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $this->redirect('/admin');
        } else {
            $this->view->render('auth.login', [
                'error' => 'Неверный email или пароль'
            ]);
        }
    }

    /**
     * Выход из системы
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}