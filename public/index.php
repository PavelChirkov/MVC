<?php

// Автозагрузка классов через Composer
require __DIR__ . '/../vendor/autoload.php';

// Старт сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Получение маршрутизатора
$router = require __DIR__ . '/../routes/web.php';

// Получение текущего URL
$url = $_SERVER['REQUEST_URI'];
$url = str_replace('/public', '', $url); // Удаление /public из URL

// Запуск маршрутизатора
$router->dispatch($url);