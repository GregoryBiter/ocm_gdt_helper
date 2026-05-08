# OpenCart GDT Helper (v1.0.0)

Современная надстройка над OpenCart 3.x/4.x, привносящая архитектурные паттерны Laravel (Dependency Injection Container, Query Builder, Fluent Response/Request) в экосистему OpenCart.

## 🚀 Основные возможности
- **Service Container**: Полноценный DI-контейнер с поддержкой `singleton` и `bind`.
- **Query Builder**: Удобный построитель SQL-запросов с автоматическим экранированием.
- **Fluent Response**: Laravel-style управление ответами (JSON, статусы, заголовки).
- **Request Service**: Удобная работа с GET/POST данными.
- **Global Helpers**: Набор глобальных функций для быстрого доступа к системе.

---

## 📂 Структура системы
Файлы ядра располагаются в `system/library/gbitstudio/gdt/`:
- `app.php` — Точка входа и бутстраппер сервисов.
- `container.php` — Ядро DI-контейнера.
- `db.php` — Менеджер базы данных.
- `querybuilder.php` — Построитель SQL-запросов.
- `session.php` — Сервис сессий и Flash-сообщений.
- `response.php` — Сервис исходящих ответов.
- `request.php` — Сервис входящих запросов.
- `url.php` — Улучшенная работа с URL и токенами.
- `language.php` — Продвинутые переводы (dot-syntax).
- `cache.php` — Обертка над кешем.

Глобальные хелперы: `system/helper/gdt_helper.php`.

---

## 🛠️ Использование

### 1. Доступ к сервисам (Контейнер)
Класс `App` теперь является чистым сервис-локатором. Основной способ работы — через глобальные хелперы или `App::make()`.

```php
use GbitStudio\Gdt\App;

// Получение через контейнер
$db = App::make('db');
$session = App::make('session');

// Доступ к оригинальному Registry OpenCart
$cart = App::get('cart');
$load = App::get('load');
```

### 2. Работа с базой данных (Query Builder)
Хелпер `db('table')` всегда возвращает **новый** экземпляр построителя, что исключает смешивание данных.

```php
// Выборка
$products = db('product')
    ->select(['product_id', 'model'])
    ->where('status', 1)
    ->where('price', '>', 100)
    ->orderBy('date_added', 'desc')
    ->limit(10)
    ->get(); // Array of rows

// Обновление
db('customer')->where('customer_id', 5)->update(['firstname' => 'Dmitry']);

// Удаление
db('cart')->where('customer_id', 5)->delete();

// Сложные запросы
$data = db('product p')
    ->join('product_description pd', 'p.product_id', '=', 'pd.product_id')
    ->where('pd.language_id', 1)
    ->first();
```

### 3. HTTP Ответы (Response)
Fluent-интерфейс для управления ответом:

```php
// JSON ответ с кодом 201
return json_response(['status' => 'success'], 201);

// Или расширенно через хелпер response()
return response('Success', 200)
    ->header('X-Custom-Header: value')
    ->header('Content-Type: text/plain');

// Редирект
return response()->redirect(route('common/home'));
```

### 4. Запросы (Request)
```php
// Получение значения (сначала GET, потом POST)
$id = request('id');

// Конкретные методы
$token = request()->query('user_token');
$name = request()->post('firstname');

// Нативный объект OpenCart
$files = request()->files;
```

### 5. Сессии и Flash-сообщения
```php
// Работа с данными
session('my_key', 'value');
$val = session('my_key');

// Flash-сообщения (удаляются после первого чтения)
flash('success', 'Товар добавлен!');
flash_error('Ошибка валидации');

// В шаблоне:
echo flash('success');
```

### 6. Переводы и URL
```php
// Умный перевод (автоматическая загрузка файлов через точку)
echo __('account/login.text_forgotten'); 

// С подстановкой переменных (sprintf)
echo __('text_welcome', null, $username);

// Умная генерация ссылок (авто-подстановка user_token в админке)
$url = route('catalog/product', ['product_id' => 10]);
```

---

## 📖 Примеры использования в проекте

Полные примеры реализации вы можете найти в папке `example/`:
- **Контроллер**: [controller.php](file:///home/gregorybiter/%D0%A0%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D1%81%D1%82%D0%BE%D0%BB/ocm_gdt_helper/example/controller.php) — обработка запросов, работа с БД, сессиями и ответами.
- **Модель**: [model.php](file:///home/gregorybiter/%D0%A0%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D1%81%D1%82%D0%BE%D0%BB/ocm_gdt_helper/example/model.php) — сложные выборки через Query Builder и использование кеша.
- **Шаблон (Twig)**: [template.twig](file:///home/gregorybiter/%D0%A0%D0%B0%D0%B1%D0%BE%D1%87%D0%B8%D0%B9%20%D1%81%D1%82%D0%BE%D0%BB/ocm_gdt_helper/example/template.twig) — использование хелперов прямо в представлении.

---

## 🔧 Кастомизация (DI)
Вы можете регистрировать свои сервисы в контейнере:

```php
App::singleton('my_service', function() {
    return new \MyNamespace\MyService();
});

// Использование
$service = App::make('my_service');
```

---

## 📦 Установка
1. Скопируйте содержимое папки `upload` в корень вашего сайта.
2. Фреймворк инициализируется автоматически через модификатор или в файле `startup.php`.
