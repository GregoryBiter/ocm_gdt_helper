<?php

namespace GbitStudio\Gdt;

/**
 * Класс App - провайдер основных экземпляров OpenCart
 * Предоставляет статический доступ ко всем компонентам системы через контейнер
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

        self::registerBaseBindings();
    }

    /**
     * Регистрация стандартных компонентов фреймворка
     */
    protected static function registerBaseBindings()
    {
        self::singleton('config', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Config')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/config.php');
            }
            return new Config($registry);
        });

        self::singleton('setting', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Setting')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/setting.php');
            }
            return new Setting($registry);
        });

        self::singleton('db', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\DB')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/db.php');
            }
            return new DB($registry);
        });

        self::singleton('session', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Session')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/session.php');
            }
            return new Session($registry);
        });

        self::singleton('response', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Response')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/response.php');
            }
            return new Response($registry);
        });

        self::singleton('request', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Request')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/request.php');
            }
            return new Request($registry);
        });

        self::singleton('url', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Url')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/url.php');
            }
            return new Url($registry);
        });

        self::singleton('language', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Language')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/language.php');
            }
            return new Language($registry);
        });

        self::singleton('cache', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Cache')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/cache.php');
            }
            return new Cache($registry);
        });
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
            return self::make($key);
        }
        return self::container();
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
     * Получение произвольного компонента (Registry)
     *
     * @param string $key Ключ компонента
     * @return mixed
     */
    public static function get($key)
    {
        return self::$registry->get($key);
    }

    /**
     * Проверка существования компонента в реестре
     *
     * @param string $key Ключ компонента
     * @return bool
     */
    public static function has($key)
    {
        return self::$registry->has($key);
    }

    /**
     * Добавление компонента в реестр
     *
     * @param string $key Ключ компонента
     * @param mixed $value Значение
     */
    public static function set($key, $value)
    {
        self::$registry->set($key, $value);
    }
}