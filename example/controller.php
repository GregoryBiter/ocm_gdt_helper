<?php

class ControllerExample extends Controller
{
    public function index()
    {
        // 1. Работа с входящими данными (Request Service)
        $productId = request('product_id'); // GET/POST
        $filter = request()->query('filter', 'default');
        $data = request()->post('data');

        // 2. Работа с базой данных (Query Builder)
        // Получаем товар и связанные данные одной цепочкой
        $product = db('product p')
            ->select(['p.*', 'pd.name'])
            ->join('product_description pd', 'p.product_id', '=', 'pd.product_id')
            ->where('p.product_id', $productId)
            ->where('pd.language_id', (int) config('config_language_id'))
            ->first();

        if (!$product) {
            flash_error(__('error_not_found'));
            return response()->redirect(route('common/home'));
        }

        // 3. Работа с настройками (Config & Setting)
        $limit = config('config_limit_admin', 20);
        $myModuleSettings = setting('module_my_module'); // Получить весь массив настроек

        // 4. Логика перевода (Language Service)
        $this->document->setTitle(__('catalog/product.text_title', null, $product['name']));

        // 5. Работа с сессией и Flash-сообщениями
        session('last_viewed_product', $productId);
        flash('success', __('text_success_view'));

        // 6. Подготовка данных для View
        $data['product'] = $product;
        $data['back_link'] = route('common/home');
        $data['action'] = route('checkout/cart/add');

        // 7. Рендеринг через хелпер
        return response(view('example/template', $data));
    }

    public function api()
    {
        // Пример JSON ответа
        $results = db('product')
            ->where('status', 1)
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ], 201);
    }
}