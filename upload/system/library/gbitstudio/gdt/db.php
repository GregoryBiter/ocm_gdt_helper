<?php

namespace GbitStudio\Gdt;

/**
 * Класс DB - Менеджер базы данных (Laravel-style Facade Manager)
 */
class DB
{
    /** @var object */
    protected $registry;

    /** @var object */
    protected $db;

    /**
     * @param object $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->db = $registry->get('db');
    }

    /**
     * Возвращает новый экземпляр построителя запросов для указанной таблицы
     *
     * @param string $table Имя таблицы без префикса
     * @return QueryBuilder
     */
    public function table($table)
    {
        if (!class_exists('\\GbitStudio\\Gdt\\QueryBuilder')) {
            require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/querybuilder.php');
        }

        $builder = new QueryBuilder($this->registry);
        return $builder->table($table);
    }

    /**
     * Выполняет прямой SQL-запрос (совместимость с OC DB)
     *
     * @param string $sql
     * @return object
     */
    public function query($sql)
    {
        return $this->db->query($sql);
    }

    /**
     * Экранирует строку (совместимость с OC DB)
     *
     * @param string $value
     * @return string
     */
    public function escape($value)
    {
        return $this->db->escape($value);
    }

    /**
     * Возвращает ID последней вставленной записи (совместимость с OC DB)
     *
     * @return int
     */
    public function getLastId()
    {
        return $this->db->getLastId();
    }
}
