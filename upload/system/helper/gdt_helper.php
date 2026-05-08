<?php

use GbitStudio\Gdt\App;

// Global helper function
if (!function_exists('registry')) {
    /**
     * Получает экземпляр реестра OpenCart или значение из него
     * Теперь использует класс GDT
     *
     * @param string|null $key Ключ для получения значения из реестра
     * @return mixed Реестр OpenCart или значение из реестра по указанному ключу
     * @throws Exception Если запрошенный ключ не найден
     */
    function registry($key = null)
    {
        return App::registry($key);
    }
}

if (!function_exists('app')) {
    /**
     * Получает экземпляр приложения GDT или сервис из него
     * Теперь использует класс GDT
     *
     * @param string|null $key Ключ для получения сервиса из приложения
     * @return mixed Экземпляр приложения или запрошенный сервис
     * @throws Exception Если запрошенный сервис не найден
     */
    function app($key = null)
    {
        return App::app($key);
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null, $data = null)
    {
        $config = App::make('config');

        if ($data !== null && $key !== null) {
            $config->set($key, $data);
            return $config->get($key);
        }

        if ($key === null) {
            return $config;
        }

        return $config->get($key, $default);
    }
}

if (!function_exists('setting')) {
    function setting($code = null, $key = null, $default = null)
    {
        $setting = App::make('setting');

        if ($code === null) {
            return $setting;
        }

        return $setting->get($code, $key, $default);
    }
}

if (!function_exists('response')) {
    function response($content = null)
    {
        $response = App::get('response');

        if ($content !== null) {
            $response->setOutput($content);
        }

        return $response;
    }
}

if (!function_exists('request')) {
    /**
     * Получает объект запроса из приложения
     * Теперь использует класс GDT
     * 
     * @param string|null $key Ключ для получения значения из запроса (если требуется)
     * @return mixed Объект запроса из приложения
     * @throws Exception Если запрошенный ключ не найден
     */
    function request($key = null)
    {
        $request = App::get('request');

        if ($key !== null) {
            if (isset($request->get[$key])) {
                return $request->get[$key];
            }
            if (isset($request->post[$key])) {
                return $request->post[$key];
            }
            return null;
        }

        return $request;
    }
}

if (!function_exists('redirect')) {
    /**
     * Перенаправляет пользователя на указанный URL
     * Теперь использует класс GDT
     *
     * @param string $link URL для перенаправления
     * @param int $status HTTP-статус перенаправления (по умолчанию 302)
     * @return void
     */
    function redirect($link, $status = 302)
    {
        return App::get('response')->redirect($link, $status);
    }
}

if (!function_exists('view')) {
    /**
     * Рендерит и возвращает HTML-шаблон с данными
     * Теперь использует класс GDT
     *
     * @param string $route Путь к шаблону
     * @param array $data Данные для передачи в шаблон
     * @return string Отрендеренный HTML-код
     * @throws Exception Если шаблон не найден или путь пустой
     */
    function view($route, $data = [])
    {
        return App::get('load')->view($route, $data);
    }
}

if (!function_exists('__')) {
    /**
     * Получает переведенную строку из языкового файла
     * Теперь использует класс GDT
     *
     * @param string $key Ключ для получения конкретного перевода
     * @param string|null $file Языковой файл (опционально)
     * @param mixed ...$args Аргументы для sprintf
     * @return string|array Переведенная строка
     */
    function __($key = null, $file = null, ...$args)
    {
        if ($key === null && $file === null) {
            return App::get('language')->all();
        }

        // Авто-определение файла из ключа (например, 'common/header.text_home')
        if ($file === null && $key !== null && strpos($key, '.') !== false) {
            $last_dot = strrpos($key, '.');
            $file = substr($key, 0, $last_dot);
            $key = substr($key, $last_dot + 1);
        }

        if ($file !== null) {
            $data = App::get('load')->language($file);

            if ($key === null || $key === '') {
                return $data;
            }

            $text = isset($data[$key]) ? $data[$key] : $key;
        } else {
            $text = App::get('language')->get($key);
        }

        if (!empty($args) && is_string($text)) {
            return vsprintf($text, $args);
        }

        return $text;
    }
}

