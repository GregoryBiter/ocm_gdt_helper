# GDT Helper Module

## Описание

Модуль **GDT Helper** является базовым компонентом GDT Framework, который предоставляет глобальные вспомогательные функции и расширяет возможности OpenCart. Этот модуль обеспечивает Laravel-подобный синтаксис для работы с основными компонентами OpenCart через удобные helper-функции.

### Основные возможности:

- 🌐 **Глобальные helper-функции** - удобный доступ к компонентам OpenCart из любого места
- 🎯 **Laravel-подобный API** - знакомый синтаксис для разработчиков
- 🔧 **Расширение Twig** - дополнительные функции для шаблонов
- 📦 **Singleton Registry** - глобальный доступ к реестру OpenCart
- ⚡ **Автоматическая инициализация** - интеграция с системой загрузки OpenCart
- 🛠️ **OCMOD модификации** - безопасное изменение ядра OpenCart

## Технические требования

- PHP 7.4 или выше
- OpenCart 3.x/4.x
- Поддержка пространств имен (namespace)
- Система OCMOD включена

## Установка

1. Скопируйте содержимое папки `upload/` в корневую директорию вашего OpenCart
2. Установите OCMOD файл через админ-панель:
   - Перейдите: Extensions → Installer
   - Загрузите файл `gdt_helper_v1.ocmod.xml`
   - Перейдите: Extensions → Modifications
   - Нажмите кнопку "Refresh"

### Структура установки:
```
system/gdt_helper_v1.ocmod.xml          # OCMOD модификации
system/helper/gdt_helper.php            # Глобальные helper-функции
system/library/gbitstudio/gdt/gdt.php   # Основной класс GDT
system/library/gbitstudio/gdt/LICENSE   # Лицензия
```

## Глобальные Helper-функции

### registry() - Доступ к реестру OpenCart

```php
// Получение всего реестра
$registry = registry();

// Получение конкретного компонента
$db = registry('db');
$config = registry('config');
$session = registry('session');
$request = registry('request');
$response = registry('response');

// Использование в любом месте кода
function myCustomFunction() {
    $db = registry('db');
    $result = $db->query("SELECT * FROM " . DB_PREFIX . "product");
    return $result->rows;
}
```

### app() - Работа с приложением GDT

```php
// Получение экземпляра приложения
$app = app();

// Получение сервиса из приложения
$service = app('my_service');

// Использование в контроллерах
class ControllerCustomModule extends Controller {
    public function index() {
        $config = app('config');
        $data['site_name'] = $config->get('config_name');
    }
}
```

### config() - Работа с конфигурацией

```php
// Получение значения конфигурации
$siteName = config('config_name');
$dbPrefix = config('db_prefix');

// Получение с значением по умолчанию
$customSetting = config('custom_setting', 'default_value');

// Установка значения конфигурации
config('custom_key', 'custom_value');

// Получение всей конфигурации
$allConfig = config();
```

### response() - Управление ответами

```php
// Получение объекта ответа
$response = response();

// Установка контента ответа
response('Hello World');

// JSON ответ
response()->addHeader('Content-Type: application/json');
response(json_encode(['status' => 'success']));

// В AJAX контроллерах
public function ajaxAction() {
    $data = ['message' => 'Success', 'data' => $someData];
    response(json_encode($data));
}
```

### request() - Работа с запросами

```php
// Получение объекта запроса
$request = request();

// Получение POST данных
$postData = request()->post;
$name = request()->post['name'] ?? '';

// Получение GET параметров
$getId = request()->get['id'] ?? 0;

// Получение конкретного значения (если поддерживается)
$value = request('parameter_name');
```

### redirect() - Перенаправления

```php
// Простое перенаправление
redirect('/admin/catalog/product');

// Перенаправление с HTTP статусом
redirect('/admin/login', 301);

// Перенаправление на внешний URL
redirect('https://example.com');

// Использование в контроллерах
public function save() {
    // Сохранение данных...
    redirect($this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token']));
}
```

## Расширения для Twig шаблонов

Модуль добавляет дополнительные функции в Twig шаблоны:

### Функция __() - Локализация

```twig
{# В шаблонах Twig #}
{{ __('text_welcome') }}
{{ __('button_save') }}

{# С параметрами #}
{{ __('text_hello', {'name': 'John'}) }}
```

### Функция route() - Маршрутизация

```twig
{# Создание ссылок #}
<a href="{{ route('product/product', {'product_id': 123}) }}">Товар</a>
<a href="{{ route('account/login') }}">Вход</a>

{# В формах #}
<form action="{{ route('account/register') }}" method="post">
    <!-- Поля формы -->
</form>
```

