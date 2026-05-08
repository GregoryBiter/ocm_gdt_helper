<?php

namespace GbitStudio\Gdt;

/**
 * Класс Cache - сервис для работы с кешем OpenCart
 */
class Cache
{
    /** @var object */
    protected $registry;

    /** @var object */
    protected $cache;

    /**
     * @param object $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->cache = $registry->get('cache');
    }

    /**
     * Получает значение из кеша
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->cache->get($key);
    }

    /**
     * Устанавливает значение в кеш
     *
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return mixed
     */
    public function set($key, $value, $expire = 3600)
    {
        $this->cache->set($key, $value); // В OC 3.x нет 3-го аргумента в set() объекта Cache
        return $value;
    }

    /**
     * Удаляет значение из кеша
     *
     * @param string $key
     */
    public function delete($key)
    {
        $this->cache->delete($key);
    }

    /**
     * Возвращает нативный объект кеша
     */
    public function native()
    {
        return $this->cache;
    }
}
