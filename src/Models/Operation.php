<?php

namespace TinkoffFinApi\Models;

use Tinkoff\Invest\V1\Operation as GrpcOperation;
use Carbon\Carbon;
use TinkoffFinApi\Client\TinkoffFinApiClient;

class Operation extends AbstractModel
{
    public string $id;
    public float  $payment;

    public function __construct(GrpcOperation $op, TinkoffFinApiClient $client)
    {
        parent::__construct($client);
        
        $this->id = $op->getId();
        $this->payment     = $op->getPayment()->getUnits()
            + $op->getPayment()->getNano() / 10**9;
    }
}
