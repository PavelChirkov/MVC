<?php

namespace core;

use PDO;
use PDOException;

/**
 * Класс для управления подключением к базе данных
 */
class Database
{
    private static $instance = null;
    private $pdo;

    /**
     * Приватный конструктор (Singleton pattern)
     */
    private function __construct()
    {
        $config = require __DIR__ . '/../app/config/database.php';

        try {
            $dsn = sprintf(
                '%s:host=%s;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['database'],
                $config['charset']
            );

            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Получение экземпляра подключения
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Получение PDO объекта
     */
    public function getConnection()
    {
        return $this->pdo;
    }
}