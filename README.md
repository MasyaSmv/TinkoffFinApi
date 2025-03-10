# TinkoffFinApi

TinkoffFinApi — это PHP-библиотека, упрощающая работу с Tinkoff API для различных FIN-проектов. Она содержит готовые
классы и методы для удобного доступа к данным о счетах, операциях и другим ресурсам, а также обеспечивает быструю и
легкую интеграцию с фреймворком Laravel.

---

## Содержание

- [Особенности](#особенности)
- [Установка](#установка)
- [Быстрый-start](#быстрый-старт)
    - [Настройка клиента](#настройка-клиента)
    - [Работа с ресурсами](#работа-с-ресурсами)
        - [Получение списка счетов](#получение-списка-счетов)
        - [Получение конкретного счета](#получение-конкретного-счета)
        - [Работа с операциями](#работа-с-операциями)
            - [Получение всех операций](#получение-всех-операций)
            - [Получение операции по ID](#получение-операции-по-id)
            - [Получение операций за период](#получение-операций-за-период)
            - [Получение операций через ресурс Account](#получение-операций-через-ресурс-account)
        - [Работа с портфелями](#работа-с-портфелями)
            - [Получение всех портфелей](#получение-всех-портфелей)
            - [Получение портфеля по ID счета](#получение-портфеля-по-id-счета)
- [Обработка ошибок](#обработка-ошибок)
- [Интеграция с Laravel](#интеграция-с-laravel)
- [Структура проекта](#структура-проекта)
- [Тестирование](#тестирование)
- [Вклад в развитие](#вклад-в-развитие)
- [Лицензия](#лицензия)

---

## Особенности

- **Управление API-токеном и соединением.** Библиотека предоставляет клиентский класс для хранения токена и настройки
  всех API-запросов.
- **Работа с ресурсами.** Специализированные классы (Resources) облегчают доступ к сущностям, таким как счета (Accounts)
  и операции (Operations).
- **Чёткие DTO-модели.** Объектные модели упрощают чтение и работу с данными, обеспечивая лучшую структуру.
- **Кастомные исключения.** Позволяют грамотно обрабатывать ошибки (например, при неверном или истекшем токене).
- **Интерфейсы.** Обеспечивают гибкую архитектуру и дают возможность при необходимости легко расширять функционал.
- **Интеграция с Laravel.** Сервис-провайдер для упрощения регистрации и использования библиотеки в вашем
  Laravel-приложении.
- **Тестирование.** Набор юнит- и функциональных тестов для проверки стабильности и корректности работы.

---

## Установка

Установите библиотеку через [Composer](https://getcomposer.org/):

```bash
composer require fin/tinkoff-api
```

> **Примечание:** Убедитесь, что в вашем проекте уже установлен Composer. Если нет, перейдите
> по [ссылке](https://getcomposer.org/) и следуйте официальной инструкции.

---

## Быстрый старт

### Настройка клиента

Подключите автозагрузчик Composer и создайте экземпляр клиента, передав в него API-токен:

```php
<?php
require 'vendor/autoload.php';

use TinkoffFinApi\Client\TinkoffFinApiClient;

// Ваш API-токен для аутентификации
$apiToken = 'YOUR_API_TOKEN_HERE';

// Создаем клиент для работы с Tinkoff API
$client = new TinkoffFinApiClient($apiToken);
```

В данном примере:

- **$apiToken** — это строка, содержащая ваш реальный токен доступа от Tinkoff.
- **TinkoffFinApiClient** отвечает за конфигурацию и управление всеми запросами к API.

### Работа с ресурсами

#### [Получение списка счетов](#получение-списка-счетов)

```php
<?php
use TinkoffFinApi\Client\TinkoffFinApiClient;
use TinkoffFinApi\Exceptions\TinkoffApiException;

$apiToken = 'YOUR_API_TOKEN_HERE';

try {
    $client = new TinkoffFinApiClient($apiToken);

    // Получаем ресурс для управления счетами
    $accounts = $client->getAccounts();

    // Перебираем и выводим все доступные счета
    foreach ($accounts->all() as $account) {
        echo 'Account ID: ' . $account->id . PHP_EOL;
    }
} catch (TinkoffApiException $e) {
    // В случае ошибки выводим сообщение
    echo 'Ошибка API: ' . $e->getMessage();
}
```

#### [Получение конкретного счета](#получение-конкретного-счета)

```php
<?php
use TinkoffFinApi\Client\TinkoffFinApiClient;use TinkoffFinApi\Exceptions\TinkoffApiException;

$apiToken = 'YOUR_API_TOKEN_HERE';
$accountId = 'YOUR_ACCOUNT_ID';

try {
    $client = new TinkoffFinApiClient($apiToken);

    // Получаем ресурс для управления счетами
    $accountResource = $client->getAccounts();

    // Ищем конкретный счет
    $account = $accountResource->findById($accountId);
    if ($account) {
        echo 'Найден счет с ID: ' . $account->id . PHP_EOL;
    } else {
        echo 'Счет с таким ID не найден.' . PHP_EOL;
    }
} catch (TinkoffApiException $e) {
    echo 'Ошибка API: ' . $e->getMessage();
}
```

### [Работа с операциями](#работа-с-операциями)

##### [Получение всех операций](#получение-всех-операций)

```php
<?php
use Carbon\Carbon;
use TinkoffFinApi\Client\TinkoffFinApiClient;
use TinkoffFinApi\Exceptions\TinkoffApiException;

$apiToken = 'YOUR_API_TOKEN_HERE';

try {
    $client = new TinkoffFinApiClient($apiToken);

    // Получаем ресурс операций
    $operations = $client->getOperations();

    // Получение всех операций
    foreach ($operations->all() as $operation) {
        echo 'Operation ID: ' . $operation->id . ' | Тип: ' . $operation->type . PHP_EOL;
    }
} catch (TinkoffApiException $e) {
    echo 'Ошибка при работе с операциями: ' . $e->getMessage();
}
```

##### [Получение операции по ID](#получение-операции-по-id)

```php
<?php
use TinkoffFinApi\Client\TinkoffFinApiClient;
use TinkoffFinApi\Exceptions\TinkoffApiException;

$apiToken = 'YOUR_API_TOKEN_HERE';
$operationId = 'YOUR_OPERATION_ID';

try {
    $client = new TinkoffFinApiClient($apiToken);
    $operations = $client->getOperations();

    // Получаем операцию по ID
    $singleOperation = $operations->findById($operationId);
    if ($singleOperation) {
        echo 'Найдена операция: ' . $singleOperation->id . PHP_EOL;
    } else {
        echo 'Операция с таким ID не найдена.' . PHP_EOL;
    }
} catch (TinkoffApiException $e) {
    echo 'Ошибка при работе с операциями: ' . $e->getMessage();
}
```

##### [Получение операций за период](#получение-операций-за-период)

```php
<?php
use Carbon\Carbon;
use TinkoffFinApi\Client\TinkoffFinApiClient;
use TinkoffFinApi\Exceptions\TinkoffApiException;

$apiToken = 'YOUR_API_TOKEN_HERE';
$accountId = 'YOUR_ACCOUNT_ID';

try {
    $client = new TinkoffFinApiClient($apiToken);
    $operations = $client->getOperations();

    // Задаем период
    $from = Carbon::now()->subYear();
    $to = Carbon::now();

    // Получаем операции за указанный промежуток времени
    $byDates = $operations->getByDateRange($accountId, $from, $to);
    foreach ($byDates as $operation) {
        echo 'Operation ID: ' . $operation->id . ' | Тип: ' . $operation->type . PHP_EOL;
    }
} catch (TinkoffApiException $e) {
    echo 'Ошибка при работе с операциями: ' . $e->getMessage();
}
```

##### [Получение операций через ресурс Account](#получение-операций-через-ресурс-account)

```php
<?php
use Carbon\Carbon;
use TinkoffFinApi\Client\TinkoffFinApiClient;
use TinkoffFinApi\Exceptions\TinkoffApiException;

$apiToken = 'YOUR_API_TOKEN_HERE';
$operationId = 'YOUR_OPERATION_ID';

try {
    $client = new TinkoffFinApiClient($apiToken);

    // Получаем аккаунты
    $accounts = $client->getAccounts();

    // Срез дат
    $from = Carbon::now()->subYear();
    $to = Carbon::now();

    foreach ($accounts->all() as $account) {
        // a) Все операции по счету
        $accountOperations = $account->getAllOperations();
        // b) Операция по конкретному ID
        $accountOperation = $account->getFindByIdOperation($operationId);
        // c) Операции за период по счету
        $byDates = $account->getByDateRangeOperations($from, $to);

        // Пример вывода
        foreach ($accountOperations as $op) {
            echo '[Account Operations] ID: ' . $op->id . ' | Тип: ' . $op->type . PHP_EOL;
        }
    }
} catch (TinkoffApiException $e) {
    echo 'Ошибка при работе с операциями: ' . $e->getMessage();
}
```

### [Работа с портфелями](#работа-с-портфелями)

#### [Получение всех портфелей](#получение-всех-портфелей)

```php
<?php
use TinkoffFinApi\Client\TinkoffFinApiClient;
use TinkoffFinApi\Exceptions\TinkoffApiException;

$apiToken = 'YOUR_API_TOKEN_HERE';

try {
    $client = new TinkoffFinApiClient($apiToken);
    $portfolios = $client->getPortfolios();

    // Получение портфелей всех счетов
    foreach ($portfolios->all() as $portfolio) {
        echo 'Account ID: ' . $portfolio->account_id;
        
        //Перебор всех бумаг в портфеле счета
        foreach ($portfolio->positions as $position) {
            echo 'FIGI: ' . $position->figi . ' | Тип: ' . $position->instrument_type . PHP_EOL;
            
        }
    }
} catch (TinkoffApiException $e) {
    echo 'Ошибка при работе с портфелями: ' . $e->getMessage();
}
```

#### [Получение портфеля по ID счета](#получение-портфеля-по-id-счета)

```php
use TinkoffFinApi\Client\TinkoffFinApiClient;

$apiToken = 'YOUR_API_TOKEN_HERE';
$accountId = 'YOUR_ACCOUNT_ID';

try {
    $client = new TinkoffFinApiClient($apiToken);
    //Получение счета
    $account = $client->getAccounts()->findById($accountId);
    // Получение порфтеля данного счета
    $portfolio = $account->getPortfolio();
    
    if ($portfolio) {
        echo 'Account ID: ' . $portfolio->account_id;
        //Перебор всех бумаг в портфеле счета
        foreach ($portfolio->positions as $position) {
            echo 'FIGI: ' . $position->figi . ' | Тип: ' . $position->instrument_type . PHP_EOL;
        }
    }
} catch (TinkoffApiException $e) {
    echo 'Ошибка при работе с портфелями: ' . $e->getMessage();
}
```

---

## Обработка ошибок

Библиотека предоставляет следующие исключения для более детальной диагностики:

- **TinkoffApiException:** общее базовое исключение для всех ошибок, связанных с запросами к API.
- **TinkoffTokenException:** выбрасывается при неверном или просроченном токене.

Используйте `try-catch` блоки для предотвращения сбоев в работе приложения:

```php
try {
    // Код обращения к API
} catch (\TinkoffFinApi\Exceptions\TinkoffTokenException $e) {
    // Обрабатываем ошибку неверного/просроченного токена
} catch (\TinkoffFinApi\Exceptions\TinkoffApiException $e) {
    // Общая обработка ошибок API
}
```

---

## Интеграция с Laravel

Для интеграции в Laravel:

1. Установите пакет с помощью Composer.
2. Добавьте сервис-провайдер в `config/app.php`:

```php
'providers' => [
    // ...
    TinkoffFinApi\TinkoffFinApiServiceProvider::class,
];
```

3. При необходимости опубликуйте и отредактируйте конфигурационный файл (если библиотека предоставляет его). После этого
   все ресурсы библиотеки доступны через IoC-контейнер Laravel.

---

## Структура проекта

```text
TinkoffFinApi/
├── composer.json
├── README.md
├── src
│   ├── Client
│   │   └── TinkoffFinApiClient.php         // Клиент для работы с API
│   ├── Contracts
│   │   ├── ClientContract.php             // Интерфейс клиента (опционально)
│   │   └── ResourceContract.php           // Интерфейс для ресурсов (Accounts, Operations и др.)
│   ├── Exceptions
│   │   ├── TinkoffApiException.php        // Основное исключение
│   │   ├── TinkoffTokenException.php      // Исключение для неверного/просроченного токена
│   │   └── ...
│   ├── Models
│   │   ├── Account.php                    // DTO-модель для счета
│   │   ├── Operation.php                  // DTO-модель для операции
│   │   └── ...
│   ├── Resources
│   │   ├── AbstractResource.php           // Базовый класс для ресурсов
│   │   ├── AccountsResource.php           // Методы для управления счетами
│   │   ├── OperationsResource.php         // Методы для управления операциями
│   │   └── ...
│   └── TinkoffFinApiServiceProvider.php   // Сервис-провайдер для Laravel
└── tests
    ├── Feature
    │   ├── AccountsTest.php               // Функциональные тесты счетов
    │   ├── OperationsTest.php             // Функциональные тесты операций
    │   └── ...
    └── Unit
        ├── ClientTest.php                // Юнит-тесты клиента
        ├── ModelsTest.php                // Юнит-тесты моделей
        └── ExceptionsTest.php            // Тесты для исключений
```

---

## Тестирование

Для запуска тестов выполните команду (убедитесь, что [PHPUnit](https://packagist.org/packages/phpunit/phpunit)
установлен):

```bash
./vendor/bin/phpunit
```

Или используйте скрипты из `composer.json`, например:

```bash
composer test-tinkoff-api
```

Убедитесь, что ваше окружение настроено корректно, чтобы все тесты проходили без ошибок.

---

## Лицензия

Данная библиотека распространяется на условиях лицензии MIT. Полный текст лицензии можно найти в файле `LICENSE` в корне
проекта или по ссылке в репозитории.

