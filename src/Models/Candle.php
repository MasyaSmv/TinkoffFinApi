<?php

namespace TinkoffFinApi\Models;

use Tinkoff\Invest\V1\HistoricCandle;
use TinkoffFinApi\Client\TinkoffFinApiClient;

class Candle extends AbstractModel
{
    /** @var string|null Цена открытия свечи */
    public ?string $open = null;

    /** @var string|null Максимальная цена свечи */
    public ?string $high = null;

    /** @var string|null Минимальная цена свечи */
    public ?string $low = null;

    /** @var string|null Цена закрытия свечи */
    public ?string $close = null;

    /** @var int Объём торгов по свече */
    public int $volume = 0;

    /** @var bool Флаг завершённости свечи */
    public bool $is_complete = false;

    /** @var \Carbon\Carbon|null Время свечи */
    public ?\Carbon\Carbon $time = null;

    public function __construct(HistoricCandle $candle, TinkoffFinApiClient $client)
    {
        parent::__construct($client);

        $this->open = $this->convertToFloat($candle->getOpen());
        $this->high = $this->convertToFloat($candle->getHigh());
        $this->low = $this->convertToFloat($candle->getLow());
        $this->close = $this->convertToFloat($candle->getClose());
        $this->volume = $candle->getVolume();
        $this->is_complete = $candle->getIsComplete();
        $this->time = $this->makeCarbon($candle->getTime());
    }
}
