<?php

use Core\Router;

// Создание экземпляра маршрутизатора
$router = new Router();


// GET маршруты для отображения форм
$router->get('/', 'AuthController', 'loginForm', 'guest');
$router->get('/login', 'AuthController', 'loginForm', 'guest');
$router->get('/register', 'AuthController', 'registerForm', 'guest');
$router->get('/logout', 'AuthController', 'logout', 'auth');

// POST маршруты для отправки форм
$router->post('/login', 'AuthController', 'login', 'guest');
$router->post('/register', 'AuthController', 'register', 'guest');

/*
|--------------------------------------------------------------------------
| Административная панель (Admin Routes)
|--------------------------------------------------------------------------
*/

// GET маршруты для администратора
$router->get('/admin', 'AdminController', 'index', 'auth');

return $router;