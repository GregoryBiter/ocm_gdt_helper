<?php

namespace GbitStudio\GDT\Engine;

/**
 * Класс GDT - провайдер основных экземпляров OpenCart
 * Предоставляет статический доступ ко всем компонентам системы OpenCart
 */
class GDT {
    /** @var object */
    private static $registry;
    
    /** @var array Кэш экземпляров */
    private static $instances = [];
    
    /**
     * Инициализация провайдера
     *
     * @param object $registry
     */
    public static function init($registry) {
        self::$registry = $registry;
    }
    
    /**
     * Получение реестра OpenCart
     *
     * @return object
     */
    public static function registry($key = null) {
        if ($key !== null) {
            if (!self::$registry->has($key)) {
                throw new \Exception('Key not found in registry: ' . $key);
            }
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
    public static function app($key = null) {
        if ($key !== null) {
            if (!self::$registry->has($key)) {
                throw new \Exception('Key not found in registry: ' . $key);
            }
            return self::$registry->get($key);
        }
        return self::$registry;
    }
    
    /**
     * Получает или устанавливает значение конфигурации
     *
     * @param string|null $key Ключ конфигурации
     * @param mixed|null $data Данные для установки
     * @return mixed Значение конфигурации или все настройки
     */
    public static function config($key = null, $data = null) {
        $config = self::$registry->get('config');
        
        if ($data !== null && $key !== null) {
            $config->set($key, $data);
            return $config->get($key);
        }
        
        if ($key !== null) {
            return $config->get($key);
        }
        
        return $config;
    }
    
    /**
     * Получает или настраивает объект ответа
     *
     * @param mixed|null $content Контент для установки в ответ
     * @return mixed Объект ответа приложения
     */
    public static function response($content = null) {
        if (!self::$registry->has('gtd_response')) {
            self::$registry->set('gtd_response', new \GbitStudio\GDT\Http\Response(self::app('response')));
        }
        
        $gtd_response = self::$registry->get('gtd_response');
        
        if ($content !== null) {
            $gtd_response->setOutput($content);
        }
        
        return $gtd_response;
    }
    
    /**
     * Получает объект запроса из приложения
     *
     * @param string|null $key Ключ для получения значения из запроса
     * @return mixed Объект запроса из приложения
     */
    public static function request($key = null) {
        if (!self::$registry->has('gtd_request')) {
            self::$registry->set('gtd_request', new \GbitStudio\GDT\Http\Request(self::app('request')));
        }
        
        $request = self::$registry->get('gtd_request');
        
        if ($key !== null) {
            return $request->get($key);
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
    public static function redirect($link, $status = 302) {
        return self::$registry->get('response')->redirect($link, $status);
    }
    
    /**
     * Рендерит и возвращает HTML-шаблон с данными
     *
     * @param string $route Путь к шаблону
     * @param array $data Данные для передачи в шаблон
     * @return string Отрендеренный HTML-код
     */
    public static function view($route, $data = []) {
        return self::$registry->get('load')->view($route, $data);
    }
    
    /**
     * Получает переведенную строку из языкового файла
     *
     * @param string $key Ключ для получения конкретного перевода
     * @param string|null $file Языковой файл
     * @return string Переведенная строка
     */
    public static function __($key = null, $file = null) {
        if ($file !== null) {
            self::$registry->get('load')->language($file, $key);
            return '';
        }
        if ($key === null) {
            return self::$registry->get('language')->all();
        }
        
        return self::$registry->get('language')->get($key);
    }
    
    /**
     * Проверяет, находится ли пользователь в админке
     *
     * @return bool True если в админке, False если в каталоге
     */
    public static function isAdmin() {
        if (defined('DIR_CATALOG')) {
            return true;
        }
        
        try {
            if (self::$registry->has('user') && self::$registry->get('user')) {
                return true;
            }
        } catch (\Exception $e) {
            // Игнорируем ошибку и продолжаем проверку
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
    public static function route($route, $args = '', $secure = true) {
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
                $session = self::$registry->get('session');
                $user_token = isset($session->data['user_token']) ? $session->data['user_token'] : '';
                
                if ($user_token && strpos($args, 'user_token=') === false) {
                    $prefix = (strlen($args) > 0) ? '&' : '';
                    $args = 'user_token=' . $user_token . $prefix . $args;
                }
            } catch (\Exception $e) {
                // Игнорируем ошибку получения токена
            }
        }
        
        return self::$registry->get('url')->link($route, $args, $secure);
    }
    
    /**
     * Получает или устанавливает значение в сессии
     *
     * @param string|null $key Ключ сессии
     * @param mixed|null $value Значение для установки
     * @return mixed Значение из сессии или объект сессии
     */
    public static function session($key = null, $value = null) {
        $session = self::$registry->get('session');
        
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
     * Получает объект базы данных
     *
     * @return object Объект базы данных
     */
    public static function db() {
        return self::$registry->get('db');
    }
    
    /**
     * Получает или устанавливает значение в кеше
     *
     * @param string $key Ключ кеша
     * @param mixed|null $value Значение для установки
     * @param int $expire Время жизни кеша в секундах
     * @return mixed Значение из кеша или объект кеша
     */
    public static function cache($key = null, $value = null, $expire = 3600) {
        $cache = self::$registry->get('cache');
        
        if ($key === null) {
            return $cache;
        }
        
        if ($value !== null) {
            $cache->set($key, $value, $expire);
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
    public static function logWrite($message, $filename = 'error.log') {
        try {
            $log = self::$registry->get('log');
            $log->write($message);
        } catch (\Exception $e) {
            // Фолбек на стандартное логирование PHP
            if (defined('DIR_LOGS')) {
                $logPath = constant('DIR_LOGS') . $filename;
            } else {
                $logPath = '/tmp/' . $filename;
            }
            error_log('[' . date('Y-m-d H:i:s') . '] ' . $message, 3, $logPath);
        }
    }
    
    /**
     * Получает объект загрузчика
     *
     * @return object Объект загрузчика
     */
    public static function load() {
        return self::$registry->get('load');
    }
    
    /**
     * Отправляет JSON-ответ
     *
     * @param mixed $data Данные для отправки
     * @param int $status HTTP-статус
     * @return void
     */
    public static function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Устанавливает или получает flash-сообщение
     *
     * @param string|null $key Ключ сообщения
     * @param string|null $message Текст сообщения
     * @return mixed Flash-сообщение или null
     */
    public static function flash($key = null, $message = null) {
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
     * Получение произвольного компонента из реестра
     *
     * @param string $key Ключ компонента
     * @return mixed
     */
    public static function get($key) {
        return self::$registry->get($key);
    }
    
    /**
     * Проверка существования компонента в реестре
     *
     * @param string $key Ключ компонента
     * @return bool
     */
    public static function has($key) {
        return self::$registry->has($key);
    }
    
    /**
     * Добавление компонента в реестр
     *
     * @param string $key Ключ компонента
     * @param mixed $value Значение
     */
    public static function set($key, $value) {
        self::$registry->set($key, $value);
    }
}