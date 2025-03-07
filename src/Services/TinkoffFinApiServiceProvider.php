<?php

namespace TinkoffFinApi\Services;

use Illuminate\Support\ServiceProvider;
use TinkoffFinApi\Client\TinkoffFinApiClient;

/**
 * Регистрирует всё, что нужно для работы библиотеки в Laravel:
 *  - Синглтон для клиента (при желании)
 *  - Публикация конфигов (если есть)
 *  - Подключение роутов (если есть)
 *  - Любую другую инициализацию
 */
class TinkoffFinApiServiceProvider extends ServiceProvider
{
    /**
     * Регистрация контейнерных биндов и синглтонов
     */
    public function register()
    {
        // Например, можно создать «ленивый» синглтон-клиент.
        // Здесь предполагается, что вы где-то в Laravel храните токен,
        // например, в config('services.tinkoff.token').
        $this->app->singleton(TinkoffFinApiClient::class, function ($app) {
            $token = config('services.tinkoff.token');
            return new TinkoffFinApiClient($token);
        });
    }

    /**
     * Запуск действий после регистрации всех сервис-провайдеров
     */
    public function boot()
    {
        // Если нужно опубликовать конфигурационные файлы:
        // $this->publishes([
        //     __DIR__ . '/../config/tinkoff.php' => config_path('tinkoff.php'),
        // ], 'tinkoff-config');

        // Если нужно регистрировать роуты:
        // $this->loadRoutesFrom(__DIR__.'/../routes/tinkoff.php');

        // Или, например, если нужно слушать события Laravel.
    }
}
