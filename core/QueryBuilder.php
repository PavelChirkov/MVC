<?php

namespace core;
use Core\Database;

/**
 * Минимальный построитель запросов
 */
class QueryBuilder
{
    private $pdo;
    private $table;
    private $where = [];
    private $params = [];
    private $orderBy = '';
    private $limit = '';

    public function __construct($table)
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->table = $table;
    }

    /**
     * Добавление условия WHERE
     */
    public function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = ':' . str_replace('.', '_', $column) . '_' . count($this->params);
        $this->where[] = "$column $operator $placeholder";
        $this->params[$placeholder] = $value;

        return $this;
    }

    /**
     * Добавление сортировки
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }

    /**
     * Добавление лимита
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit = "LIMIT $offset, $limit";
        return $this;
    }

    /**
     * Получение всех записей
     */
    public function get()
    {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if ($this->orderBy) {
            $sql .= " " . $this->orderBy;
        }

        if ($this->limit) {
            $sql .= " " . $this->limit;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);

        return $stmt->fetchAll();
    }

    /**
     * Получение первой записи
     */
    public function first()
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Вставка записи
     */
    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    /**
     * Обновление записи
     */
    public function update($data)
    {
        $sets = [];
        foreach (array_keys($data) as $key) {
            $sets[] = "$key = :set_$key";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $stmt = $this->pdo->prepare($sql);

        // Привязка данных для обновления
        foreach ($data as $key => $value) {
            $stmt->bindValue(":set_$key", $value);
        }

        // Привязка параметров WHERE
        foreach ($this->params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();
    }

    /**
     * Удаление записей
     */
    public function delete()
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($this->params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();
    }
}