<?php

namespace GbitStudio\Gdt;

/**
 * Класс Setting - управление настройками OpenCart через стандартную модель setting/setting
 */
class Setting
{
    /** @var object */
    private $registry;

    /**
     * @param object $registry Реестр OpenCart
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Получает настройки из базы данных через модель setting/setting
     *
     * @param string $code Код группы настроек
     * @param string|null $key Конкретный ключ
     * @param mixed $default Значение по умолчанию
     * @param int $store_id ID магазина
     * @return mixed
     */
    public function get($code, $key = null, $default = null, $store_id = 0)
    {
        $this->registry->get('load')->model('setting/setting');
        $model = $this->registry->get('model_setting_setting');

        if ($model) {
            $data = $model->getSetting($code, (int) $store_id);

            if ($key === null) {
                return $data;
            }

            return isset($data[$key]) ? $data[$key] : $default;
        }

        return $default;
    }

    /**
     * Сохраняет настройки в базу данных (только в админке)
     *
     * @param string $code
     * @param array $data
     * @param int $store_id
     * @return bool
     */
    public function set($code, array $data, $store_id = 0)
    {
        // В каталоге модель setting/setting не имеет методов записи и они там не нужны
        if (!App::isAdmin()) {
            return false;
        }

        $this->registry->get('load')->model('setting/setting');
        $model = $this->registry->get('model_setting_setting');

        if ($model && method_exists($model, 'editSetting')) {
            $model->editSetting($code, $data, (int) $store_id);
            return true;
        }

        return false;
    }

    /**
     * Удаляет настройки из базы данных (только в админке)
     *
     * @param string $code
     * @param int $store_id
     * @return bool
     */
    public function delete($code, $store_id = 0)
    {
        if (!App::isAdmin()) {
            return false;
        }

        $this->registry->get('load')->model('setting/setting');
        $model = $this->registry->get('model_setting_setting');

        if ($model && method_exists($model, 'deleteSetting')) {
            $model->deleteSetting($code, (int) $store_id);
            return true;
        }

        return false;
    }
}
