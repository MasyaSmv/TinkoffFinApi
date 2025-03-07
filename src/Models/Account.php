<?php

namespace TinkoffFinApi\Models;

use Tinkoff\Invest\V1\Account as GrpcAccount;

class Account extends AbstractModel
{
    public string $id;
    public int $status;
    public string $name;

    public function __construct(GrpcAccount $grpcAccount)
    {
        $this->id     = $grpcAccount->getId();
        $this->status = $grpcAccount->getStatus();
        $this->name   = $grpcAccount->getName();
        // ... остальные поля
    }
}
