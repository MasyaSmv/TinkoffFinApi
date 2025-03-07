<?php

namespace TinkoffFinApi\Client;

use Metaseller\TinkoffInvestApi2\TinkoffClientsFactory;
use TinkoffFinApi\Contracts\ClientContract;
use TinkoffFinApi\Exceptions\TinkoffTokenException;
use TinkoffFinApi\Resources\AccountsResource;
use TinkoffFinApi\Resources\OperationsResource;

class TinkoffFinApiClient implements ClientContract
{
    protected string $token;
    protected ?object $tinkoffClient = null; // Metaseller\TinkoffInvestApi2\Client

    public function __construct(string $token)
    {
        if (empty($token)) {
            throw new TinkoffTokenException('Empty Tinkoff token');
        }
        $this->token = $token;
        $this->tinkoffClient = TinkoffClientsFactory::create($token);
    }

    /**
     * Пример геттер – возвращаем TinkoffClientsFactory для внутренней работы ресурсов.
     */
    public function getTinkoffClient(): object
    {
        return $this->tinkoffClient;
    }

    /**
     * Ресурс: Счета
     */
    public function accounts(): AccountsResource
    {
        return new AccountsResource($this);
    }

    /**
     * Ресурс: Операции
     */
    public function operations(): OperationsResource
    {
        return new OperationsResource($this);
    }

    // ... Другие ресурсы (orders(), portfolio() и т.д.)
}