### Функция do_action() - Хуки

```twig
{# Выполнение хуков в шаблонах #}
{{ do_action('header_content') }}
{{ do_action('product_additional_info', {'product_id': product.id}) }}

{# Условное выполнение #}
{% if do_action('check_permission', {'action': 'edit'}) %}
    <button>Редактировать</button>
{% endif %}
```

### Функция controller() - Загрузка контроллеров

```twig
{# Загрузка и выполнение контроллера #}
{{ controller('extension/module/featured') }}
{{ controller('common/header') }}

{# С параметрами #}
{{ controller('product/review', {'product_id': 123}) }}
```

## Практические примеры использования

### Создание пользовательской модели с helper-функциями

```php
<?php
class ModelCustomProduct extends Model {
    
    public function getProducts() {
        // Использование registry() для доступа к БД
        $db = registry('db');
        
        $query = $db->query("
            SELECT * FROM " . DB_PREFIX . "product 
            WHERE status = '1' 
            ORDER BY date_added DESC
        ");
        
        return $query->rows;
    }
    
    public function getProductById($productId) {
        $db = registry('db');
        $config = config(); // Получение конфигурации
        
        $query = $db->query("
            SELECT * FROM " . DB_PREFIX . "product 
            WHERE product_id = '" . (int)$productId . "'
        ");
        
        if ($query->num_rows) {
            $product = $query->row;
            
            // Добавление URL изображения
            if ($product['image']) {
                $product['image_url'] = config('config_url') . 'image/' . $product['image'];
            }
            
            return $product;
        }
        
        return null;
    }
}
```

### AJAX контроллер с helper-функциями

```php
<?php
class ControllerApiProduct extends Controller {
    
    public function search() {
        // Проверка метода запроса
        if (request()->server['REQUEST_METHOD'] !== 'POST') {
            response()->addHeader('HTTP/1.1 405 Method Not Allowed');
            response(json_encode(['error' => 'Method not allowed']));
            return;
        }
        
        // Получение данных запроса
        $query = request()->post['query'] ?? '';
        $limit = (int)(request()->post['limit'] ?? 10);
        
        if (strlen($query) < 2) {
            response()->addHeader('Content-Type: application/json');
            response(json_encode(['error' => 'Query too short']));
            return;
        }
        
        // Поиск товаров
        $db = registry('db');
        $sql = "
            SELECT p.product_id, pd.name, p.price, p.image 
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd 
                ON p.product_id = pd.product_id
            WHERE pd.name LIKE '%" . $db->escape($query) . "%'
            AND p.status = '1'
            LIMIT " . $limit;
        
        $result = $db->query($sql);
        
        $products = [];
        foreach ($result->rows as $product) {
            $products[] = [
                'id' => $product['product_id'],
                'name' => $product['name'],
                'price' => $this->currency->format($product['price']),
                'image' => $product['image'] ? config('config_url') . 'image/' . $product['image'] : null
            ];
        }
        
        // Отправка JSON ответа
        response()->addHeader('Content-Type: application/json');
        response(json_encode([
            'success' => true,
            'products' => $products,
            'total' => count($products)
        ]));
    }
}
```

### Создание глобальной функции для логирования

```php
// В файле system/helper/custom_helper.php

if (!function_exists('write_log')) {
    /**
     * Записывает сообщение в лог файл
     * 
     * @param string $message Сообщение для записи
     * @param string $type Тип лога (error, info, debug)
     */
    function write_log($message, $type = 'info') {
        $log = registry('log');
        $log->write('[' . strtoupper($type) . '] ' . $message);
    }
}

// Использование в любом месте приложения
write_log('User logged in: ' . $userId, 'info');
write_log('Database error: ' . $error, 'error');
write_log('Debug info: ' . print_r($data, true), 'debug');
```

### Работа с сессиями через helper-функции

```php
// Создание helper-функций для сессий
if (!function_exists('session_get')) {
    function session_get($key, $default = null) {
        $session = registry('session');
        return isset($session->data[$key]) ? $session->data[$key] : $default;
    }
}

if (!function_exists('session_set')) {
    function session_set($key, $value) {
        $session = registry('session');
        $session->data[$key] = $value;
    }
}

if (!function_exists('session_flash')) {
    function session_flash($key, $message) {
        session_set('flash_' . $key, $message);
    }
}

if (!function_exists('session_get_flash')) {
    function session_get_flash($key) {
        $message = session_get('flash_' . $key);
        session_set('flash_' . $key, null); // Удаляем после получения
        return $message;
    }
}

// Использование
session_flash('success', 'Товар успешно сохранен');
$message = session_get_flash('success');
```

