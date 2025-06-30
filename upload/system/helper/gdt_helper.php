<?php

use GbitStudio\GDT\Engine\GDT;
use GbitStudio\GDT\Engine\Hook;

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
        return GDT::registry($key);
    }
}
// Global helper function
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
        return GDT::app($key);
    }
}

// Global helper function response
if (!function_exists('config')) {
    /**
     * Получает или устанавливает значение конфигурации
     * Теперь использует класс GDT
     *
     * @param string|null $key Ключ конфигурации
     * @param mixed|null $data Данные для установки (если требуется)
     * @return mixed Значение конфигурации или все настройки
     * @throws Exception Если ключ не найден
     */
    function config($key = null, $data = null)
    {
        return GDT::config($key, $data);
    }
}

// Global helper function response
if (!function_exists('response')) {
    /**
     * Получает или настраивает объект ответа
     * Теперь использует класс GDT
     *
     * @param mixed|null $content Контент для установки в ответ
     * @return mixed Объект ответа приложения
     */
    function response($content = null)
    {
        return GDT::response($content);
    }
}

// Global helper function request
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
        return GDT::request($key);
    }
}


// Global helper function request
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
        return GDT::redirect($link, $status);
    }
}

// Global helper function request
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
        return GDT::view($route, $data);
    }
}


// Global helper function request
if (!function_exists('__')) {
    /**
     * Получает переведенную строку из языкового файла
     * Теперь использует класс GDT
     *
     * @param string $key Ключ для получения конкретного перевода
     * @param string|null $file Языковой файл (опционально)
     * @return string Переведенная строка
     */
    function __($key = null, $file = null)
    {
        return GDT::__($key, $file);
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
        return GDT::isAdmin();
    }
}

// Global helper function request
if (!function_exists('route')) {
    /**
     * Генерирует URL-путь для OpenCart с учетом текущего контекста (админка или каталог)
     * Теперь использует класс GDT
     *
     * @param string $route Маршрут в формате OpenCart (controller/method)
     * @param mixed $args Параметры URL (строка или массив)
     * @param bool $secure Использовать ли HTTPS
     * @return string Сформированный URL
     */
    function route($route, $args = '', $secure = true)
    {
        return GDT::route($route, $args, $secure);
    }
}

// Global helper function session
if (!function_exists('session')) {
    /**
     * Получает или устанавливает значение в сессии
     * Теперь использует класс GDT
     *
     * @param string|null $key Ключ сессии
     * @param mixed|null $value Значение для установки
     * @return mixed Значение из сессии или объект сессии
     */
    function session($key = null, $value = null)
    {
        return GDT::session($key, $value);
    }
}

// Global helper function db
if (!function_exists('db')) {
    /**
     * Получает объект базы данных
     * Теперь использует класс GDT
     *
     * @return object Объект базы данных
     */
    function db()
    {
        return GDT::db();
    }
}

// Global helper function cache
if (!function_exists('cache')) {
    /**
     * Получает или устанавливает значение в кеше
     * Теперь использует класс GDT
     *
     * @param string $key Ключ кеша
     * @param mixed|null $value Значение для установки
     * @param int $expire Время жизни кеша в секундах
     * @return mixed Значение из кеша или объект кеша
     */
    function cache($key = null, $value = null, $expire = 3600)
    {
        return GDT::cache($key, $value, $expire);
    }
}

// Global helper function log_write
if (!function_exists('log_write')) {
    /**
     * Записывает сообщение в лог
     * Теперь использует класс GDT
     *
     * @param string $message Сообщение для записи
     * @param string $filename Имя файла лога (по умолчанию error.log)
     * @return void
     */
    function log_write($message, $filename = 'error.log')
    {
        GDT::logWrite($message, $filename);
    }
}

// Global helper function url
if (!function_exists('url')) {
    /**
     * Получает объект URL
     * Теперь использует класс GDT
     *
     * @return object Объект URL
     */
    function url()
    {
        return GDT::url();
    }
}

// Global helper function load
if (!function_exists('load')) {
    /**
     * Получает объект загрузчика
     * Теперь использует класс GDT
     *
     * @return object Объект загрузчика
     */
    function load()
    {
        return GDT::load();
    }
}

// Global helper function json_response
if (!function_exists('json_response')) {
    /**
     * Отправляет JSON-ответ
     * Теперь использует класс GDT
     *
     * @param mixed $data Данные для отправки
     * @param int $status HTTP-статус
     * @return void
     */
    function json_response($data, $status = 200)
    {
        GDT::jsonResponse($data, $status);
    }
}

// Global helper function flash
if (!function_exists('flash')) {
    /**
     * Устанавливает или получает flash-сообщение
     * Теперь использует класс GDT
     *
     * @param string|null $key Ключ сообщения
     * @param string|null $message Текст сообщения
     * @return mixed Flash-сообщение или null
     */
    function flash($key = null, $message = null)
    {
        return GDT::flash($key, $message);
    }
}

if (!function_exists('do_action')) {
    /**
     * Выполняет действие с указанным файлом и ключом
     * Теперь использует класс GDT
     *
     * @param string $file Путь к файлу действия
     * @param mixed $key Ключ для передачи в действие
     * @return mixed Результат выполнения действия
     */
    function do_action($file, $key = [])
    {
        return Hook::do_action($file, $key);
    }
}

/**
 * Проверяет существование функции и логирует конфликты
 * 
 * @param string $function_name Имя функции
 * @param string $source Источник определения функции
 * @return bool
 */
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

// Можно использовать так:
// if (!gdt_check_function_conflict('view', 'GDT')) {
//     function view($route, $data = []) { ... }
// }

