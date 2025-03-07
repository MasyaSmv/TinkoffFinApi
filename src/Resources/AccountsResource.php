<?php

namespace TinkoffFinApi\Resources;

use TinkoffFinApi\Models\Account;
use TinkoffFinApi\Exceptions\TinkoffApiException;
use Tinkoff\Invest\V1\GetAccountsRequest;

class AccountsResource extends AbstractResource
{
    /**
     * Получить список счетов
     *
     * @param array $filters Фильтр на будущее
     *
     * @return array
     */
    public function all(array $filters = []): array
    {
        $request = new GetAccountsRequest();

        [$response, $status] = $this->callApi(
            fn() => $this->client->getClient()
                ->usersServiceClient
                ->GetAccounts($request)
                ->wait()
        );

        if ($status->code !== 0) {
            throw new TinkoffApiException("Error code {$status->code}");
        }

        $accounts = [];
        foreach ($response->getAccounts() as $acc) {
            $accounts[] = new Account($acc); // DTO инициализируем
        }

        return $accounts;
    }

    /**
     * Получить счет по указанному ID
     *
     * @param $id
     *
     * @return Account|null
     */
    public function findById($id): ?Account
    {
        // Получаем все счета
        $accounts = $this->all();

        // Ищем нужный счёт
        foreach ($accounts as $account) {
            if ($account->id === (string)$id) {
                // Возвращаем найденный объект Account
                return $account;
            }
        }

        // Если ничего не нашли — возвращаем null
        return null;
    }
}
