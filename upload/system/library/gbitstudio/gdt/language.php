<?php

namespace GbitStudio\Gdt;

/**
 * Класс Language - сервис для работы с переводами OpenCart
 */
class Language
{
    /** @var object */
    protected $registry;

    /** @var object */
    protected $language;

    /**
     * @param object $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->language = $registry->get('language');
    }

    /**
     * Универсальный метод для получения переводов OpenCart.
     * 
     * @param string|null $key  Ключ перевода ИЛИ 'путь/к/файлу.ключ'
     * @param string|null $file Путь к файлу перевода
     * @param mixed       ...$args Дополнительные аргументы для замены плейсхолдеров (%s, %d) в строке
     * 
     * @return string|array|null
     */
    public function translate($key = null, $file = null, ...$args)
    {
        if ($key === null && $file === null) {
            return $this->language->all();
        }

        // Авто-определение файла из ключа (например, 'common/header.text_home')
        if ($file === null && $key !== null && strpos($key, '.') !== false) {
            $last_dot = strrpos($key, '.');
            $file = substr($key, 0, $last_dot);
            $key = substr($key, $last_dot + 1);
        }

        if ($file !== null) {
            $data = $this->registry->get('load')->language($file);

            if ($key === null || $key === '') {
                return $data;
            }

            $text = isset($data[$key]) ? $data[$key] : $key;
        } else {
            $text = $this->language->get($key);
        }

        if (!empty($args) && is_string($text)) {
            return vsprintf($text, $args);
        }

        return $text;
    }

    /**
     * Возвращает нативный объект языка
     */
    public function native()
    {
        return $this->language;
    }
}
