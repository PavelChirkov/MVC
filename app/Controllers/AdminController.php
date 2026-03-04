<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Контроллер административной панели
 */
class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Главная страница администратора
     */
    public function index()
    {
        $data = [
            'user_name' => $_SESSION['user_name'] ?? 'Гость'
        ];

        $this->view->render('admin.dashboard', $data);
    }
}