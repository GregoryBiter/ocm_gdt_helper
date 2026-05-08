<?php

namespace GbitStudio\Gdt;

/**
 * Класс Session - сервис для работы с сессией OpenCart
 */
class Session
{
    /** @var object */
    protected $registry;

    /** @var object */
    protected $session;

    /**
     * @param object $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->session = $registry->get('session');
    }

    /**
     * Получает значение из сессии
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->session->data[$key]) ? $this->session->data[$key] : $default;
    }

    /**
     * Устанавливает значение в сессию
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set($key, $value)
    {
        $this->session->data[$key] = $value;
        return $value;
    }

    /**
     * Проверяет наличие ключа в сессии
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->session->data[$key]);
    }

    /**
     * Удаляет значение из сессии
     *
     * @param string $key
     */
    public function delete($key)
    {
        if (isset($this->session->data[$key])) {
            unset($this->session->data[$key]);
        }
    }

    /**
     * Работа с flash-сообщениями
     *
     * @param string|null $key
     * @param mixed|null $message
     * @return mixed
     */
    public function flash($key = null, $message = null)
    {
        if ($key === null) {
            return $this->get('flash_messages', []);
        }

        if ($message !== null) {
            $flash_messages = $this->get('flash_messages', []);
            $flash_messages[$key] = $message;
            $this->set('flash_messages', $flash_messages);
            return $message;
        }

        $flash_messages = $this->get('flash_messages', []);
        if (isset($flash_messages[$key])) {
            $message = $flash_messages[$key];
            unset($flash_messages[$key]);
            $this->set('flash_messages', $flash_messages);
            return $message;
        }

        return null;
    }

    /**
     * Возвращает чистый объект сессии OpenCart
     *
     * @return object
     */
    public function native()
    {
        return $this->session;
    }
}
