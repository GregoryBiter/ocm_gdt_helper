<?php
/**
 * Пример использования GDT Helper в контроллере OpenCart
 */
class ControllerExtensionModuleGdtExample extends Controller
{
    public function index()
    {
        // 1. Использование глобальных хелперов

        // Работа с конфигом
        config('gdt_test_mode', true);
        $is_test = config('gdt_test_mode');

        // Работа с переводами (новые функции)
        // Автоматическая загрузка файла и получение ключа
        $title = __('common/header.text_home');

        // Перевод с форматированием (sprintf)
        // Допустим в языке: 'У вас %s товаров на сумму %s'
        $cart_text = __('checkout/cart.text_items', null, 5, '100$');

        // 2. Использование статического класса GDT напрямую
        \GbitStudio\GDT::logWrite('Пример записи в лог через GDT');

        // Получение данных из сессии
        $token = \GbitStudio\GDT::session('user_token');

        // 3. Формирование JSON-ответа
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