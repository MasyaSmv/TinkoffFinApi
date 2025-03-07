<?php

namespace TinkoffFinApi\Models;

use Tinkoff\Invest\V1\ChildOperationItem;
use Tinkoff\Invest\V1\OperationItemTrade;
use TinkoffFinApi\Client\TinkoffFinApiClient;

class OperationChild extends AbstractModel
{
    /** @var string Уникальный идентификатор инструмента */
    public string $instrument_uid = '';

    /** @var string Сумма операции */
    public string $payment = '';

    /**
     * @param ChildOperationItem $trade
     * @param TinkoffFinApiClient $client
     */
    public function __construct(ChildOperationItem $trade, TinkoffFinApiClient $client)
    {
        parent::__construct($client);

        $this->instrument_uid = $trade->getInstrumentUid();
        $this->payment = $this->convertToFloat($trade->getPayment());
    }
}