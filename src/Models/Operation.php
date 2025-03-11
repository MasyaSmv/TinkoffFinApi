<?php

namespace TinkoffFinApi\Models;

use Carbon\Carbon;
use Google\Protobuf\Internal\RepeatedField;
use Tinkoff\Invest\V1\OperationItem as GrpcOperation;
use Tinkoff\Invest\V1\OperationItemTrades;
use TinkoffFinApi\Client\TinkoffFinApiClient;

class Operation extends AbstractModel
{
    /** @var string Курсор для пагинации */
    public string $cursor = '';

    /** @var string Идентификатор родительской операции */
    public string $broker_account_id = '';

    /** @var string ID операции (может меняться) */
    public string $id = '';

    /** @var string ID родительской операции */
    public string $parent_operation_id = '';

    /** @var string Название операции */
    public string $name = '';

    /** @var null|Carbon Дата поручения  */
    public ?Carbon $date = null;

    /** @var string Описание операции */
    public string $description = '';

    /** @var int Статус поручения */
    public int $state = 0;

    /** @var int Тип операции */
    public int $type = 0;

    /** @var int Статус операции */
    public int $status = 0;

    /** @var string UID инструмента */
    public string $instrument_uid = '';

    /** @var string FIGI инструмента */
    public string $figi = '';

    /** @var string Тип инструмента */
    public string $instrument_type = '';

    /** @var int Вид инструмента */
    public int $instrument_kind = 0;

    /** @var string position_uid-идентификатора инструмента */
    public string $position_uid = '';

    /** @var int Количество инструмента */
    public int $quantity = 0;

    /** @var int Неисполненный остаток по сделке  */
    public int $quantity_rest = 0;

    /** @var int Исполненный остаток */
    public int $quantity_done = 0;

    /** @var string|null Цена за инструмент */
    public ?string $price = null;

    /** @var string|null Общая сумма операции */
    public ?string $payment = null;

    /** @var string|null Комиссия операции */
    public ?string $commission = null;

    /** @var string|null Доходность операции */
    public ?string $yield = null;

    /** @var string|null Накопленный купонный доход (НКД) */
    public ?string $accrued_int = null;

    /** @var string|null Относительная доходность */
    public ?string $yield_relative = null;

    /** @var Carbon|null Дата отмены операции */
    public ?Carbon $cancel_date_time = null;

    /** @var string Причина отмены операции */
    public string $cancel_reason = '';

    /** @var array|null Массив сделок  */
    public ?array $trades_info = null;

    /** @var string Идентификатор актива  */
    public string $asset_uid = '';

    /** @var OperationChild[] Дочерние операции */
    public array $child_operations;

    /**
     * @param GrpcOperation $op
     * @param TinkoffFinApiClient $client
     */
    public function __construct(GrpcOperation $op, TinkoffFinApiClient $client)
    {
        parent::__construct($client);

        $this->cursor = $op->getCursor();
        $this->broker_account_id = $op->getBrokerAccountId();
        $this->id = $op->getId();
        $this->parent_operation_id = $op->getParentOperationId();
        $this->name = $op->getName();
        $this->date = $this->makeCarbon($op->getDate());
        $this->type = $op->getType();
        $this->description = $op->getDescription();
        $this->state = $op->getState();
        $this->status = $op->getState();
        $this->instrument_uid = $op->getInstrumentUid();
        $this->figi = $op->getFigi();
        $this->instrument_type = $op->getInstrumentType();
        $this->instrument_kind = $op->getInstrumentKind();
        $this->position_uid = $op->getPositionUid();
        $this->payment = $this->convertToFloat($op->getPayment());
        $this->price = $this->convertToFloat($op->getPrice());
        $this->commission = $this->convertToFloat($op->getCommission());
        $this->yield = $this->convertToFloat($op->getYield());
        $this->yield_relative = $this->convertToFloat($op->getYieldRelative());
        $this->accrued_int = $this->convertToFloat($op->getAccruedInt());
        $this->quantity = $op->getQuantity();
        $this->quantity_rest = $op->getQuantityRest();
        $this->quantity_done = $op->getQuantityDone();
        $this->cancel_date_time = $this->makeCarbon($op->getCancelDateTime());
        $this->cancel_reason = $op->getCancelReason();
        $this->trades_info = $this->makeTrades($op->getTradesInfo());
        $this->asset_uid = $op->getAssetUid();
        $this->child_operations = $this->makeChildOperations($op->getChildOperations());
    }

    /**
     * @param OperationItemTrades|null $grpcTrades
     *
     * @return array
     */
    private function makeTrades(?OperationItemTrades $grpcTrades): array
    {
        $trades = $grpcTrades?->getTrades();

        // Если $trades является Traversable, преобразуем его в массив, иначе используем пустой массив
        $tradesArray = $trades instanceof \Traversable
            ? iterator_to_array($trades)
            : [];

        return array_map(
            fn($trade) => new OperationTrade($trade, $this->client),
            $tradesArray
        );
    }

    /**
     * Преобразует объект RepeatedField с дочерними операциями в массив моделей ChildOperation.
     *
     * @param RepeatedField $grpcChildOperations Объект RepeatedField
     * @return OperationChild[] Массив моделей дочерних операций
     */
    private function makeChildOperations(RepeatedField $grpcChildOperations): array
    {
        // Преобразуем RepeatedField в массив
        $childOpsArray = iterator_to_array($grpcChildOperations);

        return array_map(
            fn($childOp) => new OperationChild($childOp, $this->client),
            $childOpsArray
        );
    }
}
