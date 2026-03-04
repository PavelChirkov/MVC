<?php

namespace core;
use \core\View;

/**
 * Базовый контроллер
 */
abstract class Controller
{
    /**
     * @var View
     */
    protected $view;

    public function __construct()
    {
        $this->view = new \Core\View();
    }

    /**
     * Редирект на указанный URL
     */
    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}