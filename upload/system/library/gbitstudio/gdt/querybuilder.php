<?php

namespace GbitStudio\Gdt;

/**
 * Класс QueryBuilder - построитель SQL запросов
 */
class QueryBuilder
{
    /** @var object */
    protected $registry;

    /** @var object */
    protected $db;

    /** @var string */
    protected $table;

    /** @var string */
    protected $select = '*';

    /** @var array */
    protected $joins = [];

    /** @var array */
    protected $wheres = [];

    /** @var array */
    protected $orders = [];

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /**
     * @param object $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->db = $registry->get('db');
    }

    /**
     * Указывает таблицу для запроса
     *
     * @param string $table Имя таблицы без префикса
     * @return $this
     */
    public function table($table)
    {
        $this->table = DB_PREFIX . $table;
        return $this;
    }

    /**
     * Выбирает колонки
     *
     * @param string|array $columns
     * @return $this
     */
    public function select($columns = '*')
    {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    /**
     * Добавляет условие WHERE
     *
     * @param string $column
     * @param string|null $operator
     * @param mixed $value
     * @return $this
     */
    public function where($column, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type'     => 'AND',
            'column'   => $column,
            'operator' => $operator,
            'value'    => $value
        ];

        return $this;
    }

    /**
     * Добавляет условие OR WHERE
     *
     * @param string $column
     * @param string|null $operator
     * @param mixed $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type'     => 'OR',
            'column'   => $column,
            'operator' => $operator,
            'value'    => $value
        ];

        return $this;
    }

    /**
     * Добавляет JOIN
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     * @return $this
     */
    public function join($table, $first, $operator, $second, $type = 'INNER')
    {
        $this->joins[] = [
            'type'     => $type,
            'table'    => DB_PREFIX . $table,
            'first'    => $first,
            'operator' => $operator,
            'second'   => $second
        ];

        return $this;
    }

    public function leftJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Сортировка
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orders[] = [
            'column'    => $column,
            'direction' => strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC'
        ];

        return $this;
    }

    /**
     * Лимит
     *
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit = (int)$limit;
        $this->offset = (int)$offset;
        return $this;
    }

    /**
     * Получает все результаты
     *
     * @return array
     */
    public function get()
    {
        $sql = $this->buildSelect();
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * Алиас для get()
     */
    public function rows()
    {
        return $this->get();
    }

    /**
     * Получает первую строку
     *
     * @return array|null
     */
    public function first()
    {
        $this->limit(1);
        $sql = $this->buildSelect();
        $query = $this->db->query($sql);
        return isset($query->row) ? $query->row : null;
    }

    /**
     * Алиас для first()
     */
    public function row()
    {
        return $this->first();
    }

    /**
     * Получает количество записей
     *
     * @return int
     */
    public function count()
    {
        $oldSelect = $this->select;
        $this->select = 'COUNT(*) as total';
        $sql = $this->buildSelect();
        $this->select = $oldSelect;

        $query = $this->db->query($sql);
        return isset($query->row['total']) ? (int)$query->row['total'] : 0;
    }

    /**
     * Вставка данных
     *
     * @param array $data
     * @return int Last ID
     */
    public function insert(array $data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "`" . $key . "`";
            $values[] = "'" . $this->db->escape($value) . "'";
        }

        $sql = "INSERT INTO `" . $this->table . "` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->db->query($sql);

        return $this->db->getLastId();
    }

    /**
     * Обновление данных
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        $sets = [];

        foreach ($data as $key => $value) {
            $sets[] = "`" . $key . "` = '" . $this->db->escape($value) . "'";
        }

        $sql = "UPDATE `" . $this->table . "` SET " . implode(', ', $sets);
        $sql .= $this->buildWhere();

        $this->db->query($sql);
        return true;
    }

    /**
     * Удаление данных
     *
     * @return bool
     */
    public function delete()
    {
        $sql = "DELETE FROM `" . $this->table . "`";
        $sql .= $this->buildWhere();

        $this->db->query($sql);
        return true;
    }

    /**
     * Сборка SELECT запроса
     *
     * @return string
     */
    protected function buildSelect()
    {
        $sql = "SELECT " . $this->select . " FROM `" . $this->table . "`";

        foreach ($this->joins as $join) {
            $sql .= " " . $join['type'] . " JOIN `" . $join['table'] . "` ON " . $join['first'] . " " . $join['operator'] . " " . $join['second'];
        }

        $sql .= $this->buildWhere();

        if (!empty($this->orders)) {
            $sql .= " ORDER BY ";
            $parts = [];
            foreach ($this->orders as $order) {
                $parts[] = $order['column'] . " " . $order['direction'];
            }
            $sql .= implode(', ', $parts);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT " . (int)$this->offset . ", " . (int)$this->limit;
        }

        return $sql;
    }

    /**
     * Сборка WHERE условий
     *
     * @return string
     */
    protected function buildWhere()
    {
        if (empty($this->wheres)) {
            return "";
        }

        $sql = " WHERE ";
        foreach ($this->wheres as $i => $where) {
            if ($i > 0) {
                $sql .= " " . $where['type'] . " ";
            }

            $value = $where['value'];
            if (is_string($value)) {
                $value = "'" . $this->db->escape($value) . "'";
            } elseif ($value === null) {
                $value = "NULL";
            } elseif (is_bool($value)) {
                $value = $value ? '1' : '0';
            }

            $sql .= $where['column'] . " " . $where['operator'] . " " . $value;
        }

        return $sql;
    }
}
