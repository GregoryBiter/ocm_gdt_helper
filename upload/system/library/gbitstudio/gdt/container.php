<?php

namespace GbitStudio\Gdt;

/**
 * Класс Container - простое управление зависимостями и ленивая загрузка компонентов
 */
class Container
{
    /** @var object */
    private $registry;

    /** @var array Хранилище инстансов */
    private $instances = [];

    /** @var array Определения компонентов */
    private $definitions = [
        'config' => [
            'class' => '\\GbitStudio\\Gdt\\Config',
            'file'  => 'gbitstudio/gdt/config.php'
        ],
        'setting' => [
            'class' => '\\GbitStudio\\Gdt\\Setting',
            'file'  => 'gbitstudio/gdt/setting.php'
        ],
        'db' => [
            'class' => '\\GbitStudio\\Gdt\\DB',
            'file'  => 'gbitstudio/gdt/db.php'
        ]
    ];

    /**
     * @param object $registry Реестр OpenCart
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Получает экземпляр компонента по имени
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (isset($this->definitions[$name])) {
            $definition = $this->definitions[$name];

            if (!class_exists($definition['class'])) {
                $file = DIR_SYSTEM . 'library/' . $definition['file'];
                if (file_exists($file)) {
                    require_once($file);
                }
            }

            if (class_exists($definition['class'])) {
                $class = $definition['class'];
                $this->instances[$name] = new $class($this->registry);
                return $this->instances[$name];
            }
        }

        // Если компонента нет в определениях, пробуем получить из реестра OpenCart
        return $this->registry->get($name);
    }

    /**
     * Проверяет наличие компонента
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->instances[$name]) || isset($this->definitions[$name]) || $this->registry->has($name);
    }

    /**
     * Регистрирует готовый инстанс в контейнере
     *
     * @param string $name
     * @param mixed $instance
     */
    public function set($name, $instance)
    {
        $this->instances[$name] = $instance;
    }
}
