<?php

namespace TinkoffFinApi\Resources;

use TinkoffFinApi\Client\TinkoffFinApiClient;
use TinkoffFinApi\Contracts\ResourceContract;
use TinkoffFinApi\Exceptions\TinkoffApiException;

abstract class AbstractResource implements ResourceContract
{
    protected TinkoffFinApiClient $client;

    public function __construct(TinkoffFinApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Пример метода-обёртки для gRPC-вызова
     */
    protected function callApi(callable $func, ...$args)
    {
        try {
            return $func(...$args);
        } catch (\Exception $e) {
            throw new TinkoffApiException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
