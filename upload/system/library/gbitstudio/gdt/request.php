<?php

namespace GbitStudio\Gdt;

/**
 * Класс Request - сервис для работы с запросом OpenCart
 */
class Request
{
    /** @var object */
    protected $registry;

    /** @var object */
    protected $request;

    /**
     * @param object $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->request = $registry->get('request');
    }

    /**
     * Получает значение из GET, POST, FILES, COOKIE или SERVER
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (isset($this->request->get[$key])) {
            return $this->request->get[$key];
        }

        if (isset($this->request->post[$key])) {
            return $this->request->post[$key];
        }

        return $default;
    }

    /**
     * Получает значение только из GET
     */
    public function query($key, $default = null)
    {
        return isset($this->request->get[$key]) ? $this->request->get[$key] : $default;
    }

    /**
     * Получает значение только из POST
     */
    public function post($key, $default = null)
    {
        return isset($this->request->post[$key]) ? $this->request->post[$key] : $default;
    }

    /**
     * Возвращает нативный объект запроса OpenCart
     *
     * @return object
     */
    public function native()
    {
        return $this->request;
    }

    /**
     * Магический доступ к свойствам (server, cookie, etc.)
     */
    public function __get($name)
    {
        return isset($this->request->{$name}) ? $this->request->{$name} : null;
    }
}
