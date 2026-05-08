<?php
/**
 * Пример использования GDT Helper в контроллере OpenCart
 */
class ControllerExtensionModuleGdtExample extends Controller
{
    public function index()
    {
        // 1. Использование глобальных хелперов

        // Работа с конфигом (Runtime)
        config('gdt_test_mode', null, true);
        $is_test = config('gdt_test_mode');

        // Работа с конфигом через класс
        $site_name = \GbitStudio\Gdt\App::config()->get('config_name');

        // Использование нового класса Setting через App::setting()
        // Работа с БД настройками (таблица setting)
        $module_settings = \GbitStudio\Gdt\App::setting()->get('module_gdt_example');
        $status = \GbitStudio\Gdt\App::setting()->get('module_gdt_example', 'status', 0);

        // Работа с переводами (новые функции)
        // Автоматическая загрузка файла и получение ключа
        $title = __('common/header.text_home');

        // Перевод с форматированием (sprintf)
        // Допустим в языке: 'У вас %s товаров на сумму %s'
        $cart_text = __('checkout/cart.text_items', null, 5, '100$');

        // 2. Использование статического класса GDT напрямую
        \GbitStudio\Gdt\App::logWrite('Пример записи в лог через GDT');

        // Получение данных из сессии
        $token = \GbitStudio\Gdt\App::session('user_token');

        // 3. Работа с базой данных через Query Builder (Laravel-style)
        $latest_products = db('product')
            ->select(['product_id', 'model', 'price'])
            ->where('status', 1)
            ->where('quantity', '>', 0)
            ->orderBy('date_added', 'DESC')
            ->limit(5)
            ->get();

        // 4. Формирование JSON-ответа
        if (request('ajax')) {
            return json_response([
                'success' => true,
                'message' => __('account/login.text_success'),
                'data' => $this->load->view('extension/module/gdt_example', ['title' => $title])
            ]);
        }

        // 4. Работа с кешем
        $data = cache('my_custom_data');
        if (!$data) {
            $data = ['some' => 'data'];
            cache('my_custom_data', $data);
        }

        return view('extension/module/gdt_example', ['title' => $title]);
    }
}