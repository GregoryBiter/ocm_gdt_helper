<?php

class ModelExample extends Model
{
    /**
     * Пример использования Query Builder и Cache в модели
     */
    public function getCategories()
    {
        $cacheKey = 'gdt.categories.all.' . (int)config('config_language_id');
        
        // Пытаемся взять из кеша через наш сервис
        $categories = cache($cacheKey);

        if ($categories === null) {
            // Если нет в кеше, строим сложный запрос
            $categories = db('category c')
                ->select(['c.category_id', 'cd.name', 'c.image'])
                ->join('category_description cd', 'c.category_id', '=', 'cd.category_id')
                ->where('cd.language_id', (int)config('config_language_id'))
                ->where('c.status', 1)
                ->orderBy('c.sort_order', 'ASC')
                ->get();

            // Сохраняем в кеш на 1 час
            cache($cacheKey, $categories, 3600);
        }

        return $categories;
    }

    /**
     * Пример записи данных
     */
    public function updateProductStock($productId, $quantity)
    {
        return db('product')
            ->where('product_id', (int)$productId)
            ->update([
                'quantity'   => (int)$quantity,
                'date_modified' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Пример удаления
     */
    public function removeOldCartItems($days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        return db('cart')
            ->where('date_added', '<', $date)
            ->delete();
    }
}
