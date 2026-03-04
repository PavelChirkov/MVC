<?php

namespace Core;

/**
 * Класс маршрутизатора с поддержкой HTTP методов
 */
class Router
{
    private $routes = [];
    private $params = [];
    private $requestMethod;

    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Добавление GET маршрута
     */
    public function get($route, $controller, $action, $middleware = null)
    {
        $this->addRoute('GET', $route, $controller, $action, $middleware);
    }

    /**
     * Добавление POST маршрута
     */
    public function post($route, $controller, $action, $middleware = null)
    {
        $this->addRoute('POST', $route, $controller, $action, $middleware);
    }

    /**
     * Добавление PUT маршрута
     */
    public function put($route, $controller, $action, $middleware = null)
    {
        $this->addRoute('PUT', $route, $controller, $action, $middleware);
    }

    /**
     * Добавление DELETE маршрута
     */
    public function delete($route, $controller, $action, $middleware = null)
    {
        $this->addRoute('DELETE', $route, $controller, $action, $middleware);
    }

    /**
     * Добавление маршрута для всех методов
     */
    public function any($route, $controller, $action, $middleware = null)
    {
        $this->addRoute('GET|POST|PUT|DELETE', $route, $controller, $action, $middleware);
    }

    /**
     * Добавление маршрута в коллекцию
     */
    private function addRoute($methods, $route, $controller, $action, $middleware)
    {
        // Разделяем методы если их несколько
        $methods = explode('|', $methods);

        foreach ($methods as $method) {
            $this->routes[] = [
                'method' => strtoupper($method),
                'route' => $route,
                'controller' => $controller,
                'action' => $action,
                'middleware' => $middleware
            ];
        }
    }

    /**
     * Запуск маршрутизатора
     */
    public function dispatch($url)
    {
        $url = $this->removeQueryString($url);
        $method = $this->requestMethod;

        // Для PUT и DELETE методов, которые могут использовать _method поле
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->match($route['route'], $url)) {
                // Проверка middleware
                if ($route['middleware'] === 'auth' && !isset($_SESSION['user_id'])) {
                    header('Location: /login');
                    exit;
                }

                if ($route['middleware'] === 'guest' && isset($_SESSION['user_id'])) {
                    header('Location: /admin');
                    exit;
                }

                $controller = "App\\Controllers\\{$route['controller']}";

                if (class_exists($controller)) {
                    $controllerObject = new $controller();
                    $action = $route['action'];

                    if (method_exists($controllerObject, $action)) {
                        // Получаем данные запроса
                        $requestData = $this->getRequestData($method);

                        // Подготавливаем параметры для вызова метода
                        $callParams = [];

                        // Добавляем параметры из URL
                        foreach ($this->params as $param) {
                            $callParams[] = $param;
                        }

                        // Добавляем данные запроса последним параметром
                        $callParams[] = $requestData;

                        // Вызываем метод контроллера с параметрами
                        call_user_func_array([$controllerObject, $action], $callParams);
                        return;
                    }
                }
            }
        }

        // 404 страница
        header("HTTP/1.0 404 Not Found");
        $this->show404();
    }

    /**
     * Получение данных запроса в зависимости от метода
     */
    private function getRequestData($method)
    {
        $data = [];

        switch ($method) {
            case 'GET':
                $data = $_GET;
                break;

            case 'POST':
                $data = $_POST;
                break;

            case 'PUT':
            case 'DELETE':
                // Для PUT и DELETE читаем raw input
                $input = file_get_contents('php://input');
                parse_str($input, $data);
                break;
        }

        return $data;
    }

    /**
     * Проверка соответствия маршрута
     */
    private function match($route, $url)
    {
        $pattern = $this->preparePattern($route);

        if (preg_match($pattern, $url, $matches)) {
            // Фильтруем только именованные параметры
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $this->params[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Подготовка регулярного выражения для маршрута
     */
    private function preparePattern($route)
    {
        $pattern = preg_replace('/\//', '\\/', $route);

        // Параметры с регулярными выражениями {id:\d+}
        $pattern = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $pattern);

        // Обычные параметры {id}
        $pattern = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-zA-Z0-9-_]+)', $pattern);

        $pattern = '/^' . $pattern . '$/';

        return $pattern;
    }

    /**
     * Удаление GET параметров из URL
     */
    private function removeQueryString($url)
    {
        if ($url != '') {
            $parts = explode('?', $url, 2);
            $url = $parts[0];
        }

        return $url;
    }

    /**
     * Отображение 404 страницы
     */
    private function show404()
    {
        if (file_exists(__DIR__ . '/../app/Views/errors/404.php')) {
            require __DIR__ . '/../app/Views/errors/404.php';
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The requested URL was not found on this server.</p>";
        }
    }
}