## Модификации OCMOD

Модуль вносит следующие изменения в ядро OpenCart:

### 1. Расширение Registry класса
- Добавляет статический экземпляр для глобального доступа
- Методы `getInstance()` и `setInstance()`
- Автоматическое обновление статического экземпляра

### 2. Расширение Twig шаблонизатора
- Добавляет функции: `__()`, `route()`, `do_action()`, `controller()`
- Интеграция с системой хуков GDT Framework

### 3. Инициализация в Startup
- Автоматическая загрузка GDT Framework
- Обработка ошибок инициализации
- Загрузка хуков из директории `admin/controller/hook/`

## Интеграция с другими модулями GDT

### Использование с GDT HTTP

```php
use GbitStudio\GDT\Http\HttpTrait;

class ControllerCustomModule extends Controller {
    use HttpTrait;
    
    public function __construct($registry) {
        parent::__construct($registry);
        $this->init(); // Инициализация HTTP компонентов
    }
    
    public function save() {
        // Использование helper-функций вместе с HTTP модулем
        $request = registry('gdt_request');
        $response = registry('gdt_response');
        
        $data = $request->all();
        
        if ($this->validate($data)) {
            // Сохранение...
            $response->success('Данные сохранены');
        } else {
            $response->error('Ошибка валидации');
        }
    }
}
```

### Использование с GDT Validator

```php
use GbitStudio\GDT\Validator;

function validateProductData($data) {
    $validator = Validator::make($data, [
        'name' => 'required|min:2|max:255',
        'price' => 'required|numeric|min:0'
    ]);
    
    if ($validator->fails()) {
        // Использование helper-функций для логирования
        write_log('Validation failed: ' . json_encode($validator->errors()), 'error');
        return false;
    }
    
    return true;
}
```

## Структура модуля

```
ocm_gdt_helper/
├── README.md                                      # Этот файл
└── upload/
    ├── system/
    │   ├── gdt_helper_v1.ocmod.xml               # OCMOD модификации
    │   ├── helper/
    │   │   └── gdt_helper.php                    # Глобальные функции
    │   └── library/
    │       └── gbitstudio/
    │           └── gdt/
    │               ├── gdt.php                   # Основной класс GDT
    │               └── LICENSE                   # Лицензия
```

## Отладка и диагностика

### Проверка корректной установки

```php
// Проверка доступности helper-функций
if (function_exists('registry')) {
    echo "GDT Helper установлен корректно";
} else {
    echo "GDT Helper не установлен";
}

// Проверка работы registry
try {
    $db = registry('db');
    echo "Доступ к БД: OK";
} catch (Exception $e) {
    echo "Ошибка доступа к БД: " . $e->getMessage();
}
```

### Логирование ошибок

```php
// В случае ошибок GDT Framework
if (function_exists('write_log')) {
    write_log('GDT Framework error: ' . $error, 'error');
}
```

## Лучшие практики

1. **Проверяйте существование функций** перед использованием
2. **Используйте try-catch** для обработки исключений
3. **Логируйте ошибки** для отладки
4. **Не перегружайте глобальное пространство** лишними функциями
5. **Документируйте пользовательские helper-функции**
6. **Тестируйте совместимость** с другими расширениями

## Безопасность

- Все данные должны быть валидированы перед использованием
- Используйте подготовленные запросы для БД
- Проверяйте права доступа в критических функциях
- Логируйте подозрительную активность

## Устранение неполадок

### OCMOD не применяется
1. Проверьте права доступа к файлам
2. Обновите кэш модификаций: Extensions → Modifications → Refresh
3. Проверьте логи ошибок в System → Maintenance → Error Logs

### Helper-функции недоступны
1. Убедитесь, что OCMOD установлен и активен
2. Проверьте наличие файла `system/helper/gdt_helper.php`
3. Очистите кэш OpenCart

### Ошибки инициализации
1. Проверьте логи PHP на наличие синтаксических ошибок
2. Убедитесь в совместимости версий PHP и OpenCart
3. Проверьте порядок загрузки модулей

## Поддержка

Этот модуль является основой GDT Framework для OpenCart. Для получения поддержки или сообщения об ошибках обратитесь к документации основного фреймворка.

## Лицензия

Модуль распространяется в соответствии с лицензией основного GDT Framework.
