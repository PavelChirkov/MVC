<?php

namespace core;

/**
 * Класс для работы с представлениями
 */
class View
{
    /**
     * Рендеринг шаблона
     */
    public function render($view, $data = [])
    {
        extract($data);

        $viewFile = __DIR__ . "/../app/Views/" . str_replace('.', '/', $view) . '.php';

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new \Exception("View {$view} not found");
        }
    }
}