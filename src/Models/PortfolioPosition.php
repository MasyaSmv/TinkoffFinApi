<?php

namespace TinkoffFinApi\Models;

use Tinkoff\Invest\V1\PortfolioPosition as GrpcPortfolioPosition;
use TinkoffFinApi\Client\TinkoffFinApiClient;

class PortfolioPosition extends AbstractModel
{
    /** @var string FIGI-идентификатор инструмента */
    public string $figi = '';

    /** @var string Тип инструмента */
    public string $instrument_type = '';

    /** @var string|null Количество инструмента в портфеле в штуках */
    public ?string $quantity = null;

    /** @var string|null Средневзвешенная цена позиции. Для пересчёта возможна задержка до одной секунды */
    public ?string $average_position_price = null;

    /** @var string|null Текущая рассчитанная доходность позиции */
    public ?string $expected_yield = null;

    /** @var string|null Текущий НКД */
    public ?string $current_nkd = null;

    /** @var string|null Deprecated Средняя цена позиции в пунктах (для фьючерсов). Для пересчёта возможна задержка до одной секунды. @deprecated */
    public ?string $average_position_price_pt = null;

    /** @var string|null Текущая цена за 1 инструмент. Чтобы получить стоимость лота, нужно умножить на лотность инструмента */
    public ?string $current_price = null;

    /** @var string|null Средняя цена позиции по методу FIFO. Для пересчёта возможна задержка до одной секунды */
    public ?string $average_position_price_fifo = null;

    /** @var string|null Deprecated Количество лотов в портфеле. @deprecated */
    public ?string $quantity_lots = null;

    /** @var bool Заблокировано на бирже */
    public bool $blocked = false;

    /** @var string|null Количество бумаг, заблокированных выставленными заявками */
    public ?string $blocked_lots = null;

    /** @var string Уникальный идентификатор позиции */
    public string $position_uid = '';

    /** @var string Уникальный идентификатор инструмента */
    public string $instrument_uid = '';

    /** @var string|null Вариационная маржа */
    public ?string $var_margin = null;

    /** @var string|null Текущая рассчитанная доходность позиции */
    public ?string $expected_yield_fifo = null;

    /** @var string|null Рассчитанная доходность портфеля за день */
    public ?string $daily_yield = null;

    /**
     * @param GrpcPortfolioPosition $position
     * @param TinkoffFinApiClient $client
     */
    public function __construct(GrpcPortfolioPosition $position, TinkoffFinApiClient $client)
    {
        parent::__construct($client);
        
        $this->figi = $position->getFigi();
        $this->instrument_type = $position->getInstrumentType();
        $this->quantity = $this->convertToFloat($position->getQuantity());
        $this->average_position_price = $this->convertToFloat($position->getAveragePositionPrice());
        $this->expected_yield = $this->convertToFloat($position->getExpectedYield());
        $this->current_nkd = $this->convertToFloat($position->getCurrentNkd());
        $this->average_position_price_pt = $this->convertToFloat($position->getAveragePositionPricePT());
        $this->current_price = $this->convertToFloat($position->getCurrentPrice());
        $this->average_position_price_fifo = $this->convertToFloat($position->getAveragePositionPriceFifo());
        $this->quantity_lots = $this->convertToFloat($position->getQuantityLots());
        $this->blocked = $position->getBlocked();
        $this->blocked_lots = $this->convertToFloat($position->getBlockedLots());
        $this->position_uid = $position->getPositionUid();
        $this->instrument_uid = $position->getInstrumentUid();
        $this->var_margin = $this->convertToFloat($position->getVarMargin());
        $this->expected_yield_fifo = $this->convertToFloat($position->getExpectedYield());
        $this->daily_yield = $this->convertToFloat($position->getDailyYield());
    }
}