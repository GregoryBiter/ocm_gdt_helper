<?php

namespace GbitStudio\Gdt;

/**
 * Класс App - провайдер основных экземпляров OpenCart
 * Предоставляет статический доступ ко всем компонентам системы OpenCart
 */
class App
{
    /** @var object */
    private static $registry;

    /** @var Container Контейнер зависимостей */
    private static $container;

    /**
     * Инициализация провайдера
     *
     * @param object $registry
     */
    public function __construct($registry)
    {
        self::init($registry);
    }

    public static function init($registry)
    {
        self::$registry = $registry;

        if (!class_exists('\\GbitStudio\\Gdt\\Container')) {
            require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/container.php');
        }

        self::$container = new Container($registry);
    }

    /**
     * Получает экземпляр контейнера
     *
     * @return Container
     */
    public static function container()
    {
        return self::$container;
    }

    /**
     * Получение реестра OpenCart
     *
     * @return object
     */
    public static function registry($key = null)
    {
        if ($key !== null) {
            return self::$registry->get($key);
        }
        return self::$registry;
    }

    /**
     * Получает экземпляр приложения GDT или сервис из него
     *
     * @param string|null $key Ключ для получения сервиса из приложения
     * @return mixed Экземпляр приложения или запрошенный сервис
     */
    public static function app($key = null)
    {
        if ($key !== null) {
            return self::get($key);
        }
        return self::container();
    }

    /**
     * Получает экземпляр класса Config или конкретное значение
     *
     * @param string|null $key
     * @param mixed $default
     * @return Config|mixed
     */
    public static function config($key = null, $default = null)
    {
        $config = self::$container->get('config');

        if ($key === null) {
            return $config;
        }

        return $config->get($key, $default);
    }

    /**
     * Получает экземпляр класса Setting или конкретную настройку из БД
     *
     * @param string|null $code
     * @param string|null $key
     * @param mixed $default
     * @return Setting|mixed
     */
    public static function setting($code = null, $key = null, $default = null)
    {
        $setting = self::$container->get('setting');

        if ($code === null) {
            return $setting;
        }

        return $setting->get($code, $key, $default);
    }

    /**
     * Получает или настраивает объект ответа
     *
     * @param mixed|null $content Контент для установки в ответ
     * @return mixed Объект ответа приложения
     */
    public static function response($content = null)
    {
        $response = self::get('response');

        if ($content !== null) {
            $response->setOutput($content);
        }

        return $response;
    }

    /**
     * Получает объект запроса из приложения
     *
     * @param string|null $key Ключ для получения значения из запроса
     * @return mixed Объект запроса из приложения
     */
    public static function request($key = null)
    {
        $request = self::get('request');

        if ($key !== null) {
            if (isset($request->get[$key]))
                return $request->get[$key];
            if (isset($request->post[$key]))
                return $request->post[$key];
            return null;
        }

        return $request;
    }

    /**
     * Перенаправляет пользователя на указанный URL
     *
     * @param string $link URL для перенаправления
     * @param int $status HTTP-статус перенаправления
     * @return void
     */
    public static function redirect($link, $status = 302)
    {
        return self::get('response')->redirect($link, $status);
    }

    /**
     * Рендерит и возвращает HTML-шаблон с данными
     *
     * @param string $route Путь к шаблону
     * @param array $data Данные для передачи в шаблон
     * @return string Отрендеренный HTML-код
     */
    public static function view($route, $data = [])
    {
        return self::get('load')->view($route, $data);
    }

    /**
     * Универсальный метод для получения переводов OpenCart.
     * 
     * @param string|null $key  Ключ перевода ИЛИ 'путь/к/файлу.ключ'
     * @param string|null $file Путь к файлу перевода (если не указан в $key через точку)
     * @param mixed       ...$args Дополнительные аргументы для замены плейсхолдеров (%s, %d) в строке
     * 
     * @return string|array|null Переведенная строка, массив переводов или сам ключ, если перевод не найден
     */
    public static function __($key = null, $file = null, ...$args)
    {
        if ($key === null && $file === null) {
            return self::get('language')->all();
        }

        // Авто-определение файла из ключа (например, 'common/header.text_home')
        if ($file === null && $key !== null && strpos($key, '.') !== false) {
            $last_dot = strrpos($key, '.');
            $file = substr($key, 0, $last_dot);
            $key = substr($key, $last_dot + 1);
        }

        if ($file !== null) {
            $data = self::get('load')->language($file);

            if ($key === null || $key === '') {
                return $data;
            }

            $text = isset($data[$key]) ? $data[$key] : $key;
        } else {
            $text = self::get('language')->get($key);
        }

        if (!empty($args) && is_string($text)) {
            return vsprintf($text, $args);
        }

        return $text;
    }