if (!function_exists('is_admin')) {
    /**
     * Проверяет, находится ли пользователь в админке
     * Теперь использует класс GDT
     *
     * @return bool True если в админке, False если в каталоге
     */
    function is_admin()
    {
        if (defined('DIR_CATALOG')) {
            return true;
        }

        try {
            if (App::has('user') && App::get('user')->isLogged()) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}

if (!function_exists('route')) {
    function route($route, $args = '', $secure = true)
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

        if (is_admin()) {
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

        return App::get('url')->link($route, $args, $secure);
    }
}

if (!function_exists('session')) {
    function session($key = null, $value = null)
    {
        $session = App::make('session');

        if ($key === null) {
            return $session;
        }

        if ($value !== null) {
            return $session->set($key, $value);
        }

        return $session->get($key);
    }
}

if (!function_exists('db')) {
    function db($table = null)
    {
        $db = App::make('db');
        if ($table !== null) {
            return $db->table($table);
        }
        return $db;
    }
}

if (!function_exists('db_query')) {
    function db_query($sql)
    {
        $query = App::make('db')->query($sql);
        return isset($query->rows) ? $query->rows : [];
    }
}

if (!function_exists('db_row')) {
    function db_row($sql)
    {
        $query = App::make('db')->query($sql);
        return isset($query->row) ? $query->row : null;
    }
}

if (!function_exists('cache')) {
    function cache($key = null, $value = null, $expire = 3600)
    {
        $cache = App::get('cache');

        if ($key === null) {
            return $cache;
        }

        if ($value !== null) {
            $cache->set($key, $value);
            return $value;
        }

        return $cache->get($key);
    }
}

if (!function_exists('log_write')) {
    function log_write($message, $filename = 'error.log')
    {
        try {
            App::get('log')->write($message);
        } catch (\Exception $e) {
            if (defined('DIR_LOGS')) {
                $logPath = constant('DIR_LOGS') . $filename;
            } else {
                $logPath = '/tmp/' . $filename;
            }
            error_log('[' . date('Y-m-d H:i:s') . '] ' . $message, 3, $logPath);
        }
    }
}

if (!function_exists('url')) {
    function url()
    {
        return App::get('url');
    }
}

if (!function_exists('load')) {
    function load()
    {
        return App::get('load');
    }
}

if (!function_exists('json_response')) {
    function json_response($data, $status = 200)
    {
        $response = App::get('response');
        $response->addHeader('Content-Type: application/json');

        if ($status !== 200) {
            $response->addHeader('HTTP/1.1 ' . $status);
        }

        $response->setOutput(json_encode($data));
    }
}

if (!function_exists('flash')) {
    function flash($key = null, $message = null)
    {
        return App::make('session')->flash($key, $message);
    }
}

if (!function_exists('flash_success')) {
    function flash_success($message)
    {
        return flash('success', $message);
    }
}

if (!function_exists('flash_error')) {
    function flash_error($message)
    {
        return flash('error', $message);
    }
}

if (!function_exists('user')) {
    function user()
    {
        if (is_admin()) {
            return App::has('user') ? App::get('user') : null;
        } else {
            return App::has('customer') ? App::get('customer') : null;
        }
    }
}

if (!function_exists('gdt_check_function_conflict')) {
    function gdt_check_function_conflict($function_name, $source = 'GDT')
    {
        if (function_exists($function_name)) {
            try {
                $reflection = new ReflectionFunction($function_name);
                $file = $reflection->getFileName();
                $line = $reflection->getStartLine();

                error_log("[GDT] Function conflict detected: {$function_name} already exists in {$file}:{$line}");
                return true;
            } catch (Exception $e) {
                error_log("[GDT] Could not analyze function conflict for: {$function_name}");
                return true;
            }
        }
        return false;
    }
}
