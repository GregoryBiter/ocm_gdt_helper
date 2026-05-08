<?php

namespace GbitStudio\Gdt;

use GbitStudio\Gdt\App;

/**
 * Класс Url - сервис для работы с URL и маршрутизацией OpenCart
 */
class Url
{
    /** @var object */
    protected $url;

    public function __construct()
    {
        $this->url = App::get('url');
    }

    /**
     * Генерирует URL-путь для OpenCart
     *
     * @param string $route Маршрут в формате OpenCart
     * @param mixed $args Параметры URL
     * @param bool $secure Использовать ли HTTPS
     * @return string Сформированный URL
     */
    public function link($route, $args = '', $secure = true)
    {
        if (is_array($args)) {
            $queryString = '';
            foreach ($args as $key => $value) {
                if (strlen($queryString) > 0) {
                    $queryString .= '&';
                }
                $queryString .= urlencode($key) . '=' . urlencode($value);
            }
            $args = $queryString;
        }

        if ($this->isAdmin()) {
            try {
                $session = App::get('session');
                $user_token = isset($session->data['user_token']) ? $session->data['user_token'] : '';

                if ($user_token && strpos($args, 'user_token=') === false) {
                    $prefix = (strlen($args) > 0) ? '&' : '';
                    $args = 'user_token=' . $user_token . $prefix . $args;
                }
            } catch (\Exception $e) {
            }
        }

        return $this->url->link($route, $args, $secure);
    }

    /**
     * Проверяет, находится ли пользователь в админке
     *
     * @return bool True если в админке, False если в каталоге
     */
    public function isAdmin()
    {
        if (defined('DIR_CATALOG')) {
            return true;
        }

        try {
            $user = App::get('user');
            if ($user && $user->isLogged()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Возвращает нативный объект URL
     */
    public function native()
    {
        return $this->url;
    }
}
