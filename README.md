# App Framework Helper for OpenCart

## Описание
Модуль **App Helper** — это современная надстройка над OpenCart 3.x, которая превращает классическую разработку в комфортный процесс с использованием Laravel-подобного синтаксиса. Модуль предоставляет централизованный сервис-контейнер, мощный Query Builder и набор глобальных помощников (helpers) для быстрого доступа к ядру OpenCart.

---

## 🚀 Основные возможности
- **Service Container (DI)** — ленивая загрузка и централизованное управление компонентами.
- **Fluent Query Builder** — работа с БД в стиле Laravel (`db('table')->where(...)->get()`).
- **Глобальные Helpers** — функции `app()`, `config()`, `setting()`, `db()`, `__()` и др.
- **Умные переводы** — поддержка dot-синтаксиса и автоматическая загрузка языковых файлов.
- **Modern Architecture** — использование пространств имен `GbitStudio\Gdt`.
- **Twig Integration** — доступ ко всем хелперам прямо из шаблонов.

---

## 📦 Установка
1. Скопируйте содержимое папки `upload/` в корень вашего сайта.
2. Установите модификатор `system/gdt_helper_v1.ocmod.xml` через панель OpenCart.
3. Обновите кэш модификаторов.

### Структура файлов
```text
system/library/gbitstudio/gdt/
├── app.php        # Ядро фреймворка (Service Locator)
├── container.php  # Контейнер зависимостей (DI)
├── db.php         # Query Builder
├── config.php     # Работа с runtime-конфигом
└── setting.php    # Работа с настройками в БД
```

---

## 🛠️ Основные компоненты

### 1. Сервис-контейнер и класс `App`
Класс `App` является точкой входа. Он автоматически инициализируется при старте OpenCart.

```php
use GbitStudio\Gdt\App;

// Получение сервисов
$db = App::db();
$config = App::config();
$setting = App::setting();

// Прокси к Registry OpenCart
$cart = App::get('cart');
$customer = App::customer();
```

### 2. Работа с базой данных (Query Builder)
Вы можете использовать хелпер `db('table')` для быстрого построения запросов.

```php
// Выборка данных
$products = db('product')
    ->select(['product_id', 'model'])
    ->where('status', 1)
    ->where('price', '>', 100)
    ->orderBy('date_added', 'desc')
    ->limit(10)
    ->get(); // возвращает массив

// Одна строка
$category = db('category')->where('category_id', 15)->first(); // или ->row()

// CRUD операции
$id = db('customer')->insert(['firstname' => 'Ivan', 'email' => 'test@test.com']);
db('customer')->where('customer_id', $id)->update(['firstname' => 'Dmitry']);
db('customer')->where('customer_id', $id)->delete();
```

### 3. Быстрые SQL-запросы (Raw SQL)
Если нужен обычный SQL, используйте `db_query` и `db_row`.

```php
$rows = db_query("SELECT * FROM " . DB_PREFIX . "product WHERE status = 1");
$row = db_row("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = 1");
```

### 4. Конфигурация и Настройки
Мы разделяем **Config** (runtime настройки OC) и **Setting** (настройки из таблицы `setting`).

```php
// Runtime Config
$name = config('config_name');
config('my_runtime_key', null, 'value'); // Установка

// Persistent Settings (DB)
$val = setting('module_my', 'status', 'default');
setting()->set('module_my', ['status' => 1]); // Только в админке
```

### 5. Переводы и Языки `__()`
Хелпер `__()` автоматически загружает файлы, если это необходимо.

```php
// Обычный перевод
echo __('text_home');

// С автозагрузкой файла (синтаксис 'файл.ключ')
echo __('common/header.text_home');

// С передачей аргументов (sprintf)
echo __('checkout/cart.text_items', null, 5, '100$');
```

---

## 🌐 Глобальные Хелперы

| Функция | Описание |
| :--- | :--- |
| `app()` | Доступ к сервис-контейнеру |
| `registry()` | Доступ к Registry OpenCart |
| `db($table)` | Инициализация Query Builder |
| `config()` | Работа с конфигурацией |
| `setting()` | Работа с настройками БД |
| `__()` | Перевод строк |
| `request()` | Доступ к GET/POST данным |
| `response()` | Управление выводом |
| `view()` | Рендеринг шаблона |
| `route()` | Генерация ссылок (с учетом user_token в админке) |
| `redirect()` | Перенаправление |
| `session()` | Работа с сессией |
| `cache()` | Работа с кэшем |
| `json_response()` | Отправка JSON ответа |

---

## 🎨 Использование в Twig
Фреймворк автоматически пробрасывает основные функции в шаблоны:

```twig
{# Перевод #}
{{ __('common/header.text_home') }}

{# Генерация ссылки #}
<a href="{{ route('account/login') }}">Login</a>

{# Вызов контроллера (если нужно) #}
{{ controller('common/column_left') }}
```

---

## 🔒 Безопасность
- Все данные в Query Builder автоматически экранируются.
- Методы `setting()->set()` и `setting()->delete()` доступны только в контексте администратора (проверка `isAdmin()`).

---

## 📝 Лицензия
Распространяется "как есть" под лицензией MIT. Разработано в **GbitStudio**.
