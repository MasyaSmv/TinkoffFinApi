# TinkoffFinApi
Php library for FIN project

TinkoffFinApi/
├── composer.json
├── README.md
├── src
│   ├── Client
│   │   └── TinkoffFinApiClient.php     // Основной клиентский класс, хранит токен, настраивает соединение
│   ├── Contracts
│   │   ├── ClientContract.php          // Опциональный интерфейс для клиента
│   │   └── ResourceContract.php        // Опциональный общий интерфейс для ресурсов (Accounts, Operations и т.д.)
│   ├── Exceptions
│   │   ├── TinkoffApiException.php     // Базовое исключение библиотеки
│   │   ├── TinkoffTokenException.php   // Например, неверный/просроченный токен
│   │   └── ... (другие при необходимости)
│   ├── Models
│   │   ├── Account.php                 // DTO / Модель для аккаунта
│   │   ├── Operation.php               // DTO / Модель для операции
│   │   └── ... (другие модели при необходимости)
│   ├── Resources
│   │   ├── AbstractResource.php        // Базовый класс для всех ресурсов (общая логика)
│   │   ├── AccountsResource.php        // Методы для GetAccounts, CreateSandboxAccount и т.д.
│   │   ├── OperationsResource.php      // Методы для getOperations (и т.п.)
│   │   └── ... (другие ресурсы: PortfolioResource, OrdersResource, и т.д.)
│   └── TinkoffFinApiServiceProvider.php // Если хотим подключать в Laravel-проекты
└── tests
├── Feature
│   ├── AccountsTest.php            // Фич-тесты на работу с AccountsResource
│   ├── OperationsTest.php          // Фич-тесты на работу с OperationsResource
│   └── ...
└── Unit
├── ClientTest.php             // Юнит-тесты логики клиента
├── ModelsTest.php            // Юнит-тесты DTO-моделей
└── ExceptionsTest.php        // Тесты на кастомные исключения

