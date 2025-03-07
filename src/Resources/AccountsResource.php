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
     * @return Account[]  // Или Collection<Account>
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

    public function findById($id)
    {
        // TODO: Implement findById() method.
    }
}
