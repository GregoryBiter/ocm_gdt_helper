<?php

namespace GbitStudio\Gdt;

use ArrayAccess;
use Closure;
use Exception;

/**
 * Класс Container - Laravel-подобный сервис-контейнер
 */
class Container implements ArrayAccess
{
    /** @var object */
    protected $registry;

    /** @var array Привязки (bindings) */
    protected $bindings = [];

    /** @var array Общие экземпляры (singletons) */
    protected $instances = [];

    /**
     * @param object $registry Реестр OpenCart
     */
    public function __construct($registry)
    {
        $this->registry = $registry;

        // Регистрация базовых компонентов
        $this->registerBaseBindings();
    }

    /**
     * Регистрация стандартных компонентов фреймворка
     */
    protected function registerBaseBindings()
    {
        $this->singleton('config', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Config')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/config.php');
            }
            return new Config($registry);
        });

        $this->singleton('setting', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Setting')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/setting.php');
            }
            return new Setting($registry);
        });

        $this->singleton('db', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\DB')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/db.php');
            }
            return new DB($registry);
        });

        $this->singleton('session', function ($container, $registry) {
            if (!class_exists('\\GbitStudio\\Gdt\\Session')) {
                require_once(DIR_SYSTEM . 'library/gbitstudio/gdt/session.php');
            }
            return new Session($registry);
        });
    }

    /**
     * Регистрирует привязку в контейнере
     *
     * @param string $abstract
     * @param mixed $concrete
     * @param bool $shared
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * Регистрирует синглтон
     *
     * @param string $abstract
     * @param mixed $concrete
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Регистрирует готовый экземпляр
     *
     * @param string $abstract
     * @param mixed $instance
     */
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Извлекает экземпляр из контейнера
     *
     * @param string $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        // Если это синглтон и он уже создан
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Если привязки нет, пробуем авто-разрешение (если это имя класса)
        if (!isset($this->bindings[$abstract])) {
            if (class_exists($abstract)) {
                return new $abstract($this->registry);
            }
            return null;
        }

        $concrete = $this->bindings[$abstract]['concrete'];
        $shared = $this->bindings[$abstract]['shared'];

        // Создаем объект
        if ($concrete instanceof Closure) {
            $object = $concrete($this, $this->registry);
        } else {
            if (is_string($concrete) && class_exists($concrete)) {
                $object = new $concrete($this->registry);
            } else {
                $object = $concrete;
            }
        }

        // Сохраняем, если это синглтон
        if ($shared) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Алиас для make() (совместимость)
     */
    public function get($abstract)
    {
        return $this->make($abstract);
    }

    /**
     * Проверяет наличие привязки или экземпляра
     */
    public function has($abstract)
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Алиас для has() (совместимость)
     */
    public function bound($abstract)
    {
        return $this->has($abstract);
    }

    /**
     * Алиас для instance() (совместимость)
     */
    public function set($abstract, $instance)
    {
        $this->instance($abstract, $instance);
    }

    /**
     * Магический доступ к сервисам
     */
    public function __get($name)
    {
        return $this->make($name);
    }

    // --- Реализация ArrayAccess ---

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->make($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->bind($offset, $value instanceof Closure ? $value : function () use ($value) {
            return $value;
        });
    }

    public function offsetUnset($offset): void
    {
        unset($this->bindings[$offset], $this->instances[$offset]);
    }
}
