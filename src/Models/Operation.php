<?php

namespace TinkoffFinApi\Models;

use Tinkoff\Invest\V1\Operation as GrpcOperation;
use Carbon\Carbon;

class Operation extends AbstractModel
{
    public string $operationId;
    public float  $payment;
    // ...

    public function __construct(GrpcOperation $op)
    {
        $this->operationId = $op->getId();
        $this->payment     = $op->getPayment()->getUnits()
            + $op->getPayment()->getNano() / 10**9;

        // ... и т.д.
    }
}
