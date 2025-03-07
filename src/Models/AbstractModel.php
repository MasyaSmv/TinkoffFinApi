<?php

namespace TinkoffFinApi\Models;

use Tinkoff\Invest\V1\Account as GrpcAccount;
use TinkoffFinApi\Client\TinkoffFinApiClient;

abstract class AbstractModel
{
    protected TinkoffFinApiClient $client;
    
    public function __construct(TinkoffFinApiClient $client)
    {
        $this->client = $client;
    }
}