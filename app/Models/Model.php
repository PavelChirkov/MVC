<?php

namespace App\Models;

use Core\QueryBuilder;

/**
 * Базовая модель
 */
abstract class Model
{
    protected $table;
    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder($this->table);
    }

    /**
     * Получение всех записей
     */
    public function all()
    {
        return $this->queryBuilder->get();
    }

    /**
     * Поиск по ID
     */
    public function find($id)
    {
        return $this->queryBuilder->where('id', '=', $id)->first();
    }

    /**
     * Создание новой записи
     */
    public function create($data)
    {
        return $this->queryBuilder->insert($data);
    }

    /**
     * Обновление записи
     */
    public function update($id, $data)
    {
        return $this->queryBuilder->where('id', '=', $id)->update($data);
    }

    /**
     * Удаление записи
     */
    public function delete($id)
    {
        return $this->queryBuilder->where('id', '=', $id)->delete();
    }

    /**
     * Получение построителя запросов
     */
    protected function query()
    {
        return $this->queryBuilder;
    }
}