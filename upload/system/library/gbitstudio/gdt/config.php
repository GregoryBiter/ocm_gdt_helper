<?php

namespace GbitStudio\Gdt;

use GbitStudio\Gdt\App;

/**
 * Класс Config - управление runtime-конфигурацией OpenCart (объект Config)
 */
class Config
{
    /** @var object */
    private $registry;
    /** @var object */
    private $config;

    /**
     * @param object $registry Реестр OpenCart
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->config = App::get('config');
    }

    /**
     * Получает значение из конфигурации
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value = $this->config->get($key);
        return ($value !== null && $value !== '') ? $value : $default;
    }

    /**
     * Устанавливает значение в конфигурацию
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->config->set($key, $value);
    }

    /**
     * Проверяет существование ключа в конфигурации
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->config->has($key);
    }
}