    /**
     * Проверяет, находится ли пользователь в админке
     *
     * @return bool True если в админке, False если в каталоге
     */
    public static function isAdmin()
    {
        if (defined('DIR_CATALOG')) {
            return true;
        }

        try {
            if (self::has('user') && self::get('user')->isLogged()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Генерирует URL-путь для OpenCart
     *
     * @param string $route Маршрут в формате OpenCart
     * @param mixed $args Параметры URL
     * @param bool $secure Использовать ли HTTPS
     * @return string Сформированный URL
     */
    public static function route($route, $args = '', $secure = true)
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

        if (self::isAdmin()) {
            try {
                $session = self::get('session');
                $user_token = isset($session->data['user_token']) ? $session->data['user_token'] : '';

                if ($user_token && strpos($args, 'user_token=') === false) {
                    $prefix = (strlen($args) > 0) ? '&' : '';
                    $args = 'user_token=' . $user_token . $prefix . $args;
                }
            } catch (\Exception $e) {
            }
        }

        return self::get('url')->link($route, $args, $secure);
    }

    /**
     * Получает или устанавливает значение в сессии
     *
     * @param string|null $key Ключ сессии
     * @param mixed|null $value Значение для установки
     * @return mixed Значение из сессии или объект сессии
     */
    public static function session($key = null, $value = null)
    {
        $session = self::get('session');

        if ($key === null) {
            return $session;
        }

        if ($value !== null) {
            $session->data[$key] = $value;
            return $value;
        }

        return isset($session->data[$key]) ? $session->data[$key] : null;
    }

    /**
     * Получает объект URL
     *
     * @return object Объект URL
     */
    public static function url()
    {
        return self::get('url');
    }

    /**
     * Получает или устанавливает значение в кеше
     *
     * @param string $key Ключ кеша
     * @param mixed|null $value Значение для установки
     * @param int $expire Время жизни кеша в секундах
     * @return mixed Значение из кеша или объект кеша
     */
    public static function cache($key = null, $value = null, $expire = 3600)
    {
        $cache = self::get('cache');

        if ($key === null) {
            return $cache;
        }

        if ($value !== null) {
            $cache->set($key, $value);
            return $value;
        }

        return $cache->get($key);
    }

    /**
     * Записывает сообщение в лог
     *
     * @param string $message Сообщение для записи
     * @param string $filename Имя файла лога
     * @return void
     */
    public static function logWrite($message, $filename = 'error.log')
    {
        try {
            self::get('log')->write($message);
        } catch (\Exception $e) {
            if (defined('DIR_LOGS')) {
                $logPath = constant('DIR_LOGS') . $filename;
            } else {
                $logPath = '/tmp/' . $filename;
            }
            error_log('[' . date('Y-m-d H:i:s') . '] ' . $message, 3, $logPath);
        }
    }

    /**
     * Отправляет JSON-ответ
     *
     * @param mixed $data Данные для отправки
     * @param int $status HTTP-статус
     * @return void
     */
    public static function jsonResponse($data, $status = 200)
    {
        $response = self::get('response');
        $response->addHeader('Content-Type: application/json');

        if ($status !== 200) {
            $response->addHeader('HTTP/1.1 ' . $status);
        }

        $response->setOutput(json_encode($data));
    }

    /**
     * Получает объект загрузчика
     *
     * @return object Объект загрузчика
     */
    public static function load()
    {
        return self::get('load');
    }

    /**
     * Устанавливает или получает flash-сообщение
     *
     * @param string|null $key Ключ сообщения
     * @param string|null $message Текст сообщения
     * @return mixed Flash-сообщение или null
     */
    public static function flash($key = null, $message = null)
    {
        if ($key === null) {
            return self::session('flash_messages', []);
        }

        if ($message !== null) {
            $flash_messages = self::session('flash_messages', []);
            $flash_messages[$key] = $message;
            self::session('flash_messages', $flash_messages);
            return $message;
        }

        $flash_messages = self::session('flash_messages', []);
        if (isset($flash_messages[$key])) {
            $message = $flash_messages[$key];
            unset($flash_messages[$key]);
            self::session('flash_messages', $flash_messages);
            return $message;
        }

        return null;
    }

    /**
     * Устанавливает flash-сообщение об успехе
     *
     * @param string $message
     */
    public static function flashSuccess($message)
    {
        return self::flash('success', $message);
    }

    /**
     * Устанавливает flash-сообщение об ошибке
     *
     * @param string $message
     */
    public static function flashError($message)
    {
        return self::flash('error', $message);
    }

    /**
     * Получает текущего авторизованного пользователя (админа или клиента)
     *
     * @return object|null
     */
    public static function user()
    {
        if (self::isAdmin()) {
            return self::admin();
        } else {
            return self::customer();
        }
    }

    /**
     * Получает объект текущего администратора
     *
     * @return object|null
     */
    public static function admin()
    {
        return self::has('user') ? self::get('user') : null;
    }

    /**
     * Получает объект текущего клиента
     *
     * @return object|null
     */
    public static function customer()
    {
        return self::has('customer') ? self::get('customer') : null;
    }

    /**
     * Регистрирует привязку в контейнере
     */
    public static function bind($abstract, $concrete = null, $shared = false)
    {
        self::$container->bind($abstract, $concrete, $shared);
    }

    /**
     * Регистрирует синглтон в контейнере
     */
    public static function singleton($abstract, $concrete = null)
    {
        self::$container->singleton($abstract, $concrete);
    }

    /**
     * Регистрирует готовый экземпляр в контейнере
     */
    public static function instance($abstract, $instance)
    {
        self::$container->instance($abstract, $instance);
    }

    /**
     * Извлекает экземпляр из контейнера
     */
    public static function make($abstract)
    {
        return self::$container->make($abstract);
    }

    /**
     * Получение произвольного компонента (Mini DI или Registry)
     *
     * @param string $key Ключ компонента
     * @return mixed
     */
    public static function get($key)
    {
        return self::$container->get($key);
    }

    /**
     * Проверка существования компонента
     *
     * @param string $key Ключ компонента
     * @return bool
     */
    public static function has($key)
    {
        return self::$container->has($key);
    }

    /**
     * Добавление компонента в контейнер
     *
     * @param string $key Ключ компонента
     * @param mixed $value Значение
     */
    public static function set($key, $value)
    {
        self::$container->set($key, $value);
        self::$registry->set($key, $value);
    }
}