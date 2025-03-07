<?php

namespace TinkoffFinApi\Models;

use Carbon\Carbon;
use Tinkoff\Invest\V1\OperationItemTrade;
use TinkoffFinApi\Client\TinkoffFinApiClient;

class OperationTrade extends AbstractModel
{
    /** @var string Номер сделки */
    public string $num = '';

    /** @var Carbon|null Дата сделки */
    public ?Carbon $date = null;

    /** @var int Количество в единицах */
    public int $quantity = 0;

    /** @var string|null Количество в единицах */
    public ?string $yield_relative = null;

    /** @var string|null Доходность */
    public ?string $yield = null;

    /** @var string|null Цена сделки */
    public ?string $price = null;

    /**
     * @param OperationItemTrade $trade
     * @param TinkoffFinApiClient $client
     */
    public function __construct(OperationItemTrade $trade, TinkoffFinApiClient $client)
    {
        parent::__construct($client);
        
        $this->num = $trade->getNum();
        $this->date = $this->makeCarbon($trade->getDate());
        $this->quantity = $trade->getQuantity();

        $this->yield_relative = $this->convertToFloat($trade->getYieldRelative());
        $this->price = $this->convertToFloat($trade->getPrice());
        $this->yield = $this->convertToFloat($trade->getYield());
    }
}