<?php

namespace TinkoffFinApi\Models;

use Tinkoff\Invest\V1\PortfolioResponse;
use TinkoffFinApi\Client\TinkoffFinApiClient;

class Portfolio extends AbstractModel
{
    /** @var string|null Общая стоимость акций в портфеле */
    public ?string $total_amount_shares = null;

    /** @var string|null Общая стоимость облигаций в портфеле */
    public ?string $total_amount_bonds = null;

    /** @var string|null Общая стоимость фондов в портфеле */
    public ?string $total_amount_etf = null;

    /** @var string|null Общая стоимость валют в портфеле */
    public ?string $total_amount_currencies = null;

    /** @var string|null Общая стоимость фьючерсов в портфеле */
    public ?string $total_amount_futures = null;

    /** @var string|null Текущая относительная доходность портфеля в % */
    public ?string $expected_yield = null;

    /** @var string Идентификатор счёта пользователя */
    public string $account_id = '';

    /** @var string|null Общая стоимость опционов в портфеле */
    public ?string $total_amount_options = null;

    /** @var string|null Общая стоимость структурных нот в портфеле */
    public ?string $total_amount_sp = null;

    /** @var string|null Общая стоимость портфеля */
    public ?string $total_amount_portfolio = null;

    /** @var string|null Рассчитанная доходность портфеля за день в рублях */
    public ?string $daily_yield = null;

    /** @var string|null Относительная доходность в день в % */
    public ?string $daily_yield_relative = null;

    /** @var PortfolioPosition[] Список позиций портфеля */
    public array $positions;

    /** @var mixed Массив виртуальных позиций портфеля */
    public $virtual_positions;

    /**
     * @param ChildOperationItem $trade
     * @param TinkoffFinApiClient $client
     */
    public function __construct(PortfolioResponse $portfolio, TinkoffFinApiClient $client)
    {
        parent::__construct($client);
        
        $this->total_amount_shares = $this->convertToFloat($portfolio->getTotalAmountShares());
        $this->total_amount_bonds = $this->convertToFloat($portfolio->getTotalAmountBonds());
        $this->total_amount_etf = $this->convertToFloat($portfolio->getTotalAmountEtf());
        $this->total_amount_currencies = $this->convertToFloat($portfolio->getTotalAmountCurrencies());
        $this->total_amount_futures = $this->convertToFloat($portfolio->getTotalAmountFutures());
        $this->expected_yield = $this->convertToFloat($portfolio->getExpectedYield());
        $this->account_id = $portfolio->getAccountId();
        $this->total_amount_options = $this->convertToFloat($portfolio->getTotalAmountOptions());
        $this->total_amount_sp = $this->convertToFloat($portfolio->getTotalAmountSp());
        $this->total_amount_portfolio = $this->convertToFloat($portfolio->getTotalAmountPortfolio());
        $this->daily_yield = $this->convertToFloat($portfolio->getDailyYield());
        $this->daily_yield_relative = $this->convertToFloat($portfolio->getDailyYieldRelative());
//        $this->virtual_positions = $this->convertToFloat($portfolio->getVirtualPositions());

        foreach ($portfolio->getPositions() as $position) {
//            dump($position);
            $this->positions[] = new PortfolioPosition($position, $client);
        }
    }
}