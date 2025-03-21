<?php

namespace TinkoffFinApi\Client;

use Metaseller\TinkoffInvestApi2\TinkoffClientsFactory;
use Tinkoff\Invest\V1\GetAccountsRequest;
use TinkoffFinApi\Contracts\ClientContract;
use TinkoffFinApi\Exceptions\TinkoffApiException;
use TinkoffFinApi\Exceptions\TinkoffTokenException;
use TinkoffFinApi\Resources\AccountsResource;
use TinkoffFinApi\Resources\OperationsResource;
use Exception;
use TinkoffFinApi\Resources\PortfoliosResource;

class TinkoffFinApiClient implements ClientContract
{
    protected string $token;
    protected TinkoffClientsFactory $client;

    public function __construct(string $token)
    {
        if (empty($token)) {
            throw new TinkoffTokenException('Передан пустой токен');
        }

        $this->token = $token;
        $this->client = TinkoffClientsFactory::create($token);
    }

    /**
     * Возвращает класс клиента соединение с Тинькофф АПИ
     *
     * @return TinkoffClientsFactory
     */
    public function getClient(): TinkoffClientsFactory
    {
        return $this->client;
    }

    /**
     * Возвращает все счета полученные от Тинькофф Апи
     *
     * @return AccountsResource
     */
    public function getAccounts(): AccountsResource
    {
        return new AccountsResource($this);
    }

    /**
     * Возвращает все операции полученные от Тинькофф Апи
     *
     * @return OperationsResource
     */
    public function getOperations(): OperationsResource
    {
        return new OperationsResource($this);
    }

    /**
     * Возвращает все операции полученные от Тинькофф Апи
     *
     * @return PortfoliosResource
     */
    public function getPortfolios(): PortfoliosResource
    {
        return new PortfoliosResource($this);
    }

    /**
     * Метод проверки валидности токена.
     * Делает проверку, отправляя тестовый запрос на получение счетов
     * При валидном токен возвращается true.
     * При ошибке авторизации (неверный токен) метод возвращает false.
     * При любой другой ошибке выбрасывается TinkoffApiException.
     *
     * @return bool
     */
    public function isTokenValid(): bool
    {
        try {
            $request = new GetAccountsRequest();
            [$response, $status] = $this->client
                ->usersServiceClient
                ->GetAccounts($request)
                ->wait();

            return $this->analyzeTokenStatus($status);
        } catch (Exception $e) {
            if ($this->isUnauthorizedError($e->getMessage())) {
                return false;
            }
            throw new TinkoffApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Проводит анализ ответа на проверку токена
     *
     * @param $status
     *
     * @return bool
     */
    private function analyzeTokenStatus($status): bool
    {
        // Если код статуса 0, значит запрос прошёл успешно -> токен валиден
        if ($status->code === 0) {
            return true;
        }

        // Если пришла ошибка — проверяем, не «unauthorized» ли она
        if (isset($status->metadata['message'][0])) {
            $message = $status->metadata['message'][0];
            if ($this->isUnauthorizedError($message)) {
                return false; // невалидный токен
            }
        }

        // Иначе — какая-то иная ошибка
        throw new TinkoffApiException("Проверьте токен. Получен код: {$status->code}");
    }

    /**
     * Проверяет, не содержит ли сообщение об ошибке указания на неверный токен/авторизацию.
     *
     * @param string $message
     *
     * @return bool
     */
    private function isUnauthorizedError(string $message): bool
    {
        $msg = strtolower($message);

        return str_contains($msg, 'unauthorized')
            || str_contains($msg, 'authentication token is missing or invalid');
    }
}